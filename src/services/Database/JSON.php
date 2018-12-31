<?php

namespace onservice\services\Database;

use onservice\services\Database\json\Essentials as Essentials;
use onservice\services\Database\json\Document as Document;
use onservice\services\Database\json\Collection as Collection;
use onservice\services\Database\json\Security as Security;

class JSON {


	public function __construct($dir = null, $basename = null){
		
		$this->dir = $dir;
		$this->basename = $basename;

		$this->config = array(
			'dir'=>$dir,
			'basename'=>$basename,
		);	

	}

	public function createRepository(){
		$dir = $this->dir;
		if($dir == null){
			$dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'onservice'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'json'.DIRECTORY_SEPARATOR;				
		}

		if(!file_exists($dir))
			mkdir($dir,0777,true);		

		$dir = str_replace('//', '/', $dir);


	}

	public function config(array $parameters = null){
		
		if(!isset($parameters)){
			return $this->config; 
		}
		$this->config = $parameters;
		if(is_array($parameters) && count($parameters)>0)
		foreach ($parameters as $key => $value) {
			$this->{$key} = $value;	
		}

	}



	public function base($base){
		$this->createRepository();
		$this->basename = $base;

		return new Collection($this->dir,$this->basename);
	}


	public function createBase($basename){
		$this->createRepository();

		$basedir = $this->dir.DIRECTORY_SEPARATOR.$basename;
		$basedir = str_replace('//', '/', $basedir);
		if(!file_exists($basedir)) mkdir($basedir,0777,true);

		$security = new Security($basedir,$basename);		
		$security->apply();

		$this->basename = $basename;

		return new Collection($this->dir,$basename);
	}



}