<?php

require "../../../../../vendor/autoload.php";

use onservice\CreateServer as CreateServer;
use onservice\services\Process as Process;

// create the server
$server = new CreateServer(	new Process() );

$server->process->fork(array(
	'run'=>function($parameters,$memory,$server,$pid,$pidParent){
		$index=0;
		while($index<5){
			echo 'first fork - '.$index."\n";
			sleep(1);
			$index++;
		}
	},
	'parameters'=>array('index'=>0), // optional
	'parent'=>function($parameters,$memory,$server,$pid,$pidChild){ // optional
		// parent proccess
	}
));


$server->process->fork(array(
	'run'=>function($parameters,$memory,$server,$pid,$pidParent){
		$index = $parameters['index'] ;
		$parameters['index'] = intval($index) + 1;
		$index=0;
		while($index<5){
			echo 'second fork - '.$index."\n";
			sleep(1);
			$index++;
		}
	}, 
	'parameters'=>array('index'=>0), // optional
	'parent'=>function($parameters,$memory,$server,$pid,$pidChild){ // optional
		// parent proccess 
	}
));
