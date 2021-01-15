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

$studentId = $_POST['id'];
$plz = $_POST['plz'];
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];

$student_markers = $db->getContainingRows("markers",
    new Key("student_ids", "[$studentId]"),
    new Key("student_ids", "[$studentId,%]"),
    new Key("student_ids", "[%,$studentId,%]"),
    new Key("student_ids", "[%,$studentId]"));

$cityId = $student_markers[0]['city_id'];
$markerId = $student_markers[0]['id'];
$markerStudents = json_decode($student_markers[0]['student_ids']);

$newCity = $db->getStringFromTable("cities", "id", new Key("plz", $plz));
if ($newCity == 0 || $newCity == $cityId) {
    $newCity = $cityId;
} else {
    $newMarkerId = $utils->markerExistsAndGetId($plz);
    if ($newMarkerId != -1) {
        unset($markerStudents[array_search($studentId, $markerStudents)]);
        if (sizeof($markerStudents) == 0) {
            $db->deleteFromTable("markers", new Key("id", $markerId));
        } else {
            $db->updateValue("markers", "student_ids", json_encode($markerStudents, JSON_UNESCAPED_UNICODE), new Key("id", $markerId));
        }
        $result = $db->getStringFromTable("markers", "student_ids", new Key("id", $newMarkerId));
        $newMarkerStudents = json_decode($result);
        array_push($newMarkerStudents, intval($studentId));
        $db->updateValue("markers", "student_ids", json_encode($newMarkerStudents, JSON_UNESCAPED_UNICODE), new Key("id", $newMarkerId));
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
        $newCityId = $db->getStringFromTable("cities", "id", new Key("plz", $plz)); // ZIP Codes with multiple entries will return the first result
        $db->insertIntoTable("markers", new Insert("lon", $lon), new Insert("lat", $lat), new Insert("city_id", $newCityId), new Insert("student_ids", "[$studentId]"));
        unset($markerStudents[array_search($studentId, $markerStudents)]);
        if (sizeof($markerStudents) == 0) {
            $db->deleteFromTable("markers", new Key("id", $markerId));
        } else {
            $db->updateValue("markers", "student_ids", json_encode($markerStudents, JSON_UNESCAPED_UNICODE), new Key("id", $markerId));
        }
    }
}

$db->updateValues("students", new Key("id", $studentId), new Insert("firstname", $firstname), new Insert("lastname", $lastname));

echo 200;