<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "../../vendor/autoload.php";

use onservice\CreateServer as CreateServer;
use onservice\services\Process as Process;

// create the server
$server = new CreateServer(	new Process() );


$server->process->fork(array(
	'run'=>function(&$parameters){
		
		$index=0;
		while($index<5){


			echo 'first fork - '.$index."\n";

			sleep(1);
			$index++;
		}

	},
	'parameters'=>array('index'=>0)
));


$server->process->fork(array(
	'run'=>function(&$parameters){
		$index = $parameters['index'] ;
		$parameters['index'] = intval($index) + 1;

		
		$index=0;
		while($index<5){
			
			echo 'second fork - '.$index."\n";

			sleep(1);
			$index++;
		}
		
	}, 
	'parameters'=>array('index'=>0)
));
