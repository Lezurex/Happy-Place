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

$id = $_POST['id'];

$db->deleteFromTable("students", new Key("id", $id));

$student_markers = $db->getContainingRows("markers",
    new Key("student_ids", "[$id]"),
    new Key("student_ids", "[$id,%]"),
    new Key("student_ids", "[%,$id,%]"),
    new Key("student_ids", "[%,$id]"));
$markerStudents = json_decode($student_markers[0]['student_ids']);
$markerId = intval($student_markers[0]['id']);
unset($markerStudents[array_search($id, $markerStudents)]);
if (sizeof($markerStudents) == 0) {
    $db->deleteFromTable("markers", new Key("id", $markerId));
} else {
    $db->updateValue("markers", "student_ids", json_encode($markerStudents, JSON_UNESCAPED_UNICODE), new Key("id", $markerId));
}

echo "200";