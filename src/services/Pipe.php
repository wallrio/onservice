<?php

namespace onservice\services;


class Pipe{

	public $server = null;
	public $namespace = 'pipe';
	private $listPipes = [];
	private $previousValue = '';

	public function __construct(){
		
	}

	
	// first call after contructor
	public function _init($server){}

	public function pipeAdd( $value = null ){		
		if($value == null) return $this->server;

		
		if(  gettype($this->previousValue) !== 'array' && gettype($value) !== 'array' ){
			$this->previousValue .= $value;
		}

		if( gettype($this->previousValue) == 'array' ){
			if(is_array($value) && count($value)>0){
				foreach ($value as $key2 => $value2) {
					if(isset($this->previousValue[$key2])){
						$original = $this->previousValue[$key2];
						if(gettype($original)==='array'){						
							array_push($this->previousValue[$key2], $value2);
						}else{
							$this->previousValue[$key2] = [$original,$value2];
						}
					}else{
						$this->previousValue[$key2] = $value2;
					}
				}
			}else{
				array_push($this->previousValue, $value);
			}
		}
			
		

		return $this->server;
	}

	public function pipeService($driver = null){
		$className = get_class($driver);
		$className = explode('\\', $className);
		$className = end($className);				

		$this->listPipes[$className] = $driver;	
		$this->attachService($driver);
		return $this->server;
	}

	public function Pipe($driver = null,array $parameters = array() ){



		if( is_callable( $driver ) ){
			$response = $this->previousValue;
			$this->previousValue = $driver($response, $this->server);	
			return $this->server;
		}else if(gettype($driver) !== 'object'){
			$this->previousValue = $driver;
			return $this->server;
		}

		$this->driverCurrent = $driver;		

		$className = get_class($driver);
		$className = explode('\\', $className);
		$className = end($className);				

		$this->listPipes[$className] = $driver;	
	
		$this->server->listPipes = (object) $this->listPipes;

		if(method_exists($driver, 'pipe'))
		$this->previousValue = $driver->pipe($this->previousValue);
		

		return $this->server;
	}

	public function attachService($driver){
		$this->server->attachService($driver);
	}

	public function response(){
		return $this->previousValue;
	}

}