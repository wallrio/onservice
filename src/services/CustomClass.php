<?php

namespace onservice\services;
use onservice\essentials\File as File;

class CustomClass{

	public $server = null;
	public $namespace = 'customclass';

	public function __construct($dir = null){

	
		$dirArray = File::findRecursive($dir);	
		$this->dirArray = $dirArray;

		foreach ($this->dirArray as $key => $value) {
			$key = substr($key, 1);
			$array = explode('/', $key);

			$pathClass = $dir.$value;
				$pathClass = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $pathClass);

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


			}

		}
		
	}	



}