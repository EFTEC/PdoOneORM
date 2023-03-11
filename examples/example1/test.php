<?php /** @noinspection ForgottenDebugOutputInspection */

use eftec\examples\example1\repo\FilmTextRepo;
use eftec\PdoOneORM;
use eftec\tests\sakila2021\CityRepo;

include '../../vendor/autoload.php';

include __DIR__.'/phpconf.php';
/** @noinspection PhpUndefinedVariableInspection */
$pdo=PdoOneORM::factoryFromArray($pdoOneConfig);
$pdo->connect();
$pdo->logLevel=3;

echo "<h1>Dropping table</h1>";
FilmTextRepo::dropTable();
echo "<h1>Creating table</h1>";
FilmTextRepo::createTable();
echo "<h1>Truncating table</h1>";
FilmTextRepo::truncate(true);

echo "<h1>Listing paged</h1>";
var_dump(CityRepo::page(2,5)->toList());

echo "<h1>Listing join</h1>";
var_dump(CityRepo::innerjoin('country on city.country_id=country.country_id')->toList());
echo "<h1>Listing table</h1>";
var_dump(CityRepo::recursive(['/_country_id'])->toList());
echo "<h1>Listing cache (if available)</h1>";
var_dump(CityRepo::useCache(0)->toList());
