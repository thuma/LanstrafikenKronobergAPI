<?php
header('Content-type: application/json; charset=utf-8');

$local = json_decode(file_get_contents('coord-gtfs.json'));
$sydlist = json_decode(file_get_contents('nameidsyd.json'));


 switch (json_last_error()) {
        case JSON_ERROR_NONE:
            echo ' - No errors';
        break;
        case JSON_ERROR_DEPTH:
            echo ' - Maximum stack depth exceeded';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - Underflow or the modes mismatch';
        break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Unexpected control character found';
        break;
        case JSON_ERROR_SYNTAX:
            echo ' - Syntax error, malformed JSON';
        break;
        case JSON_ERROR_UTF8:
            echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
        break;
        default:
            echo ' - Unknown error';
        break;
    }

$mapp = array();

print_r($sydlist);
/*
foreach($sydlist as $idnamn){
$mapp[$idnamn->cleanname]=$idnamn->id;
}


foreach($local as $key => $stop){
if(isset($mapp[$stop->cleanname]))
	{
	$local[$key]->sydtaxid = $mapp[$stop->cleanname];
	}
}

file_put_contents('coord-gtfs-sydid.json',json_encode($local));
*/
