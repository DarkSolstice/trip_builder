<?php

/**
 * This is the model class for table "airports".
 *
 * The followings are the available columns in table 'airports':
 * @property integer $airport_id
 * @property string $name
 * @property string $city
 * @property string $country
 * @property string $IATA
 * @property string $ICAO
 * @property string $latitude
 * @property string $longitude
 * @property integer $altitude
 * @property string $timezone
 * @property string $daylight_saving_time
 * @property string $tz_string
 * @property string $type
 * @property string $source
 */
class Airports extends CActiveRecord
{

	public static $visitedInSearch = array();
	public static $locations_grouped = array();
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'airports';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('airport_id', 'required'),
			array('airport_id, altitude', 'numerical', 'integerOnly'=>true),
			array('name, city, country, daylight_saving_time', 'length', 'max'=>255),
			array('IATA', 'length', 'max'=>3),
			array('ICAO', 'length', 'max'=>4),
			array('latitude', 'length', 'max'=>8),
			array('longitude', 'length', 'max'=>9),
			array('timezone', 'length', 'max'=>10),
			array('tz_string, type, source', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('airport_id, name, city, country, IATA, ICAO, latitude, longitude, altitude, timezone, daylight_saving_time, tz_string, type, source', 'safe', 'on'=>'search'),
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
			'outgoing_flights' => array(
				self::HAS_MANY, 
				'Flights', 
				array('departure_airport_id' => 'airport_id'),
				'with' => array(
					'arrivalAirport' => array(
						'select' => array(
							'city',
							'IATA'
						)
					),
					'airline' => array(
						'select' => array(
							'name',
							'IATA'
						)
					)
				)
			),
			'incoming_flights' => array(
				self::HAS_MANY, 
				'Flights', 
				array('arrival_airport_id' => 'airport_id'),
				'with' => array(
					'departureAirport' => array(
						'select' => array(
							'city',
							'IATA'
						)
					),
					'airline' => array(
						'select' => array(
							'name',
							'IATA'
						)
					)
				)
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'airport_id' => 'Airport',
			'name' => 'Name',
			'city' => 'City',
			'country' => 'Country',
			'IATA' => 'Iata',
			'ICAO' => 'Icao',
			'latitude' => 'Latitude',
			'longitude' => 'Longitude',
			'altitude' => 'Altitude',
			'timezone' => 'Timezone',
			'daylight_saving_time' => 'Daylight Saving Time',
			'tz_string' => 'Tz String',
			'type' => 'Type',
			'source' => 'Source',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('airport_id',$this->airport_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('country',$this->country,true);
		$criteria->compare('IATA',$this->IATA,true);
		$criteria->compare('ICAO',$this->ICAO,true);
		$criteria->compare('latitude',$this->latitude,true);
		$criteria->compare('longitude',$this->longitude,true);
		$criteria->compare('altitude',$this->altitude);
		$criteria->compare('timezone',$this->timezone,true);
		$criteria->compare('daylight_saving_time',$this->daylight_saving_time,true);
		$criteria->compare('tz_string',$this->tz_string,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('source',$this->source,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Airports the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	//save to static variable to no pull data twice for both dropdowns
	public static function getAllAirportLocations() {
		if(empty(self::$locations_grouped)) {
			$locations = array();
			$criteria = new CDbCriteria();
			$criteria->select = "country, city";
			$criteria->addCondition('t.city != ""');
			$criteria->group = "country, city";
			$criteria->order = "t.country ASC, t.city ASC";
			$criteria->index = 'city';
			$grouped_locations = self::model()->findAll($criteria);
			$locations[] = "Select a location";
			foreach($grouped_locations as $location) {
				$locations[$location->country][$location->city] = $location->city;
			}
			ksort($locations);
			self::$locations_grouped = $locations;
		
		}
		return self::$locations_grouped;
	}


	public function markAsVisited($airport) {
		self::$visitedInSearch[$airport->IATA] = $airport->outgoing_flights;
	}

	public function checkIfVisited($airport) {
		if(isset(self::$visitedInSearch[$airport->IATA])) {
			return self::$visitedInSearch[$airport->IATA];
		}
		return false;
	}

	public static function getAirportsFromUserInputSearch() {
		$airport_criteria = new CDbCriteria();
		$airport_criteria->with = array('outgoing_flights');
		$airport_criteria->addCondition('t.city like :city');
		$airport_criteria->params = array(
			':city' => $_GET['departure_city']
		);

		//add prefered airline for flights if selected
		if(isset($_GET['airline_id']) && !empty($_GET['airline_id'])) {
			$airport_criteria->addCondition('outgoing_flights.airline_id = :airline_id');
			$airport_criteria->params[':airline_id'] = (int)$_GET['airline_id'];
		}
		$possible_airports = Airports::model()->findAll($airport_criteria);

		return $possible_airports;
	}
}
