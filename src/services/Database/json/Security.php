<?php

namespace onservice\services\Database\json;

class Security{

	function __construct($basedir,$basename){
		$this->basedir = $basedir;
		$this->basename = $basename;
	}

	public function apply(){
		$this->htaccess();
	}

	public function htaccess(){
		$data = ''
		 	 .'# Block directory list view'
			 ."\n".'Options -Indexes'
			 ."\n".'# Block the visualization of document JSON'
			 ."\n".'<Files "*_jdoc.json">'
			 ."\n".'Order Allow,Deny'
			 ."\n".'Deny from all'
			 ."\n".'</Files>'
			 .'';

		$filename = $this->basedir.DIRECTORY_SEPARATOR.'.htaccess';
		if(!file_exists($filename)) file_put_contents($filename, $data);
	}
}