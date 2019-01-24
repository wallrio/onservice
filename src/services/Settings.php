<?php

/**
*	Reference
*	=========
*	The parsing method yml uses the 'mustangostang' library available on the MIT license at github.com
*	- repository: https://github.com/mustangostang/spyc
**/

namespace onservice\services;

class Settings{
	
	public $server = null;
	public $namespace = 'settings';


	public static function curl($url){
     
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $url,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_ENCODING => "",
              CURLOPT_TIMEOUT => 30,

            ));
   

            $response = curl_exec($curl);
            $err = curl_error($curl);
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

        if ($http_status==404)return false;
        
        if($err) return false;
        
        return $response;
        


    }

	public function __construct($dir = null){

		if(
			strpos($dir, 'http:')!==false ||
			strpos($dir, 'https:')!== false 
		){	
				
			$file_parts = pathinfo($dir);
			$extension = $file_parts['extension'];
			$fileContent = $this->curl($dir);
			if($extension == 'json'){
				$fileContent = json_decode($fileContent);
			}else if($extension == 'yml'){

				require_once "Settings/spyc/Spyc.php";
				$fileContent = \Spyc::YAMLLoad($fileContent);
				$fileContent = json_encode($fileContent);
				$fileContent = json_decode($fileContent);
			}
			
			if(count($fileContent)>0)
			foreach ($fileContent as $key => $value) {
				$this->$key = $value;
			}
			
			return;
		}

		if($dir == null) $dir = getcwd().DIRECTORY_SEPARATOR.'settings'.DIRECTORY_SEPARATOR;

		$dirArray = $this->scanAllDir($dir);

		foreach ($dirArray as $key => $value) {
			$filepath = $dir.$value;
			$fileContent = file_get_contents($filepath);

			$file_parts = pathinfo($filepath);

			$extension = $file_parts['extension'];
			
			if($extension == 'json'){
				$fileContent = json_decode($fileContent);
			}else if($extension == 'yml'){

				require_once "Settings/spyc/Spyc.php";
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
							eval('$this->'.$join.' = $fileContent;');
						else
							$this->$join =(object) array();
					}else{					
						eval('$this->'.$join.' = $fileContent;');
					}

						
				}
	
				$index++;
			}
		
			

		}

		
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

}