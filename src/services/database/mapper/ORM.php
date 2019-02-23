<?php

namespace onservice\services\database\mapper;

use onservice\services\Database as Database;
use onservice\services\database\Mysql as Mysql;

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

					if(isset($attributesNew->size)){
						$baseSql = $attributesNew->type.'('.$attributesNew->size.')';
					}else{
						$baseSql = $attributesNew->type.'';				
					}

					$this->database->query('ALTER TABLE '.$table.' ADD '.$field.' '.$baseSql.' '.$notNull.' '.$autoIncrement.' '.$primaryKey.' COLLATE utf8_unicode_ci');
					continue;
				}
			}

			$this->checkDiffAttributes($table,$value,$result);
		
		}
	}



	public function checkDiffAttributes($table,$schemeLocal,$schemeBase){

	

		// normaliza a matriz de $schemeLocal e $schemeBase ----------------------
		$schemeBaseNew = [];
		foreach ($schemeBase as $key => $value) {
			$typeArray = explode('(', $value->Type);
			$type = $typeArray[0];
			$size = null;
			if(isset($typeArray[1])){
				$typeArray[1] = substr($typeArray[1], 0,strlen($typeArray[1])-1);
				$size = $typeArray[1];
			}

			if( strtolower($value->Null) == 'no')
				$null = ', null:false';
			else
				$null = ', null:true';

			if( strtolower($value->Key) == 'pri')
				$keyVal = ', primary:true';
			else
				$keyVal = ', primary:false';

			if( strtolower($value->Extra) == 'auto_increment')
				$increment = ', increment:true';
			else
				$increment = ', increment:false';

			$schemeBaseNew[$key] = 'type:'.$type.', size:'.$size.''.$null.''.$keyVal.''.$increment;
		}

		// converte $schemeLocal $ schemeBase para array igualmente----------------------

		foreach ($schemeLocal as $key => $value) {
			$attributes = explode(',', $value);
			
			$attributesNew = [];
			foreach ($attributes as $key3 => $value3) {
				$attributesNewInd = explode(':', $value3);
				$attributesNew[ trim($attributesNewInd[0]) ] = trim($attributesNewInd[1]);
			}
			$attributesNew = (object) $attributesNew;			
			$schemeLocal[$key] = $attributesNew;
		}

		foreach ($schemeBaseNew as $key => $value) {
			$attributes = explode(',', $value);
			
			$attributesNew = [];
			foreach ($attributes as $key3 => $value3) {
				$attributesNewInd = explode(':', $value3);
				$attributesNew[ trim($attributesNewInd[0]) ] = trim($attributesNewInd[1]);
			}
			$attributesNew = (object) $attributesNew;			
			$schemeBaseNew[$key] = $attributesNew;
		}

		// verifica o scheme local é igual a tabela da base de dados -----------------

		$diff = [];
		foreach ($schemeLocal as $key => $value) {

			if(isset($schemeBaseNew[$key])){

				foreach ( $schemeLocal[$key] as $key2 => $value2) {

					if( isset($value->$key2) ){
						if( (isset($schemeBaseNew[$key]->$key2) && $value2 !== $schemeBaseNew[$key]->$key2) || !isset($schemeBaseNew[$key]->$key2)){
							if(!isset($diff[$key]))$diff[$key] = [];
							if(!isset($diff[$key][$key2]))
								$diff[$key][$key2] = $schemeLocal[$key]->$key2;

								if(!isset($diff[$key]['type'])) $diff[$key]['type'] = $schemeLocal[$key]->type;
								if(isset($schemeLocal[$key]->size))
								if(!isset($diff[$key]['size']))  $diff[$key]['size'] = $schemeLocal[$key]->size;
						}
					}
				}
			}
		}
	

		// aplica na base de dados as diferenças ---------------------

		foreach ($diff as $key => $value) {
			$field = $key;
			$attributes = (object) $value;

			$primaryKey = '';
			if(isset($attributes->primary) && $attributes->primary === 'true'){
				$primaryKey = 'PRIMARY KEY';
			}else{

				$this->database->query('ALTER TABLE '.$table.' DROP PRIMARY KEY');
			}

			$autoIncrement = '';
			if(isset($attributes->increment) && $attributes->increment === 'true') $autoIncrement = 'AUTO_INCREMENT';
			
			$notNull = '';
			if(isset($attributes->null) && $attributes->null === 'false') $notNull = 'NOT NULL';

			if(isset($attributes->size)){
				$baseSql = $attributes->type.'('.$attributes->size.')';
			}else{
				$baseSql = $attributes->type.'';				
			}

			$this->database->query('ALTER TABLE '.$table.' MODIFY COLUMN '.$field.' '.$baseSql.' '.$notNull.' '.$autoIncrement.' '.$primaryKey.'');
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

			$classString .= ' function remove($fieldCompare = "id"){

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

				$response = $database->delete($tableName,$fieldCompare." = \"".$id."\"");

				if($response !== false) unset($this);
				

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



	public function remove(&$class){

		if(gettype($class) === 'array'){
			foreach ($class as $key => $value) {
				$tableName = get_class($value);
				$tableName = explode('\\', $tableName);
				$tableName = end($tableName);

				$where = '';
				$index = 0;
				foreach ($value as $key2 => $value2) {
					$operator = "";
					if($index > count($value)-1)
					$operator = " AND ";

					$where .= $operator." ".$key2.'="'.$value2.'"';

					$index++;
				}

				$result = $this->database->delete($tableName,$where );
				if($result !== false) unset($class[$key]);
			}
		}else{
			$tableName = get_class($class);
			$tableName = explode('\\', $tableName);
			$tableName = end($tableName);
			unset($class->_columns);		
			unset($class->_config);		
			$class = (array) $class;

			$where = '';
			$index = 0;
			foreach ($class as $key2 => $value2) {
				$operator = "";
				if($index > count($class)-1)
				$operator = " AND ";

				$where .= $operator." ".$key2.'="'.$value2.'"';

				$index++;
			}

			$result = $this->database->delete($tableName,$where );
			if($result !== false) unset($class);

		}
		
	
		return true;
		
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