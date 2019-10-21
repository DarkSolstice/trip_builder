<?php

/**
 * This is the model class for table "airlines".
 *
 * The followings are the available columns in table 'airlines':
 * @property integer $airline_id
 * @property string $name
 * @property string $alias
 * @property string $IATA
 * @property string $ICAO
 * @property string $callsign
 * @property string $country
 * @property integer $active
 *
 * The followings are the available model relations:
 * @property Flights[] $flights
 */
class Airlines extends CActiveRecord
{

	public static $airlines_grouped = array();
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'airlines';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('airline_id', 'required'),
			array('airline_id, active', 'numerical', 'integerOnly'=>true),
			array('name, callsign, country', 'length', 'max'=>255),
			array('alias', 'length', 'max'=>45),
			array('IATA', 'length', 'max'=>2),
			array('ICAO', 'length', 'max'=>3),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('airline_id, name, alias, IATA, ICAO, callsign, country, active', 'safe', 'on'=>'search'),
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
			'flights' => array(self::HAS_MANY, 'Flights', 'airline_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'airline_id' => 'Airline',
			'name' => 'Name',
			'alias' => 'Alias',
			'IATA' => 'Iata',
			'ICAO' => 'Icao',
			'callsign' => 'Callsign',
			'country' => 'Country',
			'active' => 'Active',
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

		$criteria->compare('airline_id',$this->airline_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('alias',$this->alias,true);
		$criteria->compare('IATA',$this->IATA,true);
		$criteria->compare('ICAO',$this->ICAO,true);
		$criteria->compare('callsign',$this->callsign,true);
		$criteria->compare('country',$this->country,true);
		$criteria->compare('active',$this->active);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Airlines the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public static function getAllAirlines() {
		if(empty(self::$airlines_grouped)) {
			$airlines = array();
			$criteria = new CDbCriteria();
			$criteria->select = "country, IATA, name, airline_id";
			$criteria->order = "t.country ASC, t.name ASC";
			$airlines_in_db = self::model()->findAll($criteria);
			$airlines[] = "Select a prefered airline (optional)";
			foreach($airlines_in_db as $al) {
				
				$airlines[$al->country][$al->airline_id] = "{$al->name}".(!empty($al->IATA) ? " ({$al->IATA})" : "");
			}
			self::$airlines_grouped = $airlines;
		
		}
		return self::$airlines_grouped;
	}
}
