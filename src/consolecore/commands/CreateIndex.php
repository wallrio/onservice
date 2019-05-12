<?php

namespace console;

use \onservice\consolecore\PrintConsole as PrintConsole;

class CreateIndex{
	
	public $title = 'create-index';
	public $description = 'Cria o arquivo na raiz do projeto o arquivo index.php ';
	public $order = 0;

	function __construct(){}

	public function index(){
		
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