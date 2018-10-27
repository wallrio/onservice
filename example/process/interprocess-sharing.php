<?php

require "../../../../../vendor/autoload.php";

use onservice\CreateServer as CreateServer;
use onservice\services\Process as Process;

// create the server
$server = new CreateServer(	new Process() );

// clear memory
$server->process->memory->clear();

$server->process->fork(array(
	'run'=>function(&$parameters,$memory,$server){

		echo "\n";
		echo "First process initialized";
		echo "\n\n";

		$memory->save('information from first process');
	},
	'parameters'=>array('index'=>0)
));


$server->process->fork(array(
	'run'=>function(&$parameters,$memory,$server){
		
		echo "Second process: ".$memory->load();
		echo "\n\n";
		
	}, 
	'parameters'=>array('index'=>0)
));


// to Get Out of Process Memory Information:
// echo $server->process->memory->load();