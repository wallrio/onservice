<?php

namespace onservice\essentials;

class File{
	
	
	public static function findRecursive($dir,$parent = '',$nivel = 0){
		$dirArray = scandir($dir);
		$newArray = [];
		
		foreach ($dirArray as $key => $value) {
			if($value == '.' || $value == '..') unset($dirArray[$key]);			
		}
		foreach ($dirArray as $key => $value) {
			if(is_dir($dir.DIRECTORY_SEPARATOR.$value)){
				if(substr($value, 0,1)=='_')continue;
				$newArray2 = self::findRecursive($dir.DIRECTORY_SEPARATOR.$value,$parent.'/'.$value,$nivel+1);
				$newArray = array_merge($newArray,$newArray2);		
			}else{
				if(substr($value, 0,1)=='_')continue;
				$valueName = $value;			
				$valueName = $parent.'/'.$value;

				$valueName = strtolower($valueName);
				$valueName = str_replace('.php', '', $valueName);
				$newArray[$valueName] = $parent.'/'.$value;
			}
		}
		return $newArray;
	}

	/**
	 * [capture directories and files recursively]
	 * @param  [string] $dir [directory to be captured]
	 * @return [array]      [a list containing the captured directories]
	 */
	
	public static function rscan($dir) {
	  if(!file_exists($dir)) return false;
	  $result = [];
	  foreach(scandir($dir) as $filename) {
	    if ($filename[0] === '.') continue;
	    $filePath = $dir . '/' . $filename;
	    if (is_dir($filePath)) {
	      foreach (self::rscan($filePath) as $childFilename) {
	        $result[] = $filename . '/' . $childFilename;
	      }
	    } else {
	      $result[] = $filename;
	    }
	  }
	  return $result;
	}

	/* Copy recursive files/directory */
	public static function rcopy($src,$dst) { 
	    $dir = opendir($src); 
	    @mkdir($dst); 
	    while(false !== ( $file = readdir($dir)) ) { 
	        if (( $file != '.' ) && ( $file != '..' )) { 
	            if ( is_dir($src . '/' . $file) ) { 
	                self::rcopy($src . '/' . $file,$dst . '/' . $file); 
	            } 
	            else { 
	                copy($src . '/' . $file,$dst . '/' . $file); 
	            } 
	        } 
	    } 
	    closedir($dir); 
	}

	/* remove recursive files/directory */
	public static function rrmdir($dir) { 
	   if (is_dir($dir)) { 
	     $objects = scandir($dir); 
	     foreach ($objects as $object) { 
	       if ($object != "." && $object != "..") { 
	         if (is_dir($dir."/".$object))
	           self::rrmdir($dir."/".$object);
	         else
	           @unlink($dir."/".$object); 
	       } 
	     }
	     @rmdir($dir); 
	   } 
	 }
}