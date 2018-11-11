<?php

namespace onservice\services\Database\json;

class Document{
	private $config = null;
	private $name = null;
	private $collection = null;
	private $suffix = '_jdoc';

	function __construct($config,$name,$collection){
		$this->config = $config;
		$this->name = $name;
		$this->collection = $collection;
		$this->collectionName = explode(DIRECTORY_SEPARATOR, $collection);
		$this->collectionName = end($this->collectionName);
	}

	

	public function delete($id){
		$collectionDir = $this->collection.DIRECTORY_SEPARATOR;
		$collectionDir = str_replace('//', '/', $collectionDir);

		if(gettype($id) == 'string'){
			$filesArray = [$id];
		}else if(gettype($id) == 'array'){
			$filesArray = $id;
		}

		foreach ($filesArray as $key => $value) {		
			if( gettype($value) == 'string' )
				$filename = $collectionDir . $value.$this->suffix.'.json';
			else if( gettype($value) == 'object' )
				$filename = $collectionDir . $key.$this->suffix.'.json';

			if(file_exists($filename)){				

				unlink($filename);
			}else{
				return false;
			}
		}
		return true;
	}

	public function update($id,$fields = null){
		$collectionDir = $this->collection.DIRECTORY_SEPARATOR;
		$collectionDir = str_replace('//', '/', $collectionDir);

		if(gettype($id) == 'string'){
			$filesArray = [$id];
		}else if(gettype($id) == 'array'){
			$filesArray = $id;
		}

		foreach ($filesArray as $key => $value) {		
			if( gettype($value) == 'string' )
				$filename = $collectionDir . $value.$this->suffix.'.json';
			else if( gettype($value) == 'object' )
				$filename = $collectionDir . $key.$this->suffix.'.json';

			if(file_exists($filename)){				
				$content = file_get_contents($filename);
				$contentObj = json_decode($content);
				foreach ($fields as $key2 => $value2) {
					$contentObj->fields->$key2 = $value2;
				}
				file_put_contents($filename, json_encode($contentObj));
			}else{
				return false;
			}
		}

		return true;
	}

	public function select(array $where = null){
		$collectionDir = $this->collection.DIRECTORY_SEPARATOR;
		$collectionDir = str_replace('//', '/', $collectionDir);

		if(!file_exists($collectionDir)){
			throw new \Exception('Collection not found: '.$this->collectionName);
			
			return false;
		}

		$resultArray = scandir($collectionDir);
		
		foreach ($resultArray as $key => $value)
			if($value == '.'  || $value == '..') unset($resultArray[$key]);
		
		$resultArray = array_values($resultArray);

		$resultFinish = [];
		foreach ($resultArray as $key => $value) {
			$filename = $this->collection.DIRECTORY_SEPARATOR.$value;
			$content = file_get_contents($filename);

			$contentObj = json_decode($content);
			
			$found = false;
			if($where == null){
				$found = true;
			}else{
				foreach ($where as $key2 => $value2) {
					
					if($contentObj->fields->$key2 == $value2) $found = true;

					if( substr($value2, 0,1) == '~')
					if( soundex($contentObj->fields->$key2) == soundex(substr($value2, 1))) $found = true;

					if( substr($value2, 0,1) == '*')
					if( strpos($contentObj->fields->$key2, substr($value2, 1) ) !== false ) $found = true;

				}
			}

			

			if($found == true){
				$resultFinish[$contentObj->hash] = $contentObj->fields;
			}
		}

		if(count($resultFinish) > 0)
			return $resultFinish;
		else
			return false;
	}

	public function create($fields = null){

		$hash = md5(json_encode($fields));
		
		$filename = $this->collection.DIRECTORY_SEPARATOR.$hash.$this->suffix.'.json';
		$filename = str_replace('//', '/', $filename);

		$doc = array(
			'hash' => $hash,
			'fields' => $fields
		);

		$dir = dirname($filename);

		if(!file_exists($dir)) mkdir($dir,0777,true);

		file_put_contents($filename, json_encode($doc));

		if(file_exists($filename))
			return array( $hash => $fields);
		
		return false;
	}
}

