<?php

class m191017_193658_initial_setup extends CDbMigration
{
	public function up()
	{

		echo "DROP TABLE IF THEY EXIST\n";
		Yii::app()->db->createCommand(
			"DROP TABLE IF EXISTS flights, airlines, airports;"
		)->execute();



		echo "Creating tables\n";
		$sql = file_get_contents(dirname(__FILE__).'/../../setup/table_creation.sql');
		Yii::app()->db->createCommand($sql)->execute();
		
		echo "Importing airport data\n";
		$sql = file_get_contents(dirname(__FILE__).'/../../setup/airports_random.sql');
		Yii::app()->db->createCommand($sql)->execute();
		
		echo "Importing airlines data\n";
		$sql = file_get_contents(dirname(__FILE__).'/../../setup/airlines_random.sql');
		Yii::app()->db->createCommand($sql)->execute();
		
		echo "Importing flights data\n";
		$sql = file_get_contents(dirname(__FILE__).'/../../setup/flights_random.sql');
		Yii::app()->db->createCommand($sql)->execute();

		echo "Creating random price for flights in dataset\n";
		Yii::app()->db->createCommand(
			"UPDATE flights SET price = FLOOR(50 + RAND( ) * 251 );"
			
		)->execute();

		echo "Creating random times for flights in dataset\n";

		//setup fake cost for each between 50 to 250
		Yii::app()->db->createCommand(
			"UPDATE flights SET departure_time = SEC_TO_TIME(FLOOR(RAND( ) * 86400 )), arrival_time = SEC_TO_TIME(FLOOR(RAND( ) * 86400));"
			
		)->execute();
		return true;
	}

	public function down()
	{
		echo "m191017_193658_initial_setup does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}