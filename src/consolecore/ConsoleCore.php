<?php

use \onservice\consolecore\PrintConsole as PrintConsole;
use \onservice\consolecore\Essentials as Essentials;
use \onservice\consolecore\Layout as Layout;

class ConsoleCore {

	private $commands = array();
	public $title = 'OnService';
	public $titleForecolor = 'red';
	public $titleBackcolor = '';
	public $titleBold = true;

	public $commandForecolor = 'cian';
	public $commandBackcolor = '';
	public $commandBold = false;

	public $commandTitleForecolor = 'blue';
	public $commandTitleBackcolor = '';
	public $commandTitleBold = true;

	public $descriptionForecolor = 'yellow';
	public $descriptionBackcolor = '';
	public $descriptionBold = false;
	
	public $legend = '';
	
	function __construct(){

		$this->getVersion();		

	}

	public function getVersion(){
		$filename = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'composer.json';		
		$contentJSON = file_get_contents($filename);
		$content = json_decode($contentJSON);

		if($content->version)
			define('OnServiceVersion',$content->version);
		else
			define('OnServiceVersion','unknown');
	}

	public function getCommands($dir){
		
		$dirArray = Essentials::rscan($dir);

		if(is_array($dirArray) && count($dirArray)>0)
		foreach ($dirArray as $key => $value) {

			$commandName = str_replace('.php', '', $value);
			$commandNameArray = explode('/', $commandName);


			$pathArray = $commandNameArray;
			$continueDir = true;
			foreach ($commandNameArray as $key2 => $value2) {				
				if(substr($value2, 0,1)=='_')$continueDir = false;
			}

			if($continueDir == false)continue;

			
			$path = implode(DIRECTORY_SEPARATOR, $pathArray);
			$fileName = ucwords(end($commandNameArray));
			$fileFull = $dir.$path;
			$fileFull = str_replace('//', '/', $fileFull);
			
			$className = $path;
			$className = 'console\\'.$path;
			$className = str_replace('/', '\\', $className);

			require_once $fileFull.'.php';
			$obj = new $className;
	
			$mthodsArray = get_class_methods($obj);

			foreach ($mthodsArray as $key2 => $value2) {
				$method = $value2;		



				$r = new \ReflectionClass( $className );
				$doc = $r->getMethod($method)->getDocComment();
				preg_match_all('#@(.*?)(\*\*\/|\n)#s', $doc, $annotations);
				$annoArray = $annotations[1];
				if(is_array($annoArray) && count($annoArray)<1)continue;

				$annoArrayNew = [];				
				foreach ($annoArray as $anKey => $anValue) {
					$anName = substr($anValue, 0, strpos($anValue, ':'));
					$annoArrayNew[$anName] = substr($anValue, strpos($anValue, ':')+1);
				}

				$nameMethod = isset($annoArrayNew['name'])?$annoArrayNew['name']:null;
				$orderMethod = isset($annoArrayNew['order'])?$annoArrayNew['order']:100000;
				$description = isset($annoArrayNew['description'])?$annoArrayNew['description']:null;

				$method = ':'.($nameMethod?$nameMethod:$method);

				if($method === ':index'){									
					$method = str_replace(''.$method, '',$method);
				}				

				$order = isset($obj->order)?$obj->order:100000;
				$descriptionClass = isset($obj->description)?$obj->description:null;

				
				$this->commands[strtolower($commandName).$method] = (object) array(
					'obj'=> $obj,
					'order'=> $order,
					'orderMethod'=> $orderMethod,
					'class'=> strtolower($commandName),
					'method'=> $method,
					'nameMethod'=> $value2,
					'description'=> $description,
					'descriptionClass'=> $descriptionClass,
				);

			}

		}


		$commands = '';
		return $commands;
	}

