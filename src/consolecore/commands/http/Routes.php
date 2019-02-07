<?php

namespace console\http;

use \onservice\consolecore\PrintConsole as PrintConsole;


class Routes{

	function __construct(){}


	/** @description: Cria uma classe de rota **/
	public function create(array $parameters){
		

		$dir = 'src'.DIRECTORY_SEPARATOR.'routes';

		$classRoute = isset($parameters[0])?$parameters[0]:null;
		$pathRoute = isset($parameters[1])?$parameters[1]:null;
			
		if($pathRoute != null) $dir = $pathRoute;

		$content = '';

		if($classRoute == null){
			
			$content .= PrintConsole::write(" ".PrintConsole::fixedStringSize('Container missing'),array('bold'=>false,'forecolor'=>'red'));
			$example = 'http/routes:create [class-name] [class-path optional]';
			$content .= PrintConsole::write('example: '.$example,array('bold'=>false,'forecolor'=>'yellow'));
			return $content;
		}

		$pathname = $dir.DIRECTORY_SEPARATOR.$classRoute.DIRECTORY_SEPARATOR;
		@mkdir($pathname,0777,true);
	
		$modelContainerPath = __DIR__.DIRECTORY_SEPARATOR.'_model'.DIRECTORY_SEPARATOR.'container-model.php';
		$modelContainer = file_get_contents($modelContainerPath);
		if(file_exists($pathname.'Index.php')){
			$content .= PrintConsole::write(" ".PrintConsole::fixedStringSize('File already exist'),array('bold'=>false,'forecolor'=>'red'));
			return $content;
		}
		file_put_contents($pathname.'Index.php', $modelContainer);
		
		$content .= PrintConsole::write(" ".PrintConsole::fixedStringSize('Created with success'),array('bold'=>false,'forecolor'=>'green'));

		return $content;
	}



}