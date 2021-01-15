<?php

use php\dashboard\ManagemenUtils;

include "../database/DatabaseAdapter.php";
require "ManagemenUtils.php";

session_start();

if (!isset($_SESSION['username'])) {
    echo "401";
    http_send_status(401);
    exit();
}

$db = new DatabaseAdapter();
$utils = new ManagemenUtils($db);

$plz = $_POST['plz'];

$db->insertIntoTable("students", new Insert("firstname", $_POST['firstname']), new Insert("lastname", $_POST['lastname']));
$studentId = $db->getConnection()->insert_id;

$markerId = $utils->markerExistsAndGetId(intval($plz));
if ($markerId != -1) {
    $json = json_decode($db->getStringFromTable("markers", "student_ids", new Key("id", $markerId)));
    array_push($json, $studentId);
    $db->updateValue("markers", "student_ids", json_encode($json, JSON_UNESCAPED_UNICODE), new Key("id", $markerId));
} else {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://nominatim.openstreetmap.org/search?postalcode=$plz&country=Switzerland&format=json");
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0");
    curl_setopt($curl, CURLOPT_REFERER, "https://ap20b.lezurex.com/");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $apiJSON = json_decode(curl_exec($curl), true);
    curl_close($curl);
    $lat = $apiJSON[0]['lat'];
    $lon = $apiJSON[0]['lon'];
    $cityId = $db->getStringFromTable("cities", "id", new Key("plz", $plz)); // ZIP Codes with multiple entries will return the first result
    $db->insertIntoTable("markers", new Insert("lon", $lon), new Insert("lat", $lat), new Insert("city_id", $cityId), new Insert("student_ids", "[$studentId]"));
}

echo "200";
