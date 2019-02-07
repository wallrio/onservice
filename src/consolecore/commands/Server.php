<?php

namespace console;

use \onservice\consolecore\PrintConsole as PrintConsole;

class Server{
		
	function __construct(){}

	/** @description: Inicia um servidor web local (PHP) **/
	public function run(){

		$ip = '127.0.0.1';
		$port = 8082;

		PrintConsole::write(" ".PrintConsole::fixedStringSize('Running Server on '.$ip.':'.$port),array('bold'=>false,'forecolor'=>'yellow'));

		
		
		@exec(' php -S '.$ip.':'.$port."");

		
		return '';
	}



}