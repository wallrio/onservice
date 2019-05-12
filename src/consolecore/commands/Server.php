<?php

namespace console;

use \onservice\consolecore\PrintConsole as PrintConsole;

class Server{
	
	public $description = 'Inicia um servidor web local (PHP)';
		
	function __construct(){}

	
	public function index(){
		$ip = '127.0.0.1';
		$port = 8082;

		echo PrintConsole::write(" ".PrintConsole::fixedStringSize('Running Server on '.$ip.':'.$port),array('bold'=>false,'forecolor'=>'yellow'));
		
		@exec(' php -S '.$ip.':'.$port."");		
		return '';
	}



}