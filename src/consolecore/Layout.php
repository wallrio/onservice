<?php

namespace onservice\consolecore;

use \onservice\consolecore\PrintConsole as PrintConsole;

class Layout extends PrintConsole{

	public static function header($newline = true,$title = null,$forecolor = null,$backcolor = null,$bold = null,$description = ''){
		
		 echo self::write("\n ".self::fixedStringSize($title),array('bold'=>$bold,'backcolor'=>$backcolor,'forecolor'=>$forecolor));
		 

		 if($description !== ''){
		 	echo "\n";
		 	echo self::write(" ".self::fixedStringSize($description),array());
		 	// echo "\n";
		 }else{
		 	// echo "\n";
		 	// echo "\n";

		 }

		 if($newline === true)
		 echo "\n";
	}

	public static function footer(){
		echo "\n\n";
	}
	
}