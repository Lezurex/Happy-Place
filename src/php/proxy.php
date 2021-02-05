<?php

$url = "";
switch ($_REQUEST['type']) {
    case "a":
        $url = "http://a.tile.stamen.com/watercolor/{$_REQUEST['z']}/{$_REQUEST['x']}/{$_REQUEST['y']}.jpg";
        break;
    case "b":
        $url = "http://b.tile.stamen.com/watercolor/{$_REQUEST['z']}/{$_REQUEST['x']}/{$_REQUEST['y']}.jpg";
        break;
    case "c":
        $url = "http://c.tile.stamen.com/watercolor/{$_REQUEST['z']}/{$_REQUEST['x']}/{$_REQUEST['y']}.jpg";
        break;
    case "d":
        $url = "http://d.tile.stamen.com/watercolor/{$_REQUEST['z']}/{$_REQUEST['x']}/{$_REQUEST['y']}.jpg";
        break;
}

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$out = curl_exec($ch);

header("Content-type: ".curl_getinfo($ch, CURLINFO_CONTENT_TYPE));
echo $out;