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
    <link rel="stylesheet" type="text/css" href="map.css"/>
    <script type="text/javascript" src="http://www.openlayers.org/api/OpenLayers.js"></script>
    <script type="text/javascript" src="http://www.openstreetmap.org/openlayers/OpenStreetMap.js"></script>
    <script type="text/javascript" src="tom.js"></script>

    <script type="text/javascript">
        //<![CDATA[

        var map;
        var layer_mapnik;
        var layer_tah;
        var layer_markers;

        function drawmap() {
            // Popup und Popuptext mit evtl. Grafik

            OpenLayers.Lang.setCode('de');

            // Position und Zoomstufe der Karte
            var lon = 8.520837;
            var lat = 47.360117;
            var zoom = 10;

            map = new OpenLayers.Map('map', {
                projection: new OpenLayers.Projection("EPSG:900913"),
                displayProjection: new OpenLayers.Projection("EPSG:4326"),
                controls: [
                    new OpenLayers.Control.Navigation(),
                    new OpenLayers.Control.LayerSwitcher(),
                    new OpenLayers.Control.PanZoomBar()],
                maxExtent:
                    new OpenLayers.Bounds(-20037508.34, -20037508.34,
                        20037508.34, 20037508.34),
                numZoomLevels: 18,
                maxResolution: 156543,
                units: 'meters'
            });

            layer_mapnik = new OpenLayers.Layer.OSM.Mapnik("Mapnik");
            layer_markers = new OpenLayers.Layer.Markers("Address", {
                projection: new OpenLayers.Projection("EPSG:4326"),
                visibility: true, displayInLayerSwitcher: false
            });

            map.addLayers([layer_mapnik, layer_markers]);
            jumpTo(lon, lat, zoom);

            // Position des Markers
            addMarker(layer_markers, 8.520837, 47.360117, "<p>ZLI Zürcher Lehrbetriebsverband</p>");
            let markerJSON = JSON.parse(document.getElementById("marker-data").innerHTML);
            markerJSON.forEach(element => {
                let countString;
                if (element.students.length === 1) {
                    countString = "Eine Person lebt an diesem Ort:";
                } else {
                    countString = element.students.length + " Personen leben an diesem Ort:";
                }
                let listItems = "";
                element.students.forEach(student => {
                    listItems += "<li>" + student.first + " " + student.last + "</li>";
                })

                addMarker(layer_markers, element.lon, element.lat, "<strong>" + element.city + "</strong><br><span>" + element.plz + "<br>" + countString + "</span><ul>" + listItems + "</ul>");
                console.log(element);
            });
        }

        //]]>
    </script>

</head>
<body onload="drawmap();">

<div id="header">
    <div id="content">Happy Place Karte</div>
    <div id="osm">© <a href="http://www.openstreetmap.org">OpenStreetMap</a>
        und <a href="http://www.openstreetmap.org/copyright">Mitwirkende</a>,
        <a href="http://creativecommons.org/licenses/by-sa/2.0/deed.de">CC-BY-SA</a>
    </div>
</div>
<div id="map">
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

//        $request = $db->executeCommand("SELECT * FROM lernende WHERE plz = '" . $marker['plz'] . "';");
//        $result = array();
//        while ($row = mysqli_fetch_assoc($request)) {
//            array_push($result, $row);
//        }
//        $students = array();
//        foreach ($result as $item) {
//            $student = array(
//                "first" => censorString($item['vorname']),
//                "last" => censorString($item['nachname'])
//            );
//            array_push($students, $student);
//        }

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

</body>
</html>