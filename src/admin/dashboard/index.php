<?php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: /admin");
    exit();
}

?>

<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Happy Place - Management Console</title>

    <link rel="stylesheet" href="/css/dashboard.css">

</head>
<body onload="onLoad();">
<div id="notice">
    <div id="notice-box">
        Operation erfolgreich vorgenommen!
    </div>
</div>

<header>
    <div>
        <p>Happy Place - Management Console</p>
    </div>
    <a href="/php/login/logout.php">
        <button>Logout</button>
    </a>
</header>

<main>
    <div class="card">
        <h2 class="card-heading">
            Management
        </h2>
        <div class="card-body">
            <p>Tabelle von eingetragenen Lernenden:</p>
            <div id="table-container">
                <table id="table">
                    <thead>
                    <tr>
                        <th>Vorname</th>
                        <th>Nachname</th>
                        <th>Wohnort(e)</th>
                        <th>Aktionen</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="card-heading">
            Lernenden hinzufügen
        </h2>
        <div class="card-body">
            <p>Fügen Sie hier einen neuen Lernenden hinzu.</p>
            <div id="add-form">
                <label>Vorname
                    <input id="add-firstname" type="text" placeholder="Max" autocomplete="off">
                </label><br>
                <label>Nachname
                    <input id="add-lastname" type="text" placeholder="Mustermann" autocomplete="off">
                </label><br>
                <label>PLZ, Ort
                    <input id="add-city" list="add-datalist" class="city-autocomplete" type="text" placeholder="1000"
                           autocomplete="off">
                    <datalist id="add-datalist"></datalist>
                </label><br><br>
                <button id="add-btn">
                    Senden
                </button>
            </div>
        </div>
    </div>
</main>
<div> <!-- Popups -->
    <div id="edit-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <label>Vorname
                <input id="edit-firstname" type="text" autocomplete="off">
            </label><br>
            <label>Nachname
                <input id="edit-lastname" type="text" autocomplete="off">
            </label><br>
            <label>PLZ, Ort
                <input id="edit-city" list="edit-datalist" class="city-autocomplete" type="text" autocomplete="off">
                <datalist id="edit-datalist"></datalist>
            </label><br><br>
            <button id="edit-save" class="btn-green">
                Speichern
            </button>
        </div>
    </div>
</div>
<script src="/js/dashboard.js"></script>
</body>
</html>
