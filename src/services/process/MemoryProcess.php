<?php

namespace onservice\services\process;

class MemoryProcess {

	private $type = 'memory';
	private $bufferSize = 16000000; // 16 Mb
	private $permission = 0666;
	private $identifier = 5;
	
	function __construct(){
		$this->identifier = rand(1, 15);
	}

	public function getType(){
		return $this->type; 
	}

	public function setIdentifier($identifier){		
		$this->identifier = $identifier;
	}
	
	public function setBuffer($size = 8192){		
		$this->bufferSize = $size;
	}

	public function setPermission($permission = 0666){		
		$this->permission = $permission;
	}

	public function save($data = null,$index = 0){		
		$index = ord($index);		
		$SHM_KEY = ftok(__FILE__, chr( $this->identifier ) ); 
		$id =  shm_attach($SHM_KEY, $this->bufferSize, $this->permission);
		shm_put_var($id, $index, $data);		
	}

	public function load($index = 0){
		$index = ord($index);
		$SHM_KEY = ftok(__FILE__, chr( $this->identifier ) ); 
		$id =  shm_attach($SHM_KEY, $this->bufferSize, $this->permission);		
		
		if(shm_has_var ( $id , $index ))
			return shm_get_var($id, $index);
		else
			return false;	
	}

	public function clear($index = 0){
		$SHM_KEY = ftok(__FILE__, chr( $this->identifier ) ); 
		$id =  shm_attach($SHM_KEY, $this->bufferSize, $this->permission);
		shm_put_var($id, $index, '');		
		shm_remove_var($id,$index);		
	}

	public function destroy(){
		$SHM_KEY = ftok(__FILE__, chr( $this->identifier ) ); 
		$id =  shm_attach($SHM_KEY, $this->bufferSize, $this->permission);		
		shm_remove($id);		
	}
	
}