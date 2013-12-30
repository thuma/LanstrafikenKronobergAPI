<?php

$url = "http://www.ltk.fskab.se/querypage_adv.aspx";

$_GET['departureTime'] = "10:00";
$_GET['date'] = "2014-01-10";

$poststring = 'inpTime='.urlencode($_GET['departureTime']).
'&inpDate='.$_GET['date'].
'&selDirection=0'.
'&selRegionFr=-1'.
'&cmdAction=search'.
'&EU_Spirit=false'.
'&SupportsScript=true'.
'&LiteMode=true'.
'&Source=startpage'.
'&load_iframe=true'.
'&inpPointFr_ajax=Kalmar+Folkets+park%7C8034028%7C0'.
'&inpPointTo_ajax=%C5s+Gamla+K%F6pstad%7C13006014%7C0';

//'selPointFr=Kalmar+Folkets+park++%5BH%E5llplats%5D'.'&selPointTo=%C5s+Gamla+K%F6pstad++%5BH%E5llplats%5D'.'&

$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, TRUE);
curl_setopt($ch,CURLOPT_POSTFIELDS, $poststring);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);

//execute post
$result = curl_exec($ch);

//close connection
curl_close($ch);

$doc = new DOMDocument();
@$doc->loadHTML($result);
$scripts = $doc->getElementsByTagName('script');

$metadata = "";

foreach($scripts as $script){
	$scriptdata = $script->nodeValue;	
	if( 0 < substr_count($scriptdata,'noOfOutwardJourneys')){
		$metadata = trim($scriptdata);
	}
}

$rader = preg_split('/\n/',$metadata);


$sok = array("/new Array\('/","/'\)/",'/new Array\(/','/\)/');
$sokval = array('/var/','/\]/');

$meatainfo = array();
foreach($rader as $rad){
	$delar = preg_split('/=/',$rad);
	$varname = trim(preg_replace($sokval,"",$delar[0]));
	$value = trim(preg_replace($sok,"",$delar[1]));
	$varparts = preg_split('/\[/',$varname);
	if(isset($varparts[1])){
		$meatainfo[$varparts[0]][intval($varparts[1])] = preg_split('/\',\'/',$value);
	}
	else{
		$meatainfo[$varname] = preg_replace('/\'/','',$value);
	}
}


for($i = 0; $i < intval($meatainfo['noOfOutwardJourneys']); $i++){
	$trip = $doc->getElementById('result-'. $i);
	$alltd = $trip->getElementsByTagName('td');
	$meatainfo['priceArr'][$i]['dep'] = trim($alltd->item(1)->nodeValue);
	$meatainfo['priceArr'][$i]['arrival'] = trim($alltd->item(2)->nodeValue);
	$meatainfo['priceArr'][$i]['duration'] = trim($alltd->item(3)->nodeValue);
	$meatainfo['priceArr'][$i]['changes'] = trim($alltd->item(4)->nodeValue);	
	$details = $doc->getElementById('standard-details-'. $i);
	$rader = $details->getElementsByTagName('tr');
	$meatainfo['priceArr'][$i]['subtrips'] = array();
	foreach($rader as $key => $rad){
		$tds = $rad->getElementsByTagName('td');
		if($tds->length != 0){
			$meatainfo['priceArr'][$i]['subtrips'][$key] = array();
			$meatainfo['priceArr'][$i]['subtrips'][$key]['line'] = trim($tds->item(1)->nodeValue);
			$meatainfo['priceArr'][$i]['subtrips'][$key]['fromto'] = trim($tds->item(2)->nodeValue);
			$meatainfo['priceArr'][$i]['subtrips'][$key]['stoppoint'] = trim($tds->item(3)->nodeValue);
			$meatainfo['priceArr'][$i]['subtrips'][$key]['deparr'] = trim($tds->item(4)->nodeValue);
			$meatainfo['priceArr'][$i]['subtrips'][$key]['realdeparr'] = trim($tds->item(5)->nodeValue);
		}
	}
}
print json_encode($meatainfo);

?>
