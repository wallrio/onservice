<?php


namespace onservice; 

define('OnServiceVersion','1.3.0');

class CreateServer{
	
	private $serverList;
	public $version = OnServiceVersion;


	public function __construct($server = null){


		$numargs = func_get_args();
		$this->serverList = $numargs;	
		foreach ($numargs as $key => $value) {
			$serverCurrent = $value;

			if( isset($serverCurrent->namespace) ){
				$namespace = $serverCurrent->namespace;
				$this->$namespace = $value;
				$serverCurrent->server = $this;
				$serverCurrent->version = $this->version;
			}
		}
	}
	
	public function __get($name) {

		foreach ($this->serverList as $key => $value) {				
			if( isset($value->namespace) ){
				return $value->$name;
			}else{
				die('Method not exist ['.$method.']');				
			}
		}
		
    }

	public function __call($method,$arguments){		
		
		foreach ($this->serverList as $key => $value) {				
			if( !isset($value->namespace) ){
				return call_user_func_array(array($value,$method), $arguments);
			}else{
				die('Method not exist ['.$method.']');				
			}
		}	
	}
	
}