<?php

include("dbHandler.php");
include("requestHandler.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set('max_execution_time', 0);
ini_set('memory_limit', '2048M');

ini_set("default_charset", "UTF-8");

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

$request_pages_count = null;
$country_users_count = null;
$results_users = null;

$request_users_size = 500;
$request_page = 1;

$countries_json = file_get_contents('europe_countries.json');
$countries = json_decode($countries_json, JSON_UNESCAPED_UNICODE);

$dbHandler = new dbHandler();
$requestHandler = new requestHandler();

$log_file = fopen("log.txt", "w");

foreach($countries as $country) {
    $request_pages_count = 2;
    $request_page = 1;
    fwrite($log_file, time() . " Start for " . $country['name'] .  PHP_EOL);
    do {
        $response = false;
        $response = $requestHandler->makeRequest($country, $request_page, $request_users_size, $log_file);
        if($response) {
            if($request_page === 1) {
                $request_pages_count = $response['pagesCount'];
                $country_users_count = $response['resultsCount'];
                fwrite($log_file, time() . " Pages Count is " . $request_pages_count .  PHP_EOL);
                fwrite($log_file, time() . " Users Count is " . $country_users_count .  PHP_EOL);
            }
            $results_users = $response['results'];
            $dbHandler->insertUsersToDB($country['name'], $results_users, $log_file);
            $request_pages_count--;
            $request_page++;
        }
    } while ($request_pages_count >= 1);
    echo ("Done for " . $country['name']);
    fwrite($log_file, time() . " Done for " . $country['name'].  PHP_EOL . "----------------- " .  PHP_EOL );
}

fclose($log_file);