<h2>Flight Searcher (<a href="<?php echo $this->createUrl('site/crazyFlights', $_GET)?>">Crazy Flights</a>)</h2>



<?php 

$this->renderPartial('_search');
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'trips',
	'dataProvider'=>$data_provider,
	'pager'=>array(
        'header' => '',
    ),
	'template'=>"{summary}{items}{pager}",
	'columns'=>array(
		array(
			'header' => 'Airline',
			'value' => '$data->airline->name . " (". $data->airline->IATA.")"'
		),
		array(
			'header' => 'Departure',
			'value' => '$data->departureAirport->city . ",". $data->departureAirport->country. " @ ". $data->departure_time'
		),
		array(
			'header' => 'Arrival location',
			'value' => '$data->arrivalAirport->city . "," .$data->arrivalAirport->country. " @ ". $data->arrival_time'
		),
		array(
			'header' => 'Flight Time',
			'value' => '$data->FlightTime'
		),
		array(
			'header' => 'Cost',
			'value' => '$data->price'
		),
	),
)); 


$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'round_trips',
	'dataProvider'=>$data_provider_rounds,
	'pager'=>array(
        'header' => '',
    ),
	'template'=>"{summary}{items}{pager}",
	'columns'=>array(
		array(
			'header' => 'Going Flights',
			'type' => 'raw',
			'value' => '$data->getGoingString()'
		),
		array(
			'header' => 'Return Flights',
			'type' => 'raw',
			'value' => '$data->getReturnString()'
		),
		array(
			'header' => 'Going Flight Time',
			'value' => '$data->getTotalGoingFlightTime()'
		),
		array(
			'header' => 'Return Flight Time',
			'value' => '$data->getTotalReturnFlightTime()'
		),
		'Cost'
	),
)); 
if(isset($hopping_provider)) { 
	$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'hopping_round_trips',
		'dataProvider'=>$hopping_provider,
		'pager'=>array(
			'header' => '',
		),
		'template'=>"{summary}{items}{pager}",
		'columns'=>array(
			array(
				'header' => 'Going Flights',
				'type' => 'raw',
				'value' => '$data->getGoingString()'
			),
			array(
				'header' => 'Return Flights',
				'type' => 'raw',
				'value' => '$data->getReturnString()'
			),
			array(
				'header' => 'Going Flight Time',
				'value' => '$data->getTotalGoingFlightTime()'
			),
			array(
				'header' => 'Return Flight Time',
				'value' => '$data->getTotalReturnFlightTime()'
			),
			'Cost'
		),
	)); 
}

	
?>
