<?php

require_once(__DIR__ . '/config.inc.php');

require_once('bionames-api/lib.php');

// Look up record that corresponds to identifer 

// mockup template

// do PHP stuff here to get query parameters...

/*
echo '<pre>';
print_r($_GET);
echo '</pre>';
*/

//exit(0);

$id = $_GET['id'];
$namespace = $_GET['namespace'];

// OK, we need some HTML content that Google can see when it crawls the page...
$url = $config['web_server'] . $config['web_root'] . "api/api_id.php?id=" . urlencode($id) . "&namespace=" . $namespace;


$json = get($url);


if ($json != '')
{
	$obj = json_decode($json);
	
	//echo $obj->_id;
	
	switch ($obj->type)
	{
		case 'article':
		case 'book':
		case 'generic':
		case 'chapter':
			$target = 'references/' . $obj->_id;
			break;
			
		case 'nameCluster':
			$target = 'names/' . $obj->_id;
			break;

		default:
			$target = $obj->_id;
			break;
	}
	
	//echo $target;
	
	// redirect
	header("Location: " . $config['web_server'] . $config['web_root'] . $target);
	exit(0);
}
else
{
	// don't have this
	//echo 'Not found';
	header('HTTP/1.1 404 Not Found');	
}



?>
