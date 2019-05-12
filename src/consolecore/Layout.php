<?php

namespace onservice\consolecore;

use \onservice\consolecore\PrintConsole as PrintConsole;

class Layout extends PrintConsole{

	public static function header($newline = true,$title = null,$forecolor = null,$backcolor = null,$bold = null,$description = ''){
		
		 echo self::write("\n ".($title),array('bold'=>$bold,'backcolor'=>$backcolor,'forecolor'=>$forecolor));
		 
		 if($description !== ''){
		 	echo "\n";
		 	echo self::write(" ".($description),array());
		 }

		 if($newline === true)
		 echo "\n";
	}

	public static function footer(){
		echo "\n\n";
	}
	
}