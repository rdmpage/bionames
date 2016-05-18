<?php

require_once('bionames-api/lib.php');


$name = '';

if (isset($_GET['name']))
{
	$name = $_GET['name'];
}

$image_url = "";

// name search 
$url = 'http://phylopic.org/api/a/name/search' . '?text=' . urlencode($name);

$json = get($url);
if ($json != '')
{
	$obj = json_decode($json);
	if (count($obj->result) > 0)
	{
		$uid = $obj->result[0]->canonicalName->uid;
		
		// Comment out SVG for now as we will need to be cleverer
		// in rescaling image for display on web page, it's not as simple as PNG
		// http://stackoverflow.com/questions/3120739/resizing-svg-in-html
		
		/*
		// Try for SVG first
		$url = 'http://phylopic.org/api/a/name/' . $uid . '/images?options=svgFile';
		$json = get($url);
		
		$obj = json_decode($json);
		
		//print_r($obj);
		
		if ($image_url == '')
		{
			if (count($obj->result->same) > 0)
			{
				if (isset($obj->result->same[0]->svgFile))
				{
					$image_url = 'http://phylopic.org' . $obj->result->same[0]->svgFile->url;
				}
			}
		}
		
		if ($image_url == '')
		{
			if (count($obj->result->supertaxa) > 0)
			{
				if (isset($obj->result->supertaxa[0]->svgFile))
				{
					$image_url = 'http://phylopic.org' . $obj->result->supertaxa[0]->svgFile->url;
				}
			}
		}
		*/
		
		// No SVG, try PNG
		if ($image_url == '')
		{
		
			$url = 'http://phylopic.org/api/a/name/' . $uid . '/images?options=pngFiles';
		
			$json = get($url);
		
			$obj = json_decode($json);
		
			//print_r($obj);
		
			if ($image_url == '')
			{
				if (count($obj->result->same) > 0)
				{
					$image_url = 'http://phylopic.org' . $obj->result->same[0]->pngFiles[0]->url;
				}
			}
		
			if ($image_url == '')
			{
				if (count($obj->result->supertaxa) > 0)
				{
					$image_url = 'http://phylopic.org' . $obj->result->supertaxa[0]->pngFiles[0]->url;
				}
			}
		}		
	}
}

if ($image_url == '')
{
	$image_url = "images/1x1.png";
}

//echo $image_url;
// Cache for performance
header("Cache-control: max-age=3600");

header("Location: $image_url");	

?>