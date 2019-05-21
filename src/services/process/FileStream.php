<?php

namespace onservice\services\process;

use onservice\essentials\File as File;

class FileStream {
	
	private $type = 'file';
	private $id = null;

	function __construct(){
		$this->id = uniqid();
	}

	public function getType(){
		return $this->type; 
	}
	
	public function save($data, $index = 0, $id = null){				
		if($id === null) $id = $this->id;	
		$temp_dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'onservice-fork'.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR;

		if(!file_exists($temp_dir))@mkdir($temp_dir,0777,true);

		$temp_file = $temp_dir.$index;	

		file_put_contents($temp_file, $data);
	}

	public function load($index = 0, $id = null){	
		if($id === null) $id = $this->id;	
		$temp_file = sys_get_temp_dir().DIRECTORY_SEPARATOR.'onservice-fork'.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.$index;
		if(file_exists($temp_file)){
			$result = file_get_contents($temp_file);
			return $result;
		}else{
			return false;
		}
	}

	public function get($index = 0, $id = null){
	
		if($id === null) $id = $this->id;	

		$temp_file = sys_get_temp_dir().DIRECTORY_SEPARATOR.'onservice-fork'.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.$index;

		if(file_exists($temp_file)){
			$result = @file_get_contents($temp_file);						
			@unlink($temp_file);			
			if($result !== false){				
				return $result;
			}else{
				return false;				
			}
		}else{
			return false;
		}
	}

	public function clean($index = 0, $id = null){
		if($id === null) $id = $this->id;	
		$temp_file = sys_get_temp_dir().DIRECTORY_SEPARATOR.'onservice-fork'.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.$index;
		if(file_exists($temp_file))
		unlink($temp_file);
	}

	public function destroy($id = null){
		if($id === null) $id = $this->id;	
		$temp_file = sys_get_temp_dir().DIRECTORY_SEPARATOR.'onservice-fork'.DIRECTORY_SEPARATOR.$id;
		File::rrmdir($temp_file);	
	}
}

