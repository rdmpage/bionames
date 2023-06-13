<?php

echo '<pre>';

// Get running CouchDB

$command = 'ps -ae | grep "CouchDB"';

$output = array();

exec($command, $output);

//print_r($output);

$pids = array();

foreach ($output as $row)
{
	if (preg_match('/^\s*(\d+)\s+.*\/Applications/', $row, $m))
	{
		$pids[] = $m[1];
	}
}

// Show PIDs and kill them

echo "<b>PIDS</b>\n";
print_r($pids);

foreach ($pids as $pid)
{
	$command = 'sudo kill ' . $pid;
	
	$result_code = 0;	
	$result = system($command, $result_code);	
	echo $command . ' ' . $result_code . "\n";
	
}

// Restart CouchDB
echo "<b>Restart CouchDB</b>\n";

$command = "sudo /usr/bin/open '/Applications/CouchDB Server.app'";
$result_code = 0;	
$result = system($command, $result_code);	
echo $command . ' ' . $result_code . "\n";

echo '</pre>';

?>
