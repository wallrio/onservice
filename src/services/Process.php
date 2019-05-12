<?php

namespace onservice\services;

use onservice\services\process\MemoryProcess as MemoryProcess;

class Process{
	
	public 
		$namespace = 'process',
		$server = null,
		$memory = null,
		$stream = null,
		$finishMethod = null;

	public static 
		$forkChilds = [],
		$forkResponses = [];

	private 
		$forkArray = array(),
		$parent_pid = null;
			
	public function __construct(){
		$this->parent_pid = getmypid();
		$this->stream = new MemoryProcess;
	}
	


	public function streamType($instance) {
		$this->stream = $instance;
	}

	public function fork($method = null,$parameters = null) {
		$pid = pcntl_fork();
		if ($pid == -1) {	  	
			// code_for_failed_to_launch_child_process();
		}else if ($pid == 0) {
			$pidChild = posix_getpid();
			if(isset($method)){

				$result = $method($parameters,$pidChild,$this->stream,$this,$this->server);
				if($result !== null){					
					$this->stream->save(json_encode(array('pid'=>$pidChild,'return'=>$result,'parameters'=>$parameters)),'.return-'.$pidChild);
				}
			}
			// code_for_child_process();
			exit(); // Make sure to exit.
		}else {
			self::$forkChilds[$pid] = $pid;	  
			return $pid;
		}
	}



	public function callbackAll($method = null){	

		while (TRUE) {
			$pid = pcntl_wait($status, WNOHANG);
			usleep(100);
			if ($pid > 0) {	
				if($method !== null){				
					$returnFork = $this->stream->load('.return-'.$pid);
					usleep(100);
					$returnFork = json_decode($returnFork);
					self::$forkResponses[$pid] = $returnFork;
				}
				// when child if done		  		
				unset(self::$forkChilds[$pid]);
				if(count(self::$forkChilds)<1){		  		
					break;
				}
			}		  			
		}
		$response = $method(self::$forkResponses,$this->stream,$this,$this->server);

		return $response;
	}

	
	public function callback($method = null){

		$response = [];

		while (TRUE) {
			$pid = pcntl_wait($status, WNOHANG);
			usleep(100);
			if ($pid > 0) {	
				if($method !== null){				
					$returnFork = $this->stream->load('.return-'.$pid);
					usleep(100);
					$returnFork = json_decode($returnFork);
					self::$forkResponses[$pid] = $returnFork;
					$response[$pid] = $method($returnFork,$this->stream,$this,$this->server);
				}
				// when child if done		  		
				unset(self::$forkChilds[$pid]);
				if(count(self::$forkChilds)<1){		  		
					break;
				}
			}		  			
		}

		return $response;
	}


	public function while($method = null){	
		while (TRUE) {
			$pid = pcntl_wait($status, WNOHANG);
			usleep(100);
			if ($pid > 0) {						
				$returnFork = $this->stream->load('.return-'.$pid);
				usleep(100);
				$returnFork = json_decode($returnFork);
				self::$forkResponses[$pid] = $returnFork;
				unset(self::$forkChilds[$pid]);
				$response = $method(self::$forkResponses[$pid],$this->stream,$this,self::$forkChilds,$this->server);
			}else{
				$response = $method(null,$this->stream,$this,self::$forkChilds,$this->server);
			}	  
			if(!empty($response)){
				break;
			}			
		}

		return $response;
	}


}


