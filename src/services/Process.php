<?php

namespace onservice\services;

use onservice\services\Process\StreamProcess as StreamProcess;
use onservice\services\Process\MemoryProcess as MemoryProcess;

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
	
	public function fork($array){

		$run = isset($array['run'])?$array['run']:null;
		$parameters = isset($array['parameters'])?$array['parameters']:array();
		


    	if(getmypid() == $this->parent_pid){
			$pid = pcntl_fork();
	    	
			if ($pid == -1) {
			     die('could not fork');
			} else if ($pid) {
			} else {
				$result = false;
				if(isset($run))
					$result = $run($parameters,$this->memory, $this->server);

				$array['parameters'] = $parameters;
				$array['run'] = $run;

			}
		}
	}

}


