<?php

namespace onservice\services\Database\json;

class Collection{

	public function __construct($dir = null, $basename = null){
		
		$this->dir = $dir;
		$this->basename = $basename;

		$this->config = array(
			'dir'=>$dir,
			'basename'=>$basename,
		);	

	}

	public function createCollection($name){
		$collection = $this->dir.DIRECTORY_SEPARATOR.$this->basename.DIRECTORY_SEPARATOR.$name;

		if(!file_exists($collection)) mkdir($collection,0777,true);

		return (object) array(
			'document' => new Document($this->config,$name,$collection)
		);
	}

	

	public function deleteCollection($name){
		$collection = $this->dir.DIRECTORY_SEPARATOR.$this->basename.DIRECTORY_SEPARATOR.$name;

		if(file_exists($collection)){
			$dirArray = scandir($collection);
			foreach ($dirArray as $key => $value) {
				if($value == '.' || $value == '..') unset($dirArray[$key]);
			}
			$dirArray = array_values($dirArray);

			if(count($dirArray) < 1){
				Essentials::rrmdir($collection);
				return true;
			}
		}

		return false;
	}

	public function collection($name){
		$collection = $this->dir.DIRECTORY_SEPARATOR.$this->basename.DIRECTORY_SEPARATOR.$name;

		return (object) array(
			'document' => new Document($this->config,$name,$collection)
		);
		
	}
}