<?php
/**
*	Credits:
*	the Zip extract, uses the zip library of 'alexcorvi', available in https://github.com/alexcorvi/php-zip
*
**/

namespace onservice\essentials;

require_once "zip/src/Zip.php";

class Zip{	

	/**
	 * [extract content from .zip files]
	 * @param  [type] $filePath    [path of file .zip]
	 * @param  [type] $extractPath [directory to extract files]
	 * @return [null]              
	 */
	public static function extract($filePath, $extractPath){
	    if(!file_exists($extractPath)) mkdir($extractPath,0777,true);
	    $zip = new ZipCss();
	    $zip->unzip_file($filePath);
	    $zip->unzip_to($extractPath);
	  }

}