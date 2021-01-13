<?php

include $_SERVER['DOCUMENT_ROOT'] . "/php/database/DatabaseAdapter.php";

$db = new DatabaseAdapter();

var_dump($_POST);

if ($db->containsEntry("admin", new Key("benutzername", $_POST['username']))) {
    if (password_verify($_POST['password'], $db->getStringFromTable("admin", "passwort", new Key("benutzername", $_POST['username'])))) {
        echo "proceed";
        exit();
    }
}

echo "unauthorized";