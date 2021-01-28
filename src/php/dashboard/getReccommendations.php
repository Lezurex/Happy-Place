<?php
include "../database/DatabaseAdapter.php";

session_start();

if (!isset($_SESSION['username'])) {
    echo "401";
    http_send_status(401);
    exit();
}

$db = new DatabaseAdapter();

$result = $db->executeCommand("SELECT plz, name FROM cities WHERE name LIKE '%{$_REQUEST['query']}%' LIMIT 50;");
$array = mysqli_fetch_all($result, MYSQLI_ASSOC);
$json = json_encode($array, JSON_UNESCAPED_UNICODE);
echo $json;