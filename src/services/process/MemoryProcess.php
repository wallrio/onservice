<?php

namespace onservice\services\process;

class MemoryProcess {

	private $type = 'memory';
	private $bufferSize = 16000000; // 16 Mb
	private $permission = 0666;
	private $identifier = 6;
	
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

	public function save($data,$index = 0, $id = null){		
		if($id === null) $id = $this->identifier;	
		$index = ord($index);		
		$id = ord($id);		
		$SHM_KEY = ftok(__FILE__, chr( $id ) ); 
		$idResult =  shm_attach($SHM_KEY, $this->bufferSize, $this->permission);		
		shm_put_var($idResult, $index, $data);		
	}

	public function load($index = 0, $id = null){
		if($id === null) $id = $this->identifier;	
		$index = ord($index);
		$id = ord($id);		
		$SHM_KEY = ftok(__FILE__, chr( $id ) ); 
		$idResult =  shm_attach($SHM_KEY, $this->bufferSize, $this->permission);		
		

		if(shm_has_var ( $idResult , $index )){			
			$result = shm_get_var($idResult, $index);			
			return $result;
		}else{
			return false;	
		}	
	}

	public function get($index = 0, $id = null){
		if($id === null) $id = $this->identifier;	
		$index = ord($index);
		$id = ord($id);		
		$SHM_KEY = ftok(__FILE__, chr( $id ) ); 
		$idResult =  shm_attach($SHM_KEY, $this->bufferSize, $this->permission);		
		

		if(shm_has_var ( $idResult , $index )){			
			$result = shm_get_var($idResult, $index);
			$this->clean($index,$id);
			return $result;
		}else{
			return false;	
		}
	}

	public function clean($index = 0, $id = null){
		
		if($id === null) $id = $this->identifier;	
		if(getType($id)!=='integer') $id = ord($id);		

		$SHM_KEY = ftok(__FILE__, chr( $id ) ); 
		$idResult =  shm_attach($SHM_KEY, $this->bufferSize, $this->permission);
		
		shm_put_var($idResult, $index, '');		
		shm_remove_var($idResult,$index);		
	}

	public function destroy(){
		if($id === null) $id = $this->identifier;
		$SHM_KEY = ftok(__FILE__, chr( $id ) ); 
		$id =  shm_attach($SHM_KEY, $this->bufferSize, $this->permission);		
		shm_remove($id);		
	}
	
}