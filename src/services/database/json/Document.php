<?php

namespace onservice\services\database\json;

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

		if(isset($filesArray) )
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
			else if( gettype($value) == 'object' ||  gettype($value) == 'array' )
				$filename = $collectionDir . $key.$this->suffix.'.json';
			
				
			

			if(file_exists($filename)){		

				$content = file_get_contents($filename);
				$contentObj = json_decode($content);

				if( (is_array($fields) || is_object($fields)) && count($fields)<1) return false;
				foreach ($fields as $key2 => $value2) {
					if(strpos($key2, '/')!= false ){
						$key2Array = explode('/', $key2);
						$join='';
						$join2='';
						foreach ($key2Array as $key3 => $value3) {
							if($key3 >= count($key2Array)-1){
								$join.='{"'.$value3.'"}';
								$join2.='{"'.$value3.'"}';
								eval('if(!isset($contentObj->fields->'.$join2.'))$contentObj->fields->'.$join2.'=(object) array();');
							}
							else{
								$join.='{"'.$value3.'"}'.'->';
								$join2.='{"'.$value3.'"}'.'->';

								eval(' if(!isset($contentObj->fields->'.substr($join, 0,strlen($join)-2).'))$contentObj->fields->'.substr($join, 0,strlen($join)-2).'=(object) array();');
							}

						}
						
						eval('$contentObj->fields->'.$join2.' = $value2 ;');
						
					}else{
						

						if(gettype($value) == 'object'){
							$contentObj->fields->$key2 = $value2;
						}else if(gettype($value) == 'array'){
							$contentObj->fields[$key2] = $value2;							
						}else{
							$contentObj->fields->$key2 = $value2;						
						}
					}
				}

	
				file_put_contents($filename, json_encode($contentObj));
			}else{
				return false;
			}
		}

		return true;
	}

	public function scanAllDir($dir) {
	  $result = [];
	  foreach(scandir($dir) as $filename) {
	    if ($filename[0] === '.') continue;
	    $filePath = $dir . '/' . $filename;
	    if (is_dir($filePath)) {
	      foreach ($this->scanAllDir($filePath) as $childFilename) {
	        $result[] = $filename . '/' . $childFilename;
	      }
	    } else {
	      $result[] = $filename;
	    }
	  }
	  return $result;
	}

	public function select(array $where = null,$options = null){

		$hashSelect = isset($options['hash'])?$options['hash']:null;
		$openFile = isset($options['open'])?$options['open']:true;
		$nocontent = isset($options['nocontent'])?$options['nocontent']:false;

		$collectionDir = $this->collection.DIRECTORY_SEPARATOR;
		$collectionDir = str_replace('//', '/', $collectionDir);

		if(!file_exists($collectionDir)){
			throw new \Exception('Collection not found: '.$this->collectionName);
			
			return false;
		}
		
		$resultArray = $this->scanAllDir($collectionDir);


		foreach ($resultArray as $key => $value)
			if($value == '.'  || $value == '..') unset($resultArray[$key]);
		
		$resultArray = array_values($resultArray);

		$remove = false;

		$index1 = 0;
		$resultFinish = [];

		foreach ($resultArray as $key => $value) {
		

			$filename = $this->collection.DIRECTORY_SEPARATOR.$value;

			

			$found = false;

			if($openFile === true){				
				$content = file_get_contents($filename);
				$contentObj = json_decode($content);
			}else{
				$hash = $value;
				$hash = str_replace('_jdoc.json', '', $hash);
				$contentObj = (object) array(
					'hash'=>$hash,
					'fields'=>(object) array()
				);		

			}

			if($hashSelect != null ){
				if($hashSelect.'_jdoc.json' == $value){
					$resultFinish[$contentObj->hash] = $contentObj->fields;
					$found = true;
				}				
				continue;
			}



			if($where == null){
				$found = true;
			}else{
			
				$operator = null;
				$index2 = 0;
				$combCond = [];
				$found = true; 
				foreach ($where as $key2 => $value2) {
					
					$operator = '';

					if( substr($key2, 0,3) == '||.'){
						$key2 = str_replace('||.', '', $key2) ;
						$operator = 'or';

					}else if( substr($key2, 0,3) == '&&.'){
						$key2 = str_replace('&&.', '', $key2) ;
						$operator = 'and';		
					}else{
						$operator = 'and'; 					
					}
		
					if(strpos($key2, '/') !==-1) $key2 = str_replace('/', '->', $key2);

					eval('$preval = isset($contentObj->fields->'.$key2.')?$contentObj->fields->'.$key2.':null;');

						
					if($preval === $value2){								
						$combCond[] = $operator;
					}else{
						if( substr($value2, 0,1) === '~'){
							if( soundex($preval) === soundex(substr($value2, 1))){
								$combCond[] = $operator;
							}else{
								$found = false;
							}
						}else if( substr($value2, 0,1) === '*'){
							if( strpos(strtolower($preval), substr(strtolower($value2), 1) ) !== false ){
								$combCond[] = $operator;
							}else{
								$found = false;
							}
						}else{
							$found = false;
						}	
					}
					$index2++;
				}

		
				if( array_search('or',$combCond) !== false )
					$found = true;
				
				if( $found === true ){
					if( (isset($contentObj->hash) && isset($contentObj->fields)) || $openFile !== true){
						if($nocontent === false){
							$resultFinish[$contentObj->hash] = $contentObj->fields;
						}else{						
							$resultFinish[$contentObj->hash] = array();
						}
					}
				}


			}



			$index1++;
			
		}

	
		if(count($resultFinish) > 0)
			return $resultFinish;
		else
			return false;
	}

	public function create($fields = null,$hash = null){

		if($hash == null){
			$hash = md5(json_encode($fields).''.time());			
		}
		
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

