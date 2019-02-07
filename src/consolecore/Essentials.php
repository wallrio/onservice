<?php

namespace onservice\consolecore;

class Essentials{

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
	
}