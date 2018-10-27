<?php

namespace onservice\services\Process;

class MemoryProcess {
	
	public function save($data = null,$index = 0){		
		$SHM_KEY = ftok(__FILE__, chr( 4 ) ); 
		$id =  shm_attach($SHM_KEY, 1024, 0666);
		shm_put_var($id, $index, $data);
	}

	public function load($index = 0){
		$SHM_KEY = ftok(__FILE__, chr( 4 ) ); 
		$id =  shm_attach($SHM_KEY, 1024, 0666);		
		
		if(shm_has_var ( $id , $index ))
			return shm_get_var($id, $index);
		else
			return false;
		
	}

	public function clear($index = 0){
		$SHM_KEY = ftok(__FILE__, chr( 4 ) ); 
		$id =  shm_attach($SHM_KEY, 1024, 0666);
		shm_put_var($id, $index, '');		
		shm_remove_var($id,$index);		
	}

	public function destroy(){
		$SHM_KEY = ftok(__FILE__, chr( 4 ) ); 
		$id =  shm_attach($SHM_KEY, 1024, 0666);		
		shm_remove($id);		
	}
	
}