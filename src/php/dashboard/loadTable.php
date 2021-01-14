<?php

include "../database/DatabaseAdapter.php";

session_start();

if (!isset($_SESSION['username'])) {
    echo "401";
    http_send_status(401);
    exit();
}

$db = new DatabaseAdapter();

$students_data = $db->getAllStringsFromTable("students");
$table_body = "";

foreach ($students_data as $student_data) {
    $table_body .= "<tr><td>{$student_data['firstname']}</td><td>{$student_data['lastname']}</td>";
    $student_markers = $db->getContainingRows("markers", new Key("student_ids", $student_data['id']));
    //TODO Student with ID 1 will also match on students with ID 10 - 19, 21, 31, etc

    $locationString = "";
    $count = sizeof($student_markers);
    foreach ($student_markers as $student_marker) {
        $location = $db->getStringsFromTable("cities", new Key("id", $student_marker['city_id']));
        $locationString .= $location['plz'] . " " . $location['name'];
        if ($count != 1) {
            $locationString .= ", ";
        }
        $count--;
    }
    $table_body .= "<td>$locationString</td><td><i data-id='{$student_data['id']}' class='edit'>📝</i><i data-id='{$student_data['id']}' class='delete'>❌</i></td></tr>";
}

echo $table_body;