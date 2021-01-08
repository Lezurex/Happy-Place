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
            var zoom = 15;

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
                addMarker(layer_markers, element.lon, element.lat, "<strong>" + element.city + "</strong><br><span>" + element.plz + "</span>");
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
    $data_item = array();
    $data_item['lon'] = doubleval($marker['lon']);
    $data_item['lat'] = doubleval($marker['lat']);
    $data_item['icon'] = $marker['icon'];
    $data_item['plz'] = $marker['plz'];


    $data_item['city'] = $db->getStringFromTable("ortschaften", "name",  new Key("plz", $marker['plz']));

    array_push($data, $data_item);
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);

?>
</div>

</body>
</html>