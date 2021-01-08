<?php

require $_SERVER['DOCUMENT_ROOT'] . '/php/database/DatabaseAdapter.php';

$db = new DatabaseAdapter();

?>

<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Happy Place</title>

    <link rel="stylesheet" href="/css/styles.css">
    <script type="text/javascript" src="http://www.openlayers.org/api/OpenLayers.js"></script>
    <script type="text/javascript" src="http://www.openstreetmap.org/openlayers/OpenStreetMap.js"></script>
    <script type="text/javascript" src="/js/map.js"></script>

</head>
<body>
<div id="map" style="width: 900px; height: 600px"></div>
<script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
<script>
    var map = L.map('map').setView([49.0205,7.7522],12);
    L.tileLayer('https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
            '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
            'Imagery © <a href="http://mapbox.com">Mapbox</a>',
        id: 'examples.map-i86knfo3'
    }).addTo(map);
</script>
</body>
</html>
