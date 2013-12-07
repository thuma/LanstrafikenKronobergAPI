<?php
require_once dirname(__FILE__) . '/CoordinateTransformationLibrary/src/coordinatetransformation/positions/RT90Position.php';
require_once dirname(__FILE__) . '/CoordinateTransformationLibrary/src/coordinatetransformation/positions/SWEREF99Position.php';
require_once dirname(__FILE__) . '/CoordinateTransformationLibrary/src/coordinatetransformation/positions/WGS84Position.php';

$all = json_decode(file_get_contents('coord.json'));
foreach($all as $key => $station)
	{
		if(isset($all[$key]->position->wgs84) == FALSE)
		  {
		  $position = new RT90Position($all[$key]->position->rt90->y, $all[$key]->position->rt90->x);
		  $wgs84 = $position->toWGS84();
		  $all[$key]->position->wgs84 = new stdClass;
		  $all[$key]->position->wgs84->lat = $wgs84->getLatitude();
		  $all[$key]->position->wgs84->long = $wgs84->getLongitude();
		  }
	}
file_put_contents('coord-wgs84.json',json_encode($all));
?>