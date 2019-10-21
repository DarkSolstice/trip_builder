<?php
/*

 simple model to aggregate connected flips for possible trips
 */
class Trip extends CActiveRecord
{
    const MAX_TRIP_HOPS = 2;
    //array of flights
    public $flights_to_go = array();

    //array of flights
    public $flights_to_return = array();

    //to data sets to keep ids visited/ used to avoid cycles
    public $airports_to_go_visited = array();
    public $airports_to_return_visited = array();

    public $final_airport = null;
    public $starting_airport = null;

    public $fake_id = 0;

    public function tableName()
    {
        return 'trip';
    }

    public function rules() {
        return array(
         /* your other rules */
         array('Cost', 'safe')
        );
    }
    public function attributeLabels() {
        return array(
            /* Your other attribute labels */
            'Cost' => 'Cost'
        );
    }


    public function getGoingString() {
        $temp_array = array();
        foreach($this->flights_to_go as $go_flight) {
            $temp_array[] = $go_flight->getFlightString();
        }
        return implode('<br>',$temp_array);
    }

    public function getReturnString() {
        $temp_array = array();
        foreach($this->flights_to_return as $return_flight) {
            $temp_array[] = $return_flight->getFlightString();
        }
        return implode('<br>',$temp_array);
    }

    public function getCost() {
        $cost = 0;

        foreach($this->flights_to_go as $go_flight) {
            $cost += $go_flight->price;
        }
        foreach($this->flights_to_return as $return_flight) {
            $cost += $return_flight->price;
        }
        return $cost;
    }
    public function getTotalGoingFlightTime() {
        $total_seconds = 0;
        foreach($this->flights_to_go as $go_flight) {
            $total_seconds += $go_flight->getFlightTime(true);
        }
        $dtF = new \DateTime('@0');
		$dtT = new \DateTime("@$total_seconds");
		return $dtF->diff($dtT)->format("%d days, %h h %i m %s s");
    }
    public function getTotalReturnFlightTime() {
        $total_seconds = 0;
        foreach($this->flights_to_return as $return_flight) {
            $total_seconds += $return_flight->getFlightTime(true);
        }
        $dtF = new \DateTime('@0');
		$dtT = new \DateTime("@$total_seconds");
		return $dtF->diff($dtT)->format("%d days, %h h %i m %s s");
    }

    //retun if this air port was used during this trip
    public function tryVisiting($flight) {
        if(!isset($this->airports_to_go_visited[$flight->arrivalAirport->IATA])) {
            $this->airports_to_go_visited[$flight->arrivalAirport->IATA] = true;
            $this->flights_to_go[] = $flight;
            return true;
        }
        return false;
    }


}
