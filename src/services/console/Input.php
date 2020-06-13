<?php

namespace onservice\services\console;

use onservice\consolecore\PrintConsole as PrintConsole;

class Input{
	
	function __construct(){}
	
	public static function questions(array $questions = null,array $parameters = array(),$try = false){

		$responses = [];
		foreach ($questions as $key => $value) {
			$id = isset($value['id'])?$value['id']:$key;
			$question = isset($value['question'])?$value['question']:null;
			$options = isset($value['options'])?$value['options']:null;
			$default = isset($value['default'])?$value['default']:null;
			$depend = isset($value['depend'])?$value['depend']:null;
			$required = isset($value['required'])?$value['required']:false;

			$next = [];
			if(is_array($depend)){				
				$next = $depend;
				foreach ($depend as $key2 => $value2) {
					if($responses[$key2] == $value2){
						unset($next[$key2]);
					}	
				}
			}
			if(count($next) < 1 || $depend === null)
			$responses[$id] = self::question($question,$default,$required,$options,$parameters);
		}

		return $responses;
	}

	public static function question($string,$default = null,$required = false,array $response = null,array $parameters = array(),$try = false){

		
		echo " ".PrintConsole::write($string,array('bold'=>false,'forecolor'=>'yellow'));
		if($response !== null){

			echo " [";
			$index = 0;		
			foreach ($response as $key => $value) {
				
				if(gettype($value) === 'string'){
					$option = $value;
				}else{
					$option = $key;
				}

				if($default !== null){
					
					if($default === $option){
						echo PrintConsole::write($option,array('bold'=>true,'forecolor'=>'purple'));
					}else{
						echo PrintConsole::write($option,array('bold'=>false,'forecolor'=>'purple'));
					}
				}else{
					
					echo PrintConsole::write($option,array('bold'=>false,'forecolor'=>'purple'));
				}

				if($index < count($response)-1)
					echo PrintConsole::write('/',array('bold'=>false,'forecolor'=>'purple'));
				$index++;
			}
			echo "]";
		}

		if($default !== null && $response === null){
			echo ' (';
			echo PrintConsole::write($default,array('bold'=>false,'forecolor'=>'purple'));
			echo ')';
		}

		if($response === null){
			echo ":";		
		}
		echo " ";		

		$line = trim(fgets(STDIN)); // reads one line from STDIN

		if($response !== null && $default === null){			
			if(isset($response[$line])){
				return $response[$line]($parameters);
			}else{
				return self::question($string,$default,$required,$response,$parameters,true);
			}
		}else{
			if($line==''){

				if($required === true)
					return self::question($string,$default,$required,$response,$parameters,true);

				if(isset($response[$default]))
					return $response[$default]($parameters);
				else
					return $default;
			}else{
				if(isset($response[$line]) || $response === null ){
					if(isset($response[$line]))
						return $response[$line]($parameters);
					else
						return $line;
				}else{					
					return self::question($string,$default,$required,$response,$parameters,true);		
				}
			}
		}

	}
}

