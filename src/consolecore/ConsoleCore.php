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
	public $commandsList = null;
	
	function __construct(){

		$this->getVersion();		

	}

	public function getVersion(){
		$filename = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'composer.json';		
		$contentJSON = file_get_contents($filename);
		$content = json_decode($contentJSON);

		if(defined('OnServiceVersion')=== false)
		if($content->version)
			define('OnServiceVersion',$content->version);
		else
			define('OnServiceVersion','unknown');
		
	}

	public function getCommands($dir){
			
		if(substr($dir, strlen($dir)-1)!=='/')
		$dir = $dir.DIRECTORY_SEPARATOR;

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

			$classNameArray = explode('\\',$className);
			unset($classNameArray[count($classNameArray)-1]);
			$namespace = implode('\\', $classNameArray);

			$fileFull_Content = file_get_contents($fileFull.'.php');			

			if(strpos($fileFull_Content, 'namespace '.$namespace.';')!==false){
			}else{
				echo "\n";
				echo PrintConsole::write(" Error:",array('bold'=>false,'forecolor'=>'red'));
				echo "\n";
				echo ' namespace ';
				echo PrintConsole::write($namespace,array('bold'=>false,'forecolor'=>'yellow'));
				echo ' not found in file ';
				echo PrintConsole::write($fileFull.'.php',array('bold'=>false,'forecolor'=>'yellow'));
				echo "\n\n";
				exit;
				
			}

			require_once $fileFull.'.php';

		

			$obj = new $className;

			
			
			$order = isset($obj->order)?$obj->order:1000;
			$description = isset($obj->description)?$obj->description:'';

			$commandNameArray = explode('/', $commandName);
			$commandNameParentArray = $commandNameArray;
			unset($commandNameParentArray[count($commandNameParentArray)-1]);
			$commandNameParent = implode('/', $commandNameParentArray);
			
			if(isset($this->commands[strtolower($commandNameParent)]->order)){
				$this->commands[strtolower($commandNameParent)]->childs = true;

				$orderParent = $this->commands[strtolower($commandNameParent)]->order.'.';
				$className =strtolower($commandNameParent);
			}
			else{
				$orderParent = '';
				$className ='index';
			}

			$title = isset($obj->title)?$obj->title:$path;

			$parent = $commandNameParent;
			$parent = str_replace('\\', '/', $parent);

			// echo $path;
			$this->commands[strtolower($title)] = (object) array(
					'obj'=> $obj,
					'mode'=> 'file',
					'title'=> strtolower($title),
					'class'=> $className,
					'method'=> strtolower(end($commandNameArray)) ,
					'order'=> $orderParent.$order,
					'nameMethod'=> 'Index',
					'description'=> $description,
					'parent'=> $parent,
				);


		

		}


		return $this->commands;
	}

	public function adjustCommandByArray(){
		foreach ($this->commandsList as $key => $value) {				
				
			$childs = $value;
			unset($childs['description']);
			unset($childs['order']);
			unset($childs['function']);

			$description = isset($value['description'])?$value['description']:'';
			$order = isset($value['order'])?$value['order']:10000;
			$name = isset($value['name'])?$value['name']:'';
			$function = isset($value['function'])?$value['function']:null;

			$newCommand = new StdClass;					
			$newCommand->mode = 'array';
			$newCommand->obj = $function;
			$newCommand->description = $description;
			$newCommand->descriptionClass = $description;
			$newCommand->order = $order;
			$newCommand->orderMethod = $order;
			$newCommand->nameMethod = 'Index';
			$newCommand->method = ''.$key;
			$newCommand->class = 'index';

			if(count($childs)>0){
				$newCommand->childs = true;
				$this->commands[''.$key] = $newCommand;

				foreach ($childs as $key2 => $value2) {
					
					$sub_description = isset($value2['description'])?$value2['description']:'';
					$sub_order = isset($value2['order'])?$value2['order']:10000;
					$sub_name = isset($value2['name'])?$value2['name']:'';
					$sub_function = isset($value2['function'])?$value2['function']:null;

					$newCommand_sub = new StdClass;
					$newCommand_sub->mode = 'array';
					$newCommand_sub->obj = $sub_function;
					$newCommand_sub->description = $sub_description;
					$newCommand_sub->descriptionClass = '';
					$newCommand_sub->method = $sub_name;
					$newCommand_sub->class = $key;						
					$newCommand_sub->order =$order.'.'.$sub_order;						
					$newCommand_sub->orderMethod = $sub_order;						
					$newCommand_sub->parent = $key;						
					$this->commands[$key.'/'.$sub_name] = $newCommand_sub;
				}	

			}else{
				$newCommand->childs = false;
				$this->commands[$key] = $newCommand;
			}
			
		}
	}


	public function run($dir = null){

		if($dir == null && !is_array($dir) ){

			$dir = getcwd().DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'wallrio'.DIRECTORY_SEPARATOR.'onservice'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'consolecore'.DIRECTORY_SEPARATOR.'commands'.DIRECTORY_SEPARATOR;
			if(!file_exists($dir))
				$dir = 'src'.DIRECTORY_SEPARATOR.'consolecore'.DIRECTORY_SEPARATOR.'commands'.DIRECTORY_SEPARATOR;
		}	


		if($this->commandsList == null){
			$this->getCommands($dir);
		}else{
			$this->adjustCommandByArray();
		}

		$orderif = false;
		
		foreach ($this->commands as $key => $value) {		
			if($value->order !== 100000)$orderif = true;				
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
				
				$childs = isset($value->childs)?$value->childs:false;									
				$parent = isset($value->parent)?$value->parent:null;									


				if($value->class != $currentClass ){
					$currentClass = $value->class;									
					
					$classNameCurrent = explode('/', $currentClass);
					$classNameCurrent = reset($classNameCurrent);

						
					echo "\n";
					if($value->class !== 'index'){

						if($parent !== null){
							$currentDescription = $this->commands[$parent]->description;
							$methodName = $this->commands[$parent]->method;
						}
							
						echo PrintConsole::write(" · ".PrintConsole::fixedStringSize($methodName),array('bold'=>$this->commandTitleBold,'forecolor'=>$this->commandTitleForecolor,'backcolor'=>$this->commandTitleBackcolor));
						
						echo PrintConsole::write(" ".$currentDescription,array('bold'=>$this->descriptionBold,'forecolor'=>$this->descriptionForecolor,'backcolor'=>$this->descriptionBackcolor));
					
						echo "\n";
					}
	


				

				}

	

				if($value->class == 'index')
					$nameCommand = $value->method;
				else
					$nameCommand = $value->class.'/'.$value->method;

				if(isset($value->title))
				$nameCommand = $value->title;

			

				if($childs === false){

					echo PrintConsole::write("   ".PrintConsole::fixedStringSize($nameCommand),array('bold'=>$this->commandBold,'forecolor'=>$this->commandForecolor,'backcolor'=>$this->commandBackcolor));
		
					echo PrintConsole::write(" ".$value->description,array('bold'=>$this->descriptionBold,'forecolor'=>$this->descriptionForecolor,'backcolor'=>$this->descriptionBackcolor));
					echo "\n";
				}

				
			}
			echo "\n";
			return;
		}



		if(isset($this->commands[$command])){			

			if(substr($command, 0,5)=='index')
				$commandTitle = substr($command, 6);
			else
				$commandTitle = $command;

			$mode = $this->commands[$command]->mode;
			$description = $this->commands[$command]->description;

			Layout::header(false,$this->title,$this->titleForecolor,$this->titleBackcolor,$this->titleBold);
	
			echo PrintConsole::write(" : ".($commandTitle),array('bold'=>$this->commandBold,'forecolor'=>$this->commandForecolor,'backcolor'=>$this->commandBackcolor));
			
			echo "\n";
			echo PrintConsole::write(' '.$description,array('bold'=>$this->descriptionBold,'forecolor'=>$this->descriptionForecolor,'backcolor'=>$this->descriptionBackcolor));

			if($description){
				echo "\n\n";
			}else{
				echo "\n";				
			}

			$commandArray = explode(':', $command);

			$method = isset($commandArray[1])?$commandArray[1]:null;
			

			if($method === null){	
				if($mode === 'array'){
					if(isset($this->commands[$command]->obj)){				
						$obj = $this->commands[$command]->obj;
						echo " ".$obj($parameters);
					}
				}else{	

					if(method_exists($this->commands[$command]->obj, 'index')){				
						echo " ".$this->commands[$command]->obj->index($parameters);
					}
				}
			}else{

				if($mode === 'array'){
					if(isset($this->commands[$command]->obj)){	
						$obj = $this->commands[$command]->obj;
						echo " ".$obj($parameters);
					}
				}else{

				
					if($this->commands[$command]->nameMethod)
						$method = $this->commands[$command]->nameMethod;
					
					if(method_exists($this->commands[$command]->obj, $method)){				
						echo " ".$this->commands[$command]->obj->$method($parameters);
					}
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