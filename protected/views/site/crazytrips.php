<h2>Crazy Flights (<a href="<?php echo $this->createUrl('site/RegularFlights', $_GET);?>">Regular Flights</a>)</h2>
<?php 

$this->renderPartial('_search');

$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'hopping_round_trips',
    'dataProvider'=>$trips,
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