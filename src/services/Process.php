<?php

namespace onservice\services;

use onservice\services\process\MemoryProcess as MemoryProcess;

class Process{
	
	public 
		$namespace = 'process',
		$server = null,
		$memory = null,
		$stream = null,
		$id = null,
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
		$this->id = uniqid();
	}
	

	/**
	 * change type of stream 
	 * @param  [instance of class] $instance [change the o method of stream]
	 * @return null
	 */
	public function streamType($instance) {
		$this->stream = $instance;
	}

	public function setIdentifier($identifier){		
		$this->id = $identifier;
	}

	/**
	 * [saves information for communication between processes]
	 * @param  [type]  $data  [description]
	 * @param  [integer/string] $index [identification of process]
	 * @return null
	 */
	public function save($data, $index = 0,$id = null){
		if($id === null) $id = $this->id;	
		$this->stream->save($data, $index, $id);
	}

	/**
	 * [capture information for communication between processes]
	 * @param  [integer/string] $index [identification of process]
	 * @return [any]         [content of stream]
	 */
	public function load($index = 0,$id = null){		
		if($id === null) $id = $this->id;	
		return $this->stream->load($index,$id);
	}

	/**
	 * [capture information for communication between processes and erases later]
	 * @param  [integer/string] $index [identification of process]
	 * @return [any]         [content of stream]
	 */
	public function get($index = 0,$id = null){

		if($id === null) $id = $this->id;	
		return $this->stream->get($index,$id);
	}

	/**
	 * [clean the record of a process]
	 * @param  [integer/string] $index [identification of process]
	 * @return null        
	 */
	public function clean($index = 0, $id = null) {
		if($id === null) $id = $this->id;	
		$this->stream->clean($index, $id);
	}

	/**
	 * [destroy all process]
	 * @return null
	 */
	public function destroy() {
		$this->stream->destroy();
	}

	
	/**
	 * [creates a process for asynchronous calls]
	 * @param  [type]  $method         [function to be executed asynchronously, the return will be passed to the methods o retorno será passado para os metodos callback,callbackAll,while]
	 * @method   [function($parameters, $pidChild, $streamClass,$contextProcess,$serverClass)]
	 * @param  [array]  $parameters     [values to be passed into the process]
	 * @param  boolean $monitorProcess [if set to false, the process is not counted in the listing]
	 * @return [integer/string]        [number of process]
	 */
	public function fork($method = null,array $parameters = null,$monitorProcess = true) {		
		if(getmypid() == $this->parent_pid){
			$pid = pcntl_fork();
			if ($pid == -1) {	 
				die('process fork not working'); 					
			}else if ($pid == 0) {
				$pidChild = posix_getpid();
				if(isset($method)){
					$result = $method($parameters,$pidChild,$this->stream,$this,$this->server);
					if($result !== null){				
						$this->stream->save(json_encode(array('pid'=>$pidChild,'return'=>$result,'parameters'=>$parameters)),'.return-'.$pidChild,$this->id);
					}
				}
				exit();
			}else {
				if($monitorProcess === true)
				self::$forkChilds[$pid] = $pid;	  
				return $pid;
			}
		}
	}


	/**
	 * [runs only when all processes are finalized]
	 * @param  [function] $method [function($responseFromChilds, $streamClass, $forkChildsList, $contextProcess, $serverClass)]
	 * @return [any]        
	 */
	public function callbackAll($method = null){	

		while (TRUE) {
			$pid = pcntl_wait($status, WNOHANG);
			usleep(100);
			if ($pid > 0) {	
				if($method !== null){				
					$returnFork = $this->stream->get('.return-'.$pid,$this->id);
					// $returnFork = $this->stream->load('.return-'.$pid);
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
		$response = $method(self::$forkResponses,$this->stream,self::$forkChilds, $this, $this->server);

		$this->stream->destroy();

		return $response;
	}

	/**
	 * [is executed when there is some value received by a process]
	 * @param  [function] $method [function($responseFromChilds, $streamClass, $forkChildsList, $contextProcess, $serverClass)]
	 * @return [any]        
	 */
	public function callback($method = null){

		$response = [];

		while (TRUE) {
			$pid = pcntl_wait($status, WNOHANG);
			usleep(100);
			if ($pid > 0) {	
				if($method !== null){				
					$returnFork = $this->stream->get('.return-'.$pid,$this->id);					
					usleep(100);
					$returnFork = json_decode($returnFork);
					self::$forkResponses[$pid] = $returnFork;
					$response[$pid] = $method($returnFork,$this->stream,self::$forkChilds,$this, $this->server);
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

	/**
	 * [creates an infinite loop that responds to the returns of the fork]
	 * @param  [function] $method [function($responseFromChilds, $streamClass, $forkChildsList, $contextProcess, $serverClass)]
	 * @return [any]        [return any value to end the loop]
	 */
	public function while($method = null){	
		while (TRUE) {
			$pid = pcntl_wait($status, WNOHANG);
			usleep(100);
			if ($pid > 0) {						

				$returnFork = $this->stream->get('.return-'.$pid,$this->id);

				usleep(100);
				$returnFork = json_decode($returnFork);
				self::$forkResponses[$pid] = $returnFork;
				unset(self::$forkChilds[$pid]);
				$response = $method(self::$forkResponses[$pid],$this->stream, self::$forkChilds, $this, $this->server);

				
			}else{
				$response = $method(null,$this->stream,self::$forkChilds, $this, $this->server);
			}	  
			if(!empty($response)){
				break;
			}			
		}

		return $response;
	}


}


