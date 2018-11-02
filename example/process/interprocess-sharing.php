<?php

require "../../../../../vendor/autoload.php";

use onservice\CreateServer as CreateServer;
use onservice\services\Process as Process;

// create the server
$server = new CreateServer(	new Process() );

// clear memory
$server->process->memory->clear();


$server->process->fork(array(
	'run'=>function($parameters,$memory,$server,$pid,$pidParent){

		$memory->save('[information from first process...]');

		echo "\n";
		echo "First process initialized";
		echo "\n\n";
	},
	'parameters'=>array('index'=>0), // optional
	'parent'=>function($parameters,$memory,$server,$pid,$pidChild){ // optional
		// parent proccess
	}
));


$server->process->fork(array(
	'run'=>function($parameters,$memory,$server,$pid,$pidParent){
		
		usleep(1000);
		echo "Second process: ".$memory->load();
		echo "\n\n";
	}, 
	'parameters'=>array('index'=>0), // optional
	'parent'=>function($parameters,$memory,$server,$pid,$pidChild){ // optional
		// parent proccess
	},
));

sleep(1);
echo "\n\n"."get information of process child on parent process:\n";

// to Get Out of Process Memory Information:
echo $server->process->memory->load();
echo "\n\n";