	public function run($dir = null){

		if($dir == null && !is_array($dir) ){

			$dir = getcwd().DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'wallrio'.DIRECTORY_SEPARATOR.'onservice'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'consolecore'.DIRECTORY_SEPARATOR.'commands'.DIRECTORY_SEPARATOR;
			if(!file_exists($dir))
				$dir = 'src'.DIRECTORY_SEPARATOR.'consolecore'.DIRECTORY_SEPARATOR.'commands'.DIRECTORY_SEPARATOR;
		}	

		$this->getCommands($dir);
		
		$orderif = false;
		
		foreach ($this->commands as $key => $value) {
			
			if($value->order !== 100000)
				$orderif = true;
				
			
		}

		function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
		    $sort_col = array();
		    foreach ($arr as $key=> $row) {		    			    	
		        $sort_col[$key] = $row->$col;
		    }

		    array_multisort($sort_col, $dir, $arr);
		}
		
	

		if($orderif == true){			
			array_sort_by_column($this->commands, 'order');
		}
		



		$argv = $GLOBALS['argv'];
		$argc = $GLOBALS['argc'];

		$command = isset($argv[1])?$argv[1]:null;
		$parameter = isset($argv[2])?$argv[2]:null;
		$method = null;

		$parameters = $argv;
		unset($parameters[0]);
		unset($parameters[1]);
		$parameters = array_values($parameters);

		if($command == null){
			Layout::header(null,$this->title,$this->titleForecolor,$this->titleBackcolor,$this->titleBold,$this->legend);
			echo "\n";
			$currentClass = null;
			foreach ($this->commands as $key => $value) {
				
				if($value->class != $currentClass){
					$currentClass = $value->class;									
					
					$classNameCurrent = explode('/', $currentClass);
					$classNameCurrent = reset($classNameCurrent);


					

					$currentDescriptionClass = $value->descriptionClass!=null?$value->descriptionClass:$classNameCurrent;
					
					echo "\n";
					if($value->class !== 'index'){
						
						echo PrintConsole::write(" · ".$currentDescriptionClass,array('bold'=>$this->commandTitleBold,'forecolor'=>$this->commandTitleForecolor,'backcolor'=>$this->commandTitleBackcolor));
						echo "\n";
					}
				}

				if($value->class == 'index')
					$nameCommand = substr($value->method, 1);
				else
					$nameCommand = $value->class.''.$value->method;

				echo PrintConsole::write("   ".PrintConsole::fixedStringSize($nameCommand),array('bold'=>$this->commandBold,'forecolor'=>$this->commandForecolor,'backcolor'=>$this->commandBackcolor));
				echo PrintConsole::write(" ".$value->description,array('bold'=>$this->descriptionBold,'forecolor'=>$this->descriptionForecolor,'backcolor'=>$this->descriptionBackcolor));
				echo "\n";
				
			}
			echo "\n";
			return;
		}

		if( !isset($this->commands[$command]) ){
			$command = 'index:'.$command;
		}

		if(isset($this->commands[$command])){			

			if(substr($command, 0,5)=='index')
				$commandTitle = substr($command, 6);
			else
				$commandTitle = $command;

			Layout::header(false,$this->title,$this->titleForecolor,$this->titleBackcolor,$this->titleBold);
			echo "\n";
			echo PrintConsole::write(" ".PrintConsole::fixedStringSize($commandTitle),array('bold'=>$this->commandBold,'forecolor'=>$this->commandForecolor,'backcolor'=>$this->commandBackcolor));
			echo "\n\n";

			$commandArray = explode(':', $command);

			$method = isset($commandArray[1])?$commandArray[1]:null;
			

			if($method === null){				
				if(method_exists($this->commands[$command]->obj, 'index')){				
					echo " ".$this->commands[$command]->obj->index($parameters);
				}
			}else{
				
				if($this->commands[$command]->nameMethod)
					$method = $this->commands[$command]->nameMethod;
				
				if(method_exists($this->commands[$command]->obj, $method)){				
					echo " ".$this->commands[$command]->obj->$method($parameters);
				}
			}

			Layout::footer();
		}else{
			Layout::header(null,$this->title,$this->titleForecolor,$this->titleBackcolor,$this->titleBold);		
			echo "\n\n";
			echo PrintConsole::write(" ".PrintConsole::fixedStringSize("Command [".$command."] not found."),array('bold'=>false,'forecolor'=>'white'));
			Layout::footer();
		}

	}
}