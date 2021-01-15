<?php

require $_SERVER['DOCUMENT_ROOT'] . '/php/database/DatabaseAdapter.php';

$db = new DatabaseAdapter();

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.5.0/css/ol.css" type="text/css">
    <link rel="stylesheet" href="/css/styles.css">
    <script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.5.0/build/ol.js"></script>

    <style>
        .map {
            height: 100%;
            width: 100%;
        }
    </style>

</head>
<body>

<div id="header">
    <div id="content">Happy Place Karte</div>
    <div id="osm">© <a href="http://www.openstreetmap.org">OpenStreetMap</a>
        und <a href="http://www.openstreetmap.org/copyright">Mitwirkende</a>,
        <a href="http://creativecommons.org/licenses/by-sa/2.0/deed.de">CC-BY-SA</a>
    </div>
</div>
<div id="map" class="map"></div>
<div id="popup" class="ol-popup">
    <a href="#" id="popup-closer" class="ol-popup-closer"></a>
    <div id="popup-content"></div>
</div>

<div style="display: none" id="marker-data">
    <?php

    $markers = $db->getAllStringsFromTable("markers");
    $data = array();

    foreach ($markers as $marker) {
        $city_data = $db->getStringsFromTable("cities", new Key("id", $marker['city_id']));

        $data_item = array();
        $data_item['lon'] = doubleval($marker['lon']);
        $data_item['lat'] = doubleval($marker['lat']);
        $data_item['icon'] = $marker['icon'];
        $data_item['plz'] = $city_data['plz'];
        $data_item['city'] = $city_data['name'];

        $student_ids = json_decode($marker['student_ids']);
        $students = array();

        foreach ($student_ids as $student_id) {
            $student = $db->getStringsFromTable("students", new Key("id", $student_id));
            $student_item = array(
                "first" => censorString($student['firstname']),
                "last" => censorString($student['lastname'])
            );
            array_push($students, $student_item);
        }

        $data_item['students'] = $students;

        array_push($data, $data_item);
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE);

    function censorString($string)
    {
        $chars = preg_split('//', $string, -1, PREG_SPLIT_NO_EMPTY);
        $return_string = "";
        $count = 0;
        foreach ($chars as $char) {
            if ($count == 0) {
                $return_string .= $char;
            } else {
                $return_string .= "*";
            }
            $count++;
        }
        return $return_string;
    }

    ?>
</div>
<script type="text/javascript">
    var map = new ol.Map({
        target: 'map',
        layers: [
            new ol.layer.Tile({
                source: new ol.source.XYZ({
                    urls : ["http://a.tile.stamen.com/watercolor/{z}/{x}/{y}.png","http://b.tile.stamen.com/watercolor/{z}/{x}/{y}.png","http://c.tile.stamen.com/watercolor/{z}/{x}/{y}.png","http://d.tile.stamen.com/watercolor/{z}/{x}/{y}.png"]
                })
            }),
            new ol.layer.Vector({
                source: new ol.source.Vector({
                    format: new ol.format.GeoJSON(),
                    url: './data/countries.geojson'
                })
            })
        ],
        view: new ol.View({
            center: ol.proj.fromLonLat([8.520833, 47.360103]),
            zoom: 10
        })
    });

    var layer = new ol.layer.Vector({
        source: new ol.source.Vector({
            features: [
                new ol.Feature({
                    geometry: new ol.geom.Point(ol.proj.fromLonLat([8.520833, 47.360103]))
                })
            ]
        })
    });
    map.addLayer(layer);

    let container = document.getElementById('popup');
    let content = document.getElementById('popup-content');
    let closer = document.getElementById('popup-closer');

    let overlay = new ol.Overlay({
        element: container,
        autoPan: true,
        autoPanAnimation: {
            duration: 250
        }
    });
    map.addOverlay(overlay);

    closer.onclick = function() {
        overlay.setPosition(undefined);
        closer.blur();
        return false;
    };

    map.on('singleclick', function (event) {
        if (map.hasFeatureAtPixel(event.pixel) === true) {
            var coordinate = event.coordinate;

            content.innerHTML = "<b>Hello world!</b><br>I am a popup.";
            overlay.setPosition(coordinate);
        } else {
            overlay.setPosition(undefined);
            closer.blur();
        }
    });
</script>

</body>
</html>