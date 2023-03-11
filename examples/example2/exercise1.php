<?php

use eftec\PdoOneORM;
use eftec\examples\example2\repo\CityRepo;

include __DIR__."/../../vendor/autoload.php"; // edit the path for the correct one.

$conn= PdoOneORM::factoryFromArray([
    'databaseType' => 'mysql',
    'server' => '127.0.0.1',
    'user' => 'root',
    'pwd' => 'abc.123',
    'database' => 'sakila',
]); // you can include the PHP file generated previously or copy and paste its configuration.
$conn->open(); // opens the database connection. $conn is a singleton, so you need to do it once.
$conn->logLevel=3; // it os optional,
echo "<h2>orm</h2>";
$cities=CityRepo::recursive(['/_country_id','/_address'])->toList();
var_dump(CityRepo::base()->lastQuery);
echo "<pre>";
var_dump($cities);
echo "</pre>";
echo "<h2>query</h2>";
$cities=CityRepo::query('select * from city inner join country on city.country_id=country.country_id');
echo "<pre>";
var_dump($cities);
echo "</pre>";
/*
$newCity=['city' => 'new city','country_id' => 105];
CityRepo::insert($newCity);
CityRepo::update($newCity);
CityRepo::delete($newCity);
CityRepo::deleteById(100);
*/
