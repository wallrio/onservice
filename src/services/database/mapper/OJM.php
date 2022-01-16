<?php

namespace onservice\services\database\mapper;

use onservice\services\Database as Database;
use onservice\services\database\JSON as JSON;


class OJM{

	public $server;
	public $namespace = 'ojm';
	public $database;

	public function __construct(){}
	public function _init(){}
	
	public function setup($parameters){
		$parameters = (array) $parameters;
		
		$dir = isset($parameters['dir'])?$parameters['dir']:null;
		$basename = isset($parameters['basename'])?$parameters['basename']:null;


		$this->config = $parameters;	
		
		$this->database = new Database( new JSON() );	

		$this->database->config(array(
			'dir' => $dir,	// optional - default = temporary dir
			'basename' => $basename
		));

		$this->base = $this->database->base($basename);	


	}

	public function find($table,$WHERE = array(),$options = null ){
		
		$table = str_replace('-', '_', $table);
		$table = str_replace('.', '_', $table);


		$basename = $this->config['basename'];

		$basename = str_replace('/', '\\', $basename);
		$basenameArray = explode('\\', $basename);
		foreach ($basenameArray as $key => $value) {
			if( is_numeric( substr($value, 0,1) ) === true ){
				$basenameArray[$key] = '_'.$value;
			}
		}
		$basename = implode('\\', $basenameArray);


		$collection = $this->base->collection($table);

	
		$result = $collection->document->select($WHERE,$options);

		$classModel = [];
		$classString = '';
		$index = 0;
		if( is_array($result) && count($result)>0)
		foreach ($result as $key => $value) {	
			
			$key = str_replace('/', '\\', $key);		
			$key = preg_replace('/[^\/A-Za-z0-9\-]/', '', $key);			

			if( is_numeric( substr($table, 0,1) ) ){
				$table = '_'.$table;
			}

			$classString .= 'namespace '.$basename.'\_'.$key.'_'.time().';'."\n";	
			
			$classString .= 'class '.$table.' {';	

			if(!isset($GLOBALS['onservice'])) $GLOBALS['onservice'] = array();
			if(!isset($GLOBALS['onservice']["Database"])) $GLOBALS['onservice']["Database"] = array();
			if(isset($GLOBALS['onservice']["Database"])) 
				$GLOBALS['onservice']["Database"]["mapper"] = $this->config;
			
			foreach ($value as $key2 => $value2) {
				$classString .= 'public $'.$key2.';';
			}
			
			$classString .= ' function save(){

				$class = $this;			
				
				$tableName = get_class($class);

				 
				$tableName = explode("\\\", $tableName);
				
			
				$tableNameLast = end($tableName);
				unset($tableName[count($tableName)-1]);
				foreach ($tableName as $key2 => $value2) {
					if( substr($value2, 0,1) === "_" ){
						$tableName[$key2] = substr($value2, 1);
					}
				}
				$id = end($tableName);
				$id = explode("_", $id);
				$id = $id[0];

				
		

				$parameters = $GLOBALS["onservice"]["Database"]["mapper"];

				$dir = isset($parameters["dir"])?$parameters["dir"]:null;
				$basename = isset($parameters["basename"])?$parameters["basename"]:null;
				
				$database = new  \onservice\services\database\JSON();

				$database->config(array(
					"dir" => $dir,	
					"basename" => $basename
				));


				if( substr($tableNameLast, 0,1) === "_" ){
					$tableNameLast = substr($tableNameLast, 1);
				}
		
				

				$class = (array) $class;

				$base = $database->base($basename);		
				$collection = $base->collection($tableNameLast);

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
				$id = explode("_", $id);
				$id = $id[0];

			

				$tableName = end($tableName);

				$parameters = $GLOBALS["onservice"]["Database"]["mapper"];

				$dir = isset($parameters["dir"])?$parameters["dir"]:null;
				$basename = isset($parameters["basename"])?$parameters["basename"]:null;
				
				$database = new  \onservice\services\database\JSON();

				$database->config(array(
					"dir" => $dir,	
					"basename" => $basename
				));

				$base = $database->base($basename);	

				$class = (array) $class;

				$collection = $base->collection($tableName);
			
				$response = $collection->document->delete($id);


			}';

			$classString .= '};';
			$classString .= ' $classModel[] = new '.$table.';';

			$index++;
		}

		// echo($classString);
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
			
				$tableNameLast = end($tableName);

				unset($tableName[count($tableName)-1]);

				foreach ($tableName as $key2 => $value2) {
					if( substr($value2, 0,1) === '_' ){
						$tableName[$key2] = substr($value2, 1);
					}
				}

				$id = end($tableName);
				$id = explode('_', $id);
				$id = $id[0];

				$collection = $this->base->collection($tableNameLast);
	
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

	public function create($table,$hash = null){

		$table = str_replace('-', '_', $table);
		$table = str_replace('.', '_', $table);

		
		$basename = $this->config['basename'];

		$basename = str_replace('/', '\\', $basename);
		$basenameArray = explode('\\', $basename);
		foreach ($basenameArray as $key => $value) {
			if( is_numeric( substr($value, 0,1) ) === true ){
				$basenameArray[$key] = '_'.$value;
			}
		}
		$basename = implode('\\', $basenameArray);

		$result = array();

		$object = new \StdClass;
		$columns = array();
		foreach ($result as $key => $value) {
			$object->$key = null;
			array_push($columns, $key);
		}

		if( is_numeric( substr($table, 0,1) ) ){
			$table = '_'.$table;
		}

		$classString = 'namespace '.$basename.';'."\n";	

		$classString .= 'class '.$table.' {';		

		

		if(!isset($GLOBALS['onservice'])) $GLOBALS['onservice'] = array();
		if(!isset($GLOBALS['onservice']["Database"])) $GLOBALS['onservice']["Database"] = array();
		if(isset($GLOBALS['onservice']["Database"])) 
			$GLOBALS['onservice']["Database"]["mapper"] = $this->config;

	
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

			$dir = isset($parameters["dir"])?$parameters["dir"]:null;
			$basename = isset($parameters["basename"])?$parameters["basename"]:null;
			
			$database = new  \onservice\services\database\JSON();



			$database->config(array(
				"dir" => $dir,	
				"basename" => $basename
			));

			if( substr($tableName, 0,1) === "_" ){
				$tableName = substr($tableName, 1);
			}
			
	
			$base = $database->base($basename);					
			$collection = $base->collection($tableName);
			$result = $collection->document->create($class,"'.$hash.'");

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