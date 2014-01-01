<?php
header('Content-type: application/json; charset=utf-8');

$filename = 'nameidsyd.json';
$all = array();

// Get names:
if(is_file($filename) == FALSE)
	{
	$bokstaver = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','Å','Ä','Ö');
	for($i = 0; $i < count($bokstaver); $i++)
		{
		$newdata = TRUE;
		$page = 0;
		while($newdata)
			{
			$newdata = FALSE;
			$doc = new DOMDocument();
			@$doc->loadHTML(file_get_contents('http://www.ltk.fskab.se/indexes.aspx?optType=0&sLetter='.urlencode(utf8_decode($bokstaver[$i])).'&iPage='.$page.'&Language=se&optFrTo=0&TNSource=ELMERSYD'));
			$list = $doc->getElementById('add-fetch')->getElementsByTagName('a');
			if($list->length == 0)
			{
				break;
			}
			foreach($list as $node)
				{
				$station['name'] = trim($node->nodeValue);
				$station['cleanname'] = preg_replace('/\s+/', ' ',$station['name']);
				$all[] = $station;
				$newdata = TRUE;
				}
			$page++;
			}
		}
	file_put_contents($filename,json_encode($all));
	}

// Load all stations into object.
$all = json_decode(file_get_contents($filename));
foreach($all as $key => $name)
	{
	if(isset($all[$key]->id) == FALSE)
		{
		$url = 'http://www.ltk.fskab.se/rpajax.aspx?net=ELMERSYD&lang=se&letters='.urlencode(utf8_decode($name->cleanname));
		$data = utf8_encode(file_get_contents($url));
		$stationer = preg_split('/></',$data.'< ');
		foreach($stationer as $soksvar)
			{
			$station = preg_split('/###/', $soksvar);
			$stationinfo = preg_split('/\|/', $station[0]);
			if(trim(preg_replace('/\s+/', ' ',$stationinfo[0])) == $name->cleanname OR trim(preg_replace('/\s+/', ' ',$stationinfo[0])) == $name->name)
				{
				$all[$key]->id = $stationinfo[1];
				$all[$key]->type = $stationinfo[2];
				$all[$key]->url = $url;
				break;
				}
			}
		print_r($all[$key]);
		}
	}

file_put_contents($filename,json_encode($all));

?>
