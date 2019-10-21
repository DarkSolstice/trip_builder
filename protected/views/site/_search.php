<div>

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
    <div>
        <div>
            Departure Location:
        </div>
        <div>
            <?php echo CHtml::dropDownList('departure_city',isset($_GET['departure_city']) ? $_GET['departure_city'] : null,Airports::model()->getAllAirportLocations()) ?>
        </div>
    </div>
    <div>
        <div>
            Arrival Location:
        </div>
        <div>
            <?php echo CHtml::dropDownList('arrival_city',isset($_GET['arrival_city']) ? $_GET['arrival_city'] : null,Airports::model()->getAllAirportLocations()) ?>
        </div>
    </div>

    <div>
        <div>
            Prefered Airline:
        </div>
        <div>
            <?php echo CHtml::dropDownList('airline_id',isset($_GET['airline_id']) ? $_GET['airline_id'] : null,Airlines::model()->getAllAirlines()) ?>
        </div>
    </div>

    <div>
        <div>
            Allow Plane Hopping:
        </div>
        <div>
            <input type='checkbox' name="plane_hopping_enabled">
        </div>
    </div>

    <div>
        <div>
            Departure date:
        </div>
        <div>
            <input type='date' name='departure_date'>
        </div>
    </div>

	<div>
		<?php echo CHtml::submitButton("Search"); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->
