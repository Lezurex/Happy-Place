<?php

use php\objects\Marker;

require_once $_SERVER['DOCUMENT_ROOT'] . '/php/database/DatabaseAdapter.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/php/objects/Marker.php';

$db = new DatabaseAdapter();

$markers = array();

$data = $db->getAllStringsFromTable("markers");
foreach ($data as $markerData) {
    $marker = new Marker($markerData['id'], $markerData['lat'], $markerData['lon'], $markerData['city_id'], json_decode($markerData['student_ids']));
    array_push($markers, $marker);
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <title>Happy Place</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta content="text/javascript" http-equiv="content-script-type"/>
    <meta content="text/css" http-equiv="content-style-type"/>
    <meta http-equiv="content-language" content="de"/>
    <meta name="author" content="Thomas Heiles"/>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.5.0/css/ol.css"
          type="text/css">
    <link rel="stylesheet" href="/css/new_styles.css">
    <script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.5.0/build/ol.js"></script>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Verdana, Arial, serif;
        }

        .map {
            height: 100%;
            width: 100%;
        }

        .overlay {
            position: absolute;
            z-index: 100;
            padding: .5rem;
            background-color: rgba(255,255,255,0.7);
        }

        #credit {
            bottom: 0;
            right: 0;
        }

        #admin {
            bottom: 0;
            left: 0;
        }

        a {
            transition: .5s;
            color: black;
            text-decoration: none;
        }
        a:hover {
            transition: .5s;
            color: #1f1f1f;
            text-decoration: underline #1f1f1f solid;
        }
    </style>

</head>
<body>
<div id="credit" class="overlay">
    <a href="https://www.openstreetmap.org/copyright">&copy OpenStreetMap contributors</a>
</div>
<div id="admin" class="overlay">
    <a href="/admin">Admin Login</a>
</div>
<div id="map" class="map"></div>
<div id="popup" class="ol-popup">
    <a href="#" id="popup-closer" class="ol-popup-closer"></a>
    <div id="popup-content"></div>
</div>

<script type="text/javascript">
    const markerData = [<?php
        foreach ($markers as $marker) {
            echo $marker->toJSON();
        }
        ?>];

    const features = [];

    let zli = new ol.Feature({
        geometry: new ol.geom.Point(ol.proj.fromLonLat([8.520840,47.360150])),
        name: '<b>Zürcher Lehrbetriebsverband ICT</b><br>Edenstrasse 20'
    })
    zli.setStyle(new ol.style.Style({
        image: new ol.style.Icon({
            anchor: [0.5,0.5],
            anchorXUnits: 'fraction',
            anchorYUnits: 'fraction',
            src: './img/zli.jpg',
            scale: 0.1
        })
    }))
    features.push(zli);

    for (let marker of markerData) {
        let listContent = "";
        let headerText = ""
        if (marker.students.length === 1) {
            headerText = `Hier lebt ${marker.students.length} Person:<br>`;
        } else {
            headerText = `Hier leben ${marker.students.length} Personen:<br>`;
        }
        marker.students.forEach(student => {
            listContent += `<li>${student.firstname} ${student.lastname}</li>`;
        })
        features.push(new ol.Feature({
            geometry: new ol.geom.Point(ol.proj.fromLonLat([marker.lng, marker.lat])),
            name: `<b>${marker.zip} ${marker.name}</b><br>${headerText}<ul>${listContent}<ul>`
        }));
    }

    /**
     * Elements that make up the popup.
     */
    var container = document.getElementById('popup');
    var content = document.getElementById('popup-content');
    var closer = document.getElementById('popup-closer');

    let markers = new ol.layer.Vector({
        source: new ol.source.Vector({
            features: features
        }),
        style: new ol.style.Style({
            image: new ol.style.Icon({
                anchor: [0.5, 1],
                anchorXUnits: 'fraction',
                anchorYUnits: 'fraction',
                src: './img/marker.png',
                scale: 0.05
            })
        })
    })

    /**
     * Create an overlay to anchor the popup to the map.
     */
    var overlay = new ol.Overlay({
        element: container,
        autoPan: true,
        autoPanAnimation: {
            duration: 250
        }
    });


    let map = new ol.Map({
        target: 'map',
        layers: [
            new ol.layer.Tile({
                source: new ol.source.XYZ({
                    urls: [window.location.origin + "/php/proxy.php?type=a&x={x}&y={y}&z={z}", window.location.origin + "/php/proxy.php?type=b&x={x}&y={y}&z={z}", window.location.origin + "/php/proxy.php?type=c&x={x}&y={y}&z={z}", window.location.origin + "/php/proxy.php?type=d&x={x}&y={y}&z={z}"]
                })
            }),
            new ol.layer.Vector({
                source: new ol.source.Vector({
                    format: new ol.format.GeoJSON(),
                    url: './data/countries.geojson'
                })
            }),
            markers
        ],
        overlays: [
            overlay
        ],
        view: new ol.View({
            center: ol.proj.fromLonLat([8.5208324, 47.360127]),
            zoom: 10
        })
    });

    /**
     * Add a click handler to hide the popup.
     * @return {boolean} Don't follow the href.
     */
    closer.onclick = function() {
        overlay.setPosition(undefined);
        closer.blur();
        return false;
    };

    /**
     * Add a click handler to the map to render the popup.
     */
    map.on('singleclick', function(evt) {
        var name = map.forEachFeatureAtPixel(evt.pixel, function(feature) {
            return feature.get('name');
        })
        if (name) {
            container.style.display="block";
            var coordinate = evt.coordinate;
            content.innerHTML = name;
            overlay.setPosition(coordinate);
        } else {
            container.style.display="none";
        }
    });



</script>

</body>
</html>