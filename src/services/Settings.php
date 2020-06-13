<?php

/**
*	Reference
*	=========
*	The parsing method yml uses the 'mustangostang' library available on the MIT license at github.com
*	- repository: https://github.com/mustangostang/spyc
**/

namespace onservice\services;

use onservice\essentials\Http as HttpCon;
use onservice\essentials\File as File;

class Settings{
	
	public function __construct($dir = null,$mode = null){

		$this->repository = (object) array();

		if(
			strpos($dir, 'http:')!==false ||
			strpos($dir, 'https:')!== false 
		){	
				
			$file_parts = pathinfo($dir);
			$filename = $file_parts['filename'];
			$extension = isset($file_parts['extension'])?$file_parts['extension']:null;

			$this->repository->$filename = (object) array();

			$fileContent = HttpCon::request(array(
				'url'=>$dir,					
				'method'=>'get'
			));
			if($fileContent == false) return false;

			if($mode !== null) $extension = $mode;

			if($extension == 'json'){
				$fileContent = json_decode($fileContent);
			}else if($extension == 'yml'){

				require_once "settings/spyc/Spyc.php";
				$fileContent = \Spyc::YAMLLoad($fileContent);
				$fileContent = json_encode($fileContent);
				$fileContent = json_decode($fileContent);
			}
			
			if(is_object($fileContent) || is_array($fileContent))
			foreach ($fileContent as $key => $value) {
				$this->repository->$filename->$key = $value;
			}
			
			return;
		}

		if($dir == null) $dir = getcwd().DIRECTORY_SEPARATOR.'settings'.DIRECTORY_SEPARATOR;

		$dirArray = File::rscan($dir);

		if(is_array($dirArray) && count($dirArray)>0)
		foreach ($dirArray as $key => $value) {
			$filepath = $dir.$value;
			$fileContent = file_get_contents($filepath);

			$file_parts = pathinfo($filepath);

			$extension = $file_parts['extension'];
			
			if($extension == 'json'){
				$fileContent = json_decode($fileContent);
			}else if($extension == 'yml'){

				require_once "settings/spyc/Spyc.php";
				$fileContent = \Spyc::YAMLLoad($fileContent);
				$fileContent = json_encode($fileContent);
				$fileContent = json_decode($fileContent);
			}

			$namedir = $value;
			$namedirArray = explode('/', $namedir);
			$join = '';
			$join2 = '';
			$index=0;
	
			foreach ( $namedirArray as $key2 => $value2) {
				if( (count($namedirArray)-2) >= $index){
					
					if($join == '')
						$join.=$value2.'';						
					else
						$join.=$value2.'->';						
		
				}else{

					if($join == '')
						$join.=$value2.'';						
					else
						$join.='->'.$value2.'';	

				
				}
				
				if( !isset($this->$join)){
						
						
					$join = str_replace('.json', '', $join);						
					$join = str_replace('.yml', '', $join);						

					if($index == 0){
						if( (count($namedirArray)-1) == $index)
							eval('$this->repository->'.$join.' = $fileContent;');
						else
							$this->repository->$join =(object) array();
					}else{					
						eval('$this->repository->'.$join.' = $fileContent;');
					}

						
				}
	
				$index++;
			}
		
			

		}

		
	}

	
	

}