<?php

namespace onservice\essentials;

class File{
	
	
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