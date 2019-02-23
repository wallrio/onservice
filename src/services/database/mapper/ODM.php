<?php

namespace onservice\services\database\mapper;

use onservice\services\Database as Database;
use onservice\services\database\Mongo as Mongo;


class ODM{

	public $server;
	public $namespace = 'odm';
	public $database;

	public function __construct(){}
	public function _init(){}
	
	public function setup($parameters){
		$parameters = (array) $parameters;
		
		$basename = isset($parameters['basename'])?$parameters['basename']:null;


		$this->config = $parameters;	
		
		$this->database = new Database( new Mongo() );	
		$this->base = $this->database->base($basename);	

	}

	public function find($table,$WHERE = array() ){
		
		$collection = $this->base->collection($table);

		$result = $collection->document->select($WHERE);

		
		

		$classModel = [];
		$classString = '';
		$index = 0;
		if( is_array($result) && count($result)>0)
		foreach ($result as $key => $value) {			
			$classString .= 'namespace '.$this->config['basename'].'\_'.$key.';'."\n";	
			$classString .= 'class '.$table.' {';	

			if(!isset($GLOBALS['onservice'])) $GLOBALS['onservice'] = array();
			if(!isset($GLOBALS['onservice']["Database"])) $GLOBALS['onservice']["Database"] = array();
			if(!isset($GLOBALS['onservice']["Database"]["mapper"])) $GLOBALS['onservice']["Database"]["mapper"] = $this->config;
			
			foreach ($value as $key2 => $value2) {
				$classString .= 'public $'.$key2.';';
			}
			
			$classString .= ' function save(){

				$class = $this;			
				
				$tableName = get_class($class);
				$tableName = explode("\\\", $tableName);
				
				$id = $tableName[1];
				$id = substr($id, 1);

				$tableName = end($tableName);

				$parameters = $GLOBALS["onservice"]["Database"]["mapper"];

				
				$basename = isset($parameters["basename"])?$parameters["basename"]:null;
				
				$database = new  \onservice\services\database\Mongo();
	
				$class = (array) $class;

				$base = $database->base($basename);		
				$collection = $base->collection($tableName);

				$result = $collection->document->update($id,
					$class
				);

			
			}';

			$classString .= ' function remove(){

				$class = $this;	
				$tableName = get_class($class);
				$tableName = explode("\\\", $tableName);
				$id = $tableName[1];
				$id = substr($id, 1);
				$tableName = end($tableName);

				$parameters = $GLOBALS["onservice"]["Database"]["mapper"];

			
				$basename = isset($parameters["basename"])?$parameters["basename"]:null;
				
				$database = new  \onservice\services\database\Mongo();
				$base = $database->base($basename);	

				$class = (array) $class;

				$collection = $base->collection($tableName);
			
				$response = $collection->document->delete($id);

				if($response !== false) 
					unset($this);
				

			}';

			$classString .= '};';
			$classString .= ' $classModel[] = new '.$table.';';

			$index++;
		}

		eval($classString);
			


		$index = 0;
		if( is_array($result) && count($result)>0)
		foreach ($result as $key => $value) {
			foreach ($value as $key2 => $value2) {
				$classModel[$index]->$key2 = $value2;
			}
			$index++;
		}
	
		return $classModel;	
	}


	public function save($class,$fieldCompare = 'id'){
		
		if(gettype($class) === 'array'){
			foreach ($class as $key => $value) {
				$tableName = get_class($value);
				$tableName = explode('\\', $tableName);
				$id = $tableName[1];
				$id = substr($id, 1);

				$tableName = end($tableName);

				$collection = $this->base->collection($tableName);

				$result = $collection->document->update($id,
					$value
				);

			}
		}else{
			$tableName = get_class($class);
			$tableName = explode('\\', $tableName);
			$tableName = end($tableName);
			unset($class->_columns);		
			unset($class->_config);		
			$class = (array) $class;
			$collection = $this->base->collection($tableName);

			$result = $collection->document->update($id,
				$class
			);

		}
		
	}

	public function create($table){
		$basename = $this->config['basename'];
		$result = array();

		$object = new \StdClass;
		$columns = array();
		foreach ($result as $key => $value) {
			$object->$key = null;
			array_push($columns, $key);
		}

		$classString = 'namespace '.$basename.';'."\n";	

		$classString .= 'class '.$table.' {';		

		if(!isset($GLOBALS['onservice'])) $GLOBALS['onservice'] = array();
		if(!isset($GLOBALS['onservice']["Database"])) $GLOBALS['onservice']["Database"] = array();
		if(!isset($GLOBALS['onservice']["Database"]["mapper"])) $GLOBALS['onservice']["Database"]["mapper"] = $this->config;

	
		foreach ($result as $key => $value) {			
			$classString .= 'public $'.$key.';';
		}

		$classString .= 'public function save(){ 
			$class = $this;			
			
			$tableName = get_class($class);
			$tableName = explode("\\\", $tableName);
			$tableName = end($tableName);
	
			$class = (array) $class;

			$parameters = $GLOBALS["onservice"]["Database"]["mapper"];

			$driver = isset($parameters["driver"])?$parameters["driver"]:null;
			$host = isset($parameters["host"])?$parameters["host"]:null;
			$username = isset($parameters["username"])?$parameters["username"]:null;
			$password = isset($parameters["password"])?$parameters["password"]:null;
			$basename = isset($parameters["basename"])?$parameters["basename"]:null;
			
			$database = new \onservice\services\database\Mongo();
		
			$base = $database->base($basename);					
			$collection = $base->collection($tableName);
			$result = $collection->document->create($class);

			return true;

		}';

		$classString .= '}; $classModel = new \\'.$basename.'\\'.$table.';';

		eval($classString);
		
		return $classModel;
	}



	public function remove(&$class){	
		foreach ($class as $key => $value) {
			$tableName = get_class($value);
			$tableName = explode('\\', $tableName);
			$tableName = end($tableName);

			$collection = $this->base->collection($tableName);
			$resultSelect = $collection->document->select((array)$value);
			$result = $collection->document->delete($resultSelect);

		}
		return true;
	}
}