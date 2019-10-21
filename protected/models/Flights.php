<?php

/**
 * This is the model class for table "flights".
 *
 * The followings are the available columns in table 'flights':
 * @property integer $flight_id
 * @property integer $airline_id
 * @property integer $departure_airport_id
 * @property string $departure_time
 * @property integer $arrival_airport_id
 * @property string $arrival_time
 *
 * The followings are the available model relations:
 * @property Airports $arrivalAirport
 * @property Airports $departureAirport
 * @property Airlines $airline
 */
class Flights extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'flights';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('flight_id, airline_id, departure_airport_id, arrival_airport_id', 'required'),
			array('flight_id, airline_id, departure_airport_id, arrival_airport_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('flight_id, airline_id, departure_airport_id, departure_time, arrival_airport_id, arrival_time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'arrivalAirport' => array(self::BELONGS_TO, 'Airports', 'arrival_airport_id'),
			'departureAirport' => array(self::BELONGS_TO, 'Airports', 'departure_airport_id'),
			'airline' => array(self::BELONGS_TO, 'Airlines', 'airline_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'flight_id' => 'Flight',
			'airline_id' => 'Airline',
			'departure_airport_id' => 'Departure Airport',
			'departure_time' => 'Departure Time',
			'arrival_airport_id' => 'Arrival Airport',
			'arrival_time' => 'Arrival Time',
		);
	}


	//get the total time of the flight this assumes a flight can not be longer than 24hours
	public function getFlightTime($returnSummable = false) {
		//will assume we turn to next day if the arrival is timed before the departure as we only keep time field
		$depart = strtotime("1970-01-01 $this->departure_time UTC");
		$arrival = strtotime("1970-01-01 $this->arrival_time UTC");
		$seconds = 0;
		if($depart > $arrival) {
			$seconds = 86400-$depart + $arrival;
		}
		else {
			$seconds = $arrival - $depart;
		}
		$seconds = ceil($seconds);
		if($returnSummable) {
			return $seconds;
		}
		$dtF = new \DateTime('@0');
		$dtT = new \DateTime("@$seconds");
		return $dtF->diff($dtT)->format("%h h %i m %s s");
	}

	public function getFlightString() {
		return  "{$this->departureAirport->IATA} @ {$this->departure_time} -> {$this->arrivalAirport->IATA} @ {$this->arrival_time} with {$this->airline->name}";
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Flights the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
