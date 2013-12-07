<?php
require_once dirname(__FILE__) . '/gtfs-stop-reader/getstopname.php';

$all = json_decode(file_get_contents('coord-wgs84.json'));

foreach($all as $key => $station)
	{
		if(isset($all[$key]->gtfs) == FALSE OR $all[$key]->gtfs->id == NULL){
			print "\n".$all[$key]->name;
			$stop = getClosestStation(floatval($all[$key]->position->wgs84->lat),floatval($all[$key]->position->wgs84->long));
			$all[$key]->gtfs = new stdClass;
			$all[$key]->gtfs->id = $stop->stop_id;
			$all[$key]->gtfs->name = $stop->stop_name;
			$all[$key]->gtfs->lat = $stop->stop_lat;
			$all[$key]->gtfs->long = $stop->stop_lon;
			print $stop->stop_name;
		}
	}
file_put_contents('coord-gtfs.json',json_encode($all));
?>