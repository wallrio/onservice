<?php

namespace onservice\services\Database\mapper;

use onservice\services\Database as Database;
use onservice\services\Database\Mysql as Mysql;

class ORM{

	public $server;
	public $namespace = 'orm';
	public $database;

	public function __construct(){}
	public function _init(){}
	
	public function setup($parameters){
		$parameters = (array) $parameters;
		
		$driver = isset($parameters['driver'])?$parameters['driver']:null;
		$host = isset($parameters['host'])?$parameters['host']:null;
		$username = isset($parameters['username'])?$parameters['username']:null;
		$password = isset($parameters['password'])?$parameters['password']:null;
		$basename = isset($parameters['basename'])?$parameters['basename']:null;

		$this->config = $parameters;	
		$this->database = new Database( new Mysql($host,$username,$password,$basename ) );	

	}


	// Mantem a base igual ao scheme (apenas cria/adiciona)
	public function scheme($scheme){
		foreach ($scheme as $key => $value) {
			$result = $this->database->getScheme($key);
			
			$checkTable = $this->database->query('SHOW TABLES LIKE "'.$key.'"');

			$table = $key;

			$result = (array) $result;
			$value = (array) $value;

			$scheme_keys = array_keys($value);
			$base_keys = array_keys($result);

			$diff1 = array_diff($scheme_keys,$base_keys);
			if(count($diff1)>0){

				foreach ($diff1 as $key2 => $value2) {
					$field = $value2;
					

					$attributes = isset($value[$field])?$value[$field]:null;

					$attributes = explode(',', $attributes);
										
					$attributesNew = [];
					foreach ($attributes as $key3 => $value3) {
						$attributesNewInd = explode(':', $value3);
						$attributesNew[ trim($attributesNewInd[0]) ] = trim($attributesNewInd[1]);
					}
					$attributesNew = (object) $attributesNew;
				

					$primaryKey = '';
					if(isset($attributesNew->primary) && $attributesNew->primary === 'true') $primaryKey = 'PRIMARY KEY';

					$autoIncrement = '';
					if(isset($attributesNew->increment) && $attributesNew->increment === 'true') $autoIncrement = 'AUTO_INCREMENT';
					
					$notNull = '';
					if(isset($attributesNew->null) && $attributesNew->null === 'false') $notNull = 'NOT NULL';
					
					if(!is_array($checkTable)){	

						$fields = array($field=>$attributesNew->type.' '.$notNull.' '.$autoIncrement.' '.$primaryKey.'  COLLATE utf8_unicode_ci');
						
						$this->database->createTable($key,$fields);

						$this->scheme($scheme);
						return;
					}

					$this->database->query('ALTER TABLE '.$table.' ADD '.$field.' '.$attributesNew->type.'('.$attributesNew->size.') '.$notNull.' COLLATE utf8_unicode_ci');
				}
			}
		
		}
	}



	public function find($table,$WHERE = null){
		$result = $this->database->select($table,$WHERE);

		$object = new \StdClass;
		$columns = array();
		foreach ($result as $key => $value) {
			$object->$key = null;
			array_push($columns, $key);
		}

		$classModel = [];
		$classString = '';
		foreach ($result as $key => $value) {			
			$classString .= 'namespace '.$this->config['basename'].'\index_'.$key.';'."\n";	
			$classString .= 'class '.$table.' {';	

			if(!isset($GLOBALS['onservice'])) $GLOBALS['onservice'] = array();
			if(!isset($GLOBALS['onservice']["Database"])) $GLOBALS['onservice']["Database"] = array();
			if(!isset($GLOBALS['onservice']["Database"]["mapper"])) $GLOBALS['onservice']["Database"]["mapper"] = $this->config;
			
			foreach ($value as $key2 => $value2) {
				$classString .= 'public $'.$key2.';';
			}
			
			$classString .= ' function save($fieldCompare = "id"){

				$class = $this;			
				
				$tableName = get_class($class);
				$tableName = explode("\\\", $tableName);
				$tableName = end($tableName);

				$parameters = $GLOBALS["onservice"]["Database"]["mapper"];

				$driver = isset($parameters["driver"])?$parameters["driver"]:null;
				$host = isset($parameters["host"])?$parameters["host"]:null;
				$username = isset($parameters["username"])?$parameters["username"]:null;
				$password = isset($parameters["password"])?$parameters["password"]:null;
				$basename = isset($parameters["basename"])?$parameters["basename"]:null;
				
				$database = new \onservice\services\Database( new \onservice\services\Database\Mysql($host,$username,$password,$basename ) );
				
				$id = $class->$fieldCompare;	
				
				$class = (array) $class;

				$database->update($tableName,$class,$fieldCompare." = \"".$id."\"");

			}';

			$classString .= '}; $classModel[] = new '.$table.';';
		}

		eval($classString);
		
		foreach ($result as $key => $value) {
			foreach ($value as $key2 => $value2) {
				$classModel[$key]->$key2 = $value2;
			}
		}
	
		return $classModel;
	}



	public function save($class,$fieldCompare = 'id'){
		
		if(gettype($class) === 'array'){
			foreach ($class as $key => $value) {
				$tableName = get_class($value);
				$tableName = explode('\\', $tableName);
				$tableName = end($tableName);

				$id = $value->$fieldCompare;				
				$this->database->update($tableName ,(array) $value,
					$fieldCompare.' = "'.$id.'"'
				);		
			}
		}else{
			$tableName = get_class($class);
			$tableName = explode('\\', $tableName);
			$tableName = end($tableName);
			unset($class->_columns);		
			unset($class->_config);		
			$class = (array) $class;
			$this->database->insert($tableName,(array)$class);
		}
		
	}



	public function create($table){
		$result = $this->database->getScheme($table);

		$object = new \StdClass;
		$columns = array();
		foreach ($result as $key => $value) {
			$object->$key = null;
			array_push($columns, $key);
		}

		$classString = 'namespace '.$this->config['basename'].';'."\n";	

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
			
			$database = new \onservice\services\Database( new \onservice\services\Database\Mysql($host,$username,$password,$basename ) );
			
			unset($class["_config"]);
			
			$database->insert($tableName,$class);

			return true;

		}';

		$classString .= '}; $classModel = new '.$table.';';

		eval($classString);
		
		return $classModel;
	}

}