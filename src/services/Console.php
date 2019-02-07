<?php

namespace onservice\services;

class Console{

	public $server = null;
	public $namespace = 'console';
	public $dirConsoleContainer;
	public $title = null;

	public $titleForecolor = null;
	public $titleBackcolor = null;
	public $titleBold = null;

	public $commandForecolor = null;
	public $commandBackcolor = null;
	public $commandBold = null;

	public $commandTitleForecolor = null;
	public $commandTitleBackcolor = null;
	public $commandTitleBold = null;

	public $descriptionForecolor = null;
	public $descriptionBackcolor = null;
	public $descriptionBold = null;
	
	public $legend = null;
	




	// public function __construct($dirConsoleContainer = null,$useCode = true){		
	public function __construct($options = null){		

		$this->title = isset($options['title'])?$options['title']:null;
		$this->legend = isset($options['legend'])?$options['legend']:null;
		
		$this->titleForecolor = isset($options['titleForecolor'])?$options['titleForecolor']:null;
		$this->titleBackcolor = isset($options['titleBackcolor'])?$options['titleBackcolor']:null;
		$this->titleBold = isset($options['titleBold'])?$options['titleBold']:null;

		$this->commandForecolor = isset($options['commandForecolor'])?$options['commandForecolor']:null;
		$this->commandBackcolor = isset($options['commandBackcolor'])?$options['commandBackcolor']:null;
		$this->commandBold = isset($options['commandBold'])?$options['commandBold']:null;

		$this->commandTitleForecolor = isset($options['commandTitleForecolor'])?$options['commandTitleForecolor']:null;
		$this->commandTitleBackcolor = isset($options['commandTitleBackcolor'])?$options['commandTitleBackcolor']:null;
		$this->commandTitleBold = isset($options['commandTitleBold'])?$options['commandTitleBold']:null;

		$this->descriptionForecolor = isset($options['descriptionForecolor'])?$options['descriptionForecolor']:null;
		$this->descriptionBackcolor = isset($options['descriptionBackcolor'])?$options['descriptionBackcolor']:null;
		$this->descriptionBold = isset($options['descriptionBold'])?$options['descriptionBold']:null;



		$dirConsoleContainer = isset($options['dir'])?$options['dir']:null;
		$useCode = isset($options['core'])?$options['core']:true;

		if($dirConsoleContainer == null){
			$this->dirConsoleContainer = getcwd().DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'console'.DIRECTORY_SEPARATOR;
		}else{
			$this->dirConsoleContainer = $dirConsoleContainer;
		}

		if($useCode === true)
		$this->checkConsole();



		$this->run();
	}

	public function checkConsole(){



		$argv = isset($GLOBALS['argv'])?$GLOBALS['argv']:null;
		$argc = isset($GLOBALS['argc'])?$GLOBALS['argc']:null;

		
		if(is_array($argv) && count($argv) >1 && $argv[1] === '-c'){

			$dir = getcwd().DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'wallrio'.DIRECTORY_SEPARATOR.'onservice'.DIRECTORY_SEPARATOR;

			$beforeArg0 = $argv[0];
			unset($argv[0]);				
			$argv = array_values($argv);
			$argv[0] = $beforeArg0;			
			$GLOBALS['argv'] = $argv;

			$content = file_get_contents($dir .'/'. 'console') ;;
			$content = str_replace('#!/usr/bin/php', '', $content);
			$content = str_replace('<?php', '', $content);			
			eval($content);	
			die();
		}
		
	}

	public function run(){

		$argv = isset($GLOBALS['argv'])?$GLOBALS['argv']:null;
		$argc = isset($GLOBALS['argc'])?$GLOBALS['argc']:null;

		if(is_array($argv) && count($argv) >0 ){

			$dirArray = explode('/', __DIR__);
			unset($dirArray[count($dirArray)-1]);
			unset($dirArray[count($dirArray)-1]);
			$dir = implode('/', $dirArray);
				
			$argv = array_values($argv);
			$GLOBALS['argv'] = $argv;

			$content = file_get_contents($dir .'/'. 'console') ;;
			$content = str_replace('#!/usr/bin/php', '', $content);
			$content = str_replace('<?php', '', $content);						
			$content = str_replace('$console->run();', '', $content);			
			$content = str_replace('$console->legend', '//$console->legend', $content);			
			eval($content);	
		
			if($this->title !== null) $console->title = $this->title;
			if($this->titleForecolor !== null) $console->titleForecolor = $this->titleForecolor;
			if($this->titleBackcolor !== null) $console->titleBackcolor = $this->titleBackcolor;

			if($this->commandForecolor !== null) $console->commandForecolor = $this->commandForecolor;
			if($this->commandBackcolor !== null) $console->commandBackcolor = $this->commandBackcolor;
			if($this->commandBold !== null) $console->commandBold = $this->commandBold;

			if($this->commandTitleForecolor !== null) $console->commandTitleForecolor = $this->commandTitleForecolor;
			if($this->commandTitleBackcolor !== null) $console->commandTitleBackcolor = $this->commandTitleBackcolor;
			if($this->commandTitleBold !== null) $console->commandTitleBold = $this->commandTitleBold;


			if($this->descriptionForecolor !== null) $console->descriptionForecolor = $this->descriptionForecolor;
			if($this->descriptionBackcolor !== null) $console->descriptionBackcolor = $this->descriptionBackcolor;
			if($this->descriptionBold !== null) $console->descriptionBold = $this->descriptionBold;
			
			if($this->legend !== null) $console->legend = $this->legend;

			if($this->titleBold !== null){	
				$console->titleBold = $this->titleBold;
			}
			
			
			$console->run($this->dirConsoleContainer);
			die();
		}

	}
}