<?php

error_reporting(E_ALL);

global $config;

// Date timezone
date_default_timezone_set('UTC');

$config['site_name'] 	= 'BioNames';
$config['web_server'] 	= 'http://localhost';
$config['web_server'] 	= 'http://192.168.68.119';
$config['web_server'] 	= 'http://bionames.hopto.org';
$config['web_server'] 	= 'http://bionames.org';
$config['web_root']		= "/";

$config['use_disqus']	= false;

?>
