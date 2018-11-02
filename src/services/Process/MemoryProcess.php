<?php

namespace onservice\services\Process;

class MemoryProcess {

	private $bufferSize = 16000000;
	private $permission = 0666;
	
	
	public function setBuffer($size = 8192){		
		$this->bufferSize = $size;
	}

	public function setPermission($permission = 0666){		
		$this->permission = $permission;
	}

	public function save($data = null,$index = 0){		
		$SHM_KEY = ftok(__FILE__, chr( 4 ) ); 
		$id =  shm_attach($SHM_KEY, $this->bufferSize, $this->permission);
		shm_put_var($id, $index, $data);
	}

	public function load($index = 0){
		$SHM_KEY = ftok(__FILE__, chr( 4 ) ); 
		$id =  shm_attach($SHM_KEY, $this->bufferSize, $this->permission);		
		
		if(shm_has_var ( $id , $index ))
			return shm_get_var($id, $index);
		else
			return false;	
	}

	public function clear($index = 0){
		$SHM_KEY = ftok(__FILE__, chr( 4 ) ); 
		$id =  shm_attach($SHM_KEY, $this->bufferSize, $this->permission);
		shm_put_var($id, $index, '');		
		shm_remove_var($id,$index);		
	}

	public function destroy(){
		$SHM_KEY = ftok(__FILE__, chr( 4 ) ); 
		$id =  shm_attach($SHM_KEY, $this->bufferSize, $this->permission);		
		shm_remove($id);		
	}
	
}