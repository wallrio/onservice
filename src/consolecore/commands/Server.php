<?php

namespace console;

use \onservice\consolecore\PrintConsole as PrintConsole;

class Server{
	
	public $description = 'Inicia um servidor web local (PHP)';
		
	function __construct(){}

	
	public function index(array $parameters){
		$ip = '127.0.0.1';
		$port = isset($parameters[0])?$parameters[0]:8082;


		echo PrintConsole::write(" ".'Running Server on '.$ip.':'.$port,array('bold'=>false,'forecolor'=>'yellow'));
		echo "\n\n ";
		
		@exec(' php -S '.$ip.':'.$port."");		
		return '';
	}



}