<?php

namespace onservice\services;

class Process{

	private $forkArray = array();
	private $parent_pid = null;

	public $namespace = 'process';
	
	public function __construct(){
		$this->parent_pid = getmypid();
		
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
					$result = $run($parameters,$pid);

				$array['parameters'] = $parameters;
				$array['run'] = $run;

			}
		}
	}

}


