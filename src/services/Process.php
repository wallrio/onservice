<?php

namespace onservice\services;

use onservice\services\process\StreamProcess as StreamProcess;
use onservice\services\process\MemoryProcess as MemoryProcess;

class Process{

	public $server = null;
	private $forkArray = array();
	private $parent_pid = null;
	public $memory = null;

	public $namespace = 'process';
	
	public function __construct(){
		$this->parent_pid = getmypid();

		$this->memory = new MemoryProcess;
		
		
	}
	
	public function isJson($string) {
	 json_decode($string);
	 return (json_last_error() == JSON_ERROR_NONE);
	}

	public function fork($array){

		$run = isset($array['run'])?$array['run']:null;
		$parameters = isset($array['parameters'])?$array['parameters']:array();
		$parent = isset($array['parent'])?$array['parent']:null;
		


    	if(getmypid() == $this->parent_pid){
			$pid = pcntl_fork();
	    	
			if ($pid == -1) {
			     die('could not fork');
			} else if ($pid) {

				$pidParent = posix_getpid();

				if(isset($parent))
					$result = $parent($parameters,$this->memory, $this->server,$pidParent,$pid);

				
			} else {				
				$pidParent = posix_getppid();
				$pidChild = posix_getpid();
				

				$result = false;
				if(isset($run))
					$result = $run($parameters,$this->memory, $this->server,$pidChild,$pidParent);


				$array['parameters'] = $parameters;
				$array['run'] = $run;

				if($result != true)
				posix_kill($pidChild, SIGTERM);

			}
		}
	}

}


