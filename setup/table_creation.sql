CREATE TABLE `default`.`airports` (
  `airport_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  `city` VARCHAR(255) NULL,
  `city_code` VARCHAR(10) NULL,
  `country` VARCHAR(255) NULL,
  `IATA` VARCHAR(3) NULL,
  `ICAO` VARCHAR(4) NULL,
  `latitude` DECIMAL(8,6) NULL,
  `longitude` DECIMAL(9,6) NULL,
  `altitude` INT NULL,
  `timezone` VARCHAR(20) NULL,
  `daylight_saving_time` VARCHAR(255) NULL,
  `tz_string` VARCHAR(45) NULL,
  `type` VARCHAR(45) NULL,
  `source` VARCHAR(45) NULL,
PRIMARY KEY (`airport_id`));

CREATE TABLE `default`.`airlines` (
  `airline_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  `alias` VARCHAR(45) NULL,
  `IATA` VARCHAR(2) NULL,
  `ICAO` VARCHAR(3) NULL,
  `callsign` VARCHAR(255) NULL,
  `country` VARCHAR(255) NULL,
  `active` INT NULL,
PRIMARY KEY (`airline_id`));

CREATE TABLE `default`.`flights` (
  `flight_id` INT NOT NULL AUTO_INCREMENT,
  `airline_code` VARCHAR(45) NOT NULL,
  `airline_id` INT NOT NULL,
  `departure_airport_code` VARCHAR(45) NOT NULL,  
  `departure_airport_id` INT NOT NULL,
  `departure_time` TIME NULL,
  `arrival_airport_code` VARCHAR(45) NOT NULL,  
  `arrival_airport_id` INT NOT NULL,
  `arrival_time` TIME NULL,
  `price` DECIMAL(7,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`flight_id`));

ALTER TABLE `default`.`flights` 
ADD INDEX `airline_flights_relation_idx` (`airline_id` ASC),
ADD INDEX `departure_airport_relation_idx` (`departure_airport_id` ASC),
ADD INDEX `arrival_airport_relation_idx` (`arrival_airport_id` ASC);
ALTER TABLE `default`.`flights` 
ADD CONSTRAINT `airline_flights_relation`
  FOREIGN KEY (`airline_id`)
  REFERENCES `default`.`airlines` (`airline_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `departure_airport_relation`
  FOREIGN KEY (`departure_airport_id`)
  REFERENCES `default`.`airports` (`airport_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `arrival_airport_relation`
  FOREIGN KEY (`arrival_airport_id`)
  REFERENCES `default`.`airports` (`airport_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

CREATE TABLE `default`.`trip` (
  `trip_id` INT NOT NULL,
  PRIMARY KEY (`trip_id`));


