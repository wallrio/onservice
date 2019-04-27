<?php

namespace console;

use \onservice\consolecore\PrintConsole as PrintConsole;

class Index{
	
	public $order = 0;

	function __construct(){}

	/** 
		@order: 0
		@description: show version of onservice 
	**/
	public function version(){
		return OnServiceVersion;
	}

	/** 
		@order: 1
		@name:create-index
		@description: make the file index.php
	**/
	public function createIndex(){
		
		$dir = getcwd().DIRECTORY_SEPARATOR;
		$filename = $dir.'index.php';
		$sourcePath = __DIR__.DIRECTORY_SEPARATOR.'_index'.DIRECTORY_SEPARATOR.'index.php';
		$sourceContent = file_get_contents($sourcePath);
		
		if(file_exists($filename)){
			echo PrintConsole::write(' File already exist',array('bold'=>false,'forecolor'=>'red'));
			return ;
		}

		$sourceContent = str_replace('[version]', 'v'.OnServiceVersion, $sourceContent);

		file_put_contents($filename, $sourceContent);

		if(file_exists($filename)){
			echo PrintConsole::write(' Created with sucess',array('bold'=>false,'forecolor'=>'green'));
			return ;
		}else{
			echo PrintConsole::write(" ".PrintConsole::fixedStringSize('Error'),array('bold'=>false,'forecolor'=>'red'));
			return ;
		}
	}





}