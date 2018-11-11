<?php

namespace onservice\services\Database\json;

class Essentials{

	public static function rrmdir($dir) { 
	   if (is_dir($dir)) { 
	     $objects = scandir($dir); 
	     foreach ($objects as $object) { 
	       		if ($object != "." && $object != "..") { 
	       		 	if (is_dir($dir."/".$object))
	       		    	self::rrmdir($dir."/".$object);
	       		  	else
	       		    	unlink($dir."/".$object); 
	       		} 
	    	}
	    	rmdir($dir); 
	    } 
	}

}