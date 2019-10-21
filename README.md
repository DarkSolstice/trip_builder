# mv everything including yii directory to web capable directory #

## step 1 - db config ##

from inside the directory (web-root) navigate to protected/config/database.php

and modify the connectionString to point to an existing mySql installation server

`my local string was 'mysql:host=localhost:3306;dbname=default'` with a db named default.

update the user and password to be able to connect.

## step 1.1 - Test ##

connect to the index page, if everything above is setup correctly you should be redirected to the index.php/site/RegularFlights

otherwise a "could not connect to db" message appears and the above configuration is not valid.

## step 2 - migration ##


Once the above test has completed, you can now apply the migration to the db.

From inside the directory (web-root) run the following command

`php protected/yiic migrate`

the above command should import all the data I used to create the website.

a success message and echoes should be return each step.

## step 3 - Explore flights ##

navigate again to your browser to the index page and you will now be able to search through the flight searcher.

most of my test were doen with the following get parameters

`?departure_city=Montreal&arrival_city=Vancouver&airline_id=330&departure_date=&yt0=Search`

### Final notes ###

Because every flight is available everyday of the week at given times, i didnt add the logic to filter by dates and time. 
That being said the logic is simple, convert everything to UCT (to match times), and compare that the previous flights is before the next flight that should tried to be taken.

Pagination and sorting is possible, i only added sorting by clicking the cost column header.

The crazy flights url (highlighted in blue at the top of regular flights)

Returns a sample size of all fligths with at most 5 hops to get to the destination.

Becasue possibilities are very larger I limited the checking of outgoing flights to the first 5 only.

If anything is wrong contact me please