<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	public function actionIndex() {


		try {
			Yii::app()->db;
		}
		catch(Exception $e) {
			echo "could not connect to db.";
			die();
		}
		$this->redirect('index.php/site/RegularFlights');
	}

	//url used to get ones_ways, round_trips, double hopping round_trips
	public function actionRegularFlights()
	{
		//set limit to 1gb because 128M wasnt enough to process and save all data
		ini_set('memory_limit', '1024M');
		if(isset($_GET)) {
			$possible_airports = Airports::getAirportsFromUserInputSearch();

			$one_ways = array();
			$round_trips = array();
			$hopping_trips = array();

			foreach($possible_airports as $airport) {
				foreach($airport->outgoing_flights as $outgoing) {
					//pulled using relations and defined with outgoing_flights
					if($outgoing->arrivalAirport->city == $_GET['arrival_city']) {
						//set relation as we want it accessible later
						//instead of querying
						$outgoing->departureAirport = $airport;
						$one_ways[] = $outgoing;
					}
					else {
						foreach($outgoing->arrivalAirport->outgoing_flights as $outgoing_level2) {
							if($outgoing_level2->arrivalAirport->city == $_GET['arrival_city']) {
								$trip = new Trip;
								$trip->flights_to_go[] = $outgoing;
								$trip->flights_to_go[] = $outgoing_level2;
								$trip->final_airport = $outgoing_level2->arrivalAirport;
								$hopping_trips[] = $trip;
							}
						}
					}
				}
			}
			
			$hopping_trips_with_return = array();

			/* exhuasting too much memory might have infinite loop */
			//go through hopping trips to find hop returns
			foreach($hopping_trips as $trip) {
				$airport = $trip->final_airport;
				foreach($airport->outgoing_flights as $outgoing) {
					//pulled using relations and defined with outgoing_flights
					if($outgoing->arrivalAirport->city == $_GET['departure_city']) {
						
						$trip_clone = new Trip();
						$trip_clone->flights_to_go = $trip->flights_to_go;
						$trip_clone->flights_to_return[] = $outgoing;
						$hopping_trips_with_return[] = $trip_clone;
					}
					else {
						foreach($outgoing->arrivalAirport->outgoing_flights as $outgoing_level2) {
							if($outgoing_level2->arrivalAirport->city == $_GET['departure_city']) {
								$trip_clone = new Trip();
								$trip_clone->flights_to_go = $trip->flights_to_go;
								$trip_clone->flights_to_return[] = $outgoing;
								$trip_clone->flights_to_return[] = $outgoing_level2;
								$hopping_trips_with_return[] = $trip_clone;
							}
						}
					}
				}
				break;
			}
			
			foreach($one_ways as $one) {
				foreach($one->arrivalAirport->outgoing_flights as $return_flight) {
					if($return_flight->arrivalAirport->city == $_GET['departure_city']) {
						$trip = new Trip;
						$trip->flights_to_go[] = $one;
						$trip->flights_to_return[] = $return_flight;
						$round_trips[] = $trip;
					}
				}
			}
		}

		$data_provider = new CArrayDataProvider($one_ways, array(
			'id'=>'one_way_flights',
			'keyField' => 'flight_id',
			'sort'=>array(
				'attributes'=>array(
					 'departure_time', 
					 'price',
				),
			),
			'pagination'=>array(
				'pageSize'=>10,
			),
			'totalItemCount' => count($one_ways)
		));


		$data_provider_rounds = new CArrayDataProvider($round_trips, array(
			'id'=>'round_flights',
			'keyField' => 'fake_id',
			'sort'=>array(
				'attributes'=>array(
					'Cost' => array(
						'asc' => 'Cost ASC',
						'desc' => 'Cost DESC' 
					)
				),
			),
			'pagination'=>array(
				'pageSize'=>10,
			),
			'totalItemCount' => count($round_trips)
		));

		$hopping_provider = new CArrayDataProvider($hopping_trips_with_return, array(
			'id'=>'hopping_round_flights',
			'keyField' => 'fake_id',
			'sort'=>array(
				'attributes'=>array(
					'Cost' => array(
						'asc' => 'Cost ASC',
						'desc' => 'Cost DESC' 
					)
				),
			),
			'pagination'=>array(
				'pageSize'=>10,
			),
			'totalItemCount' => count($hopping_trips_with_return)
		));

		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index', array(
			'data_provider' => $data_provider,
			'data_provider_rounds' => $data_provider_rounds,
			'hopping_provider' => $hopping_provider
		) );
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}



	//url used to get one ways with many lay overs
	public function actionCrazyFlights() {
		ini_set('memory_limit', '1024M');
		$trips = array();		
		if(isset($_GET)) {
			$possible_airports = Airports::getAirportsFromUserInputSearch();
			//most cases it's one airport/city
			foreach($possible_airports as $airport) {
				$outgoings = array_slice($airport->outgoing_flights, -5);
				foreach($outgoings as $outgoing) {
					//setup initial airport
					$trip = new Trip();
					$trip->airports_to_go_visited[$airport->IATA] = true;
					$trips = array_merge($trips,$this->checkNextHop($outgoing, 1, $_GET['arrival_city'], $trip));
				}
			}
		}


		$trips = new CArrayDataProvider($trips, array(
			'id'=>'hopping_round_flights',
			'keyField' => 'fake_id',
			'sort'=>array(
				'attributes'=>array(
					'Cost' => array(
						'asc' => 'Cost ASC',
						'desc' => 'Cost DESC' 
					)
				),
			),
			'pagination'=>array(
				'pageSize'=>10,
			),
			'totalItemCount' => count($trips)
		));
		
		$this->render('crazyTrips',array(
			'trips' => $trips,
		));
	}

	//flights Model with what depth level to restrict max, city we are trying to get to
	private function checkNextHop($flight, $depth, $city_to_locate, $trip = null) {
		$trips = array();
		if($flight->arrivalAirport->city == $city_to_locate) {
			//to avoid cycling over and over
			if($trip->tryVisiting($flight)) {
				$trips[] = $trip;
			}
		}
		else {
			//will only go through if we havent visited it
			if($trip->tryVisiting($flight)) {
				$outgoings = array_slice($flight->arrivalAirport->outgoing_flights, -5);
				foreach($outgoings as $flight_next_level) {
					if($depth < 5) {
						$trips = array_merge($trips,$this->checkNextHop($flight_next_level, $depth+1, $city_to_locate, clone $trip));
					}
				}
			}
		}
			
		return $trips;
	}


}