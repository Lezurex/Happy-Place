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

if (!is_null($students_data)) {

    foreach ($students_data as $student_data) {
        $table_body .= "<tr><td>{$student_data['firstname']}</td><td>{$student_data['lastname']}</td>";
        $id = $student_data['id'];
        $student_markers = $db->getContainingRows("markers",
            new Key("student_ids", "[$id]"),
            new Key("student_ids", "[$id,%]"),
            new Key("student_ids", "[%,$id,%]"),
            new Key("student_ids", "[%,$id]"));

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
        $table_body .= "<td>$locationString</td><td><span data-id='{$student_data['id']}' class='edit'>📝</span><span data-id='{$student_data['id']}' class='delete'>❌</span></td></tr>";
    }
} else {
    $table_body = "<tr><td colspan='4'>Es sind noch keine Lernende eingetragen! Fügen Sie welche hinzu!</td></tr>";
}

echo $table_body;