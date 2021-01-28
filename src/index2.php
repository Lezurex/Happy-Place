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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de-de">
<head>
    <title>Happy Place</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="content-script-type" content="text/javascript"/>
    <meta http-equiv="content-style-type" content="text/css"/>
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

        #credit {
            position: absolute;
            z-index: 100;
            bottom: 0;
            right: 0;
            padding: .5rem;
            background-color: rgba(255,255,255,0.7);
        }

        #credit a {
            transition: .5s;
            color: black;
            text-decoration: none;
        }
        #credit a:hover {
            transition: .5s;
            color: #1f1f1f;
            text-decoration: underline #1f1f1f solid;
        }
    </style>

</head>
<body>
<div id="credit">
    <a href="https://www.openstreetmap.org/copyright">&copy OpenStreetMap contributors</a>
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
    for (let marker of markerData) {
        features.push(new ol.Feature({
            geometry: new ol.geom.Point(ol.proj.fromLonLat([marker.lng, marker.lat])),
        }));
    }
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
                    urls: ["http://a.tile.stamen.com/watercolor/{z}/{x}/{y}.png", "http://b.tile.stamen.com/watercolor/{z}/{x}/{y}.png", "http://c.tile.stamen.com/watercolor/{z}/{x}/{y}.png", "http://d.tile.stamen.com/watercolor/{z}/{x}/{y}.png"]
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
     * Elements that make up the popup.
     */
    var container = document.getElementById('popup');
    var content = document.getElementById('popup-content');
    var closer = document.getElementById('popup-closer');

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