<?php

namespace onservice\services;
use onservice\essentials\File as File;

class CustomClass{

	public $server = null;
	public $namespace = 'customclass';
	// public $dirArray = null;
	
	public function __construct($dir = null){

	
		$dirArray = File::findRecursive($dir);	
		$this->dirArray = $dirArray;

		foreach ($this->dirArray as $key => $value) {
			$key = substr($key, 1);
			$array = explode('/', $key);

			$pathClass = $dir.$value;
				$pathClass = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $pathClass);

			// echo $pathClass;
			require $pathClass;


			$join = '';
			$join2 = '';
			foreach ($array as $key2 => $value2) {
				if($key2 >0){
					if( $key2 < count($array) -1 ){
						$join .= '->'.$value2;
						$join2 .= $value2.'\\';
					}else{
						$join .= '->'.ucfirst($value2);
						$join2 .= ucfirst($value2).'';
					}
				}
				else{
					if( $key2 < count($array) -1 ){
						$join .= ''.$value2;
						$join2 .= $value2.'\\';
					}else{
						$join .= ''.ucfirst($value2);
						$join2 .= ucfirst($value2).'';
					}

				}

/*

				if( $key2 < count($array) -1 )
					eval('$this->'.$join.' = (object) array();');
				else
					eval('$this->'.$join.' = new CustomClass\\'.$join2.';');
*/
			}

		}
		
	}	


	public function __call($method,$arguments){		
			// return  $method;
		// print_r($this->dirArray);

		// foreach ($this->dirArray as $key => $value) {				
			/*if( !isset($value->namespace) ){
				return call_user_func_array(array($value,$method), $arguments);
			}else{
				die('Method not exist ['.$method.']');				
			}*/
		// }	
	}

}