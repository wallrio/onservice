<?php


namespace onservice; 

define('OnServiceVersion','2.0.4');

class CreateServer{
	
	private $serverList = [];
	public $version = OnServiceVersion;


	public function __construct($server = null){
		$numargs = func_get_args();
		foreach ($numargs as $key => $value) {
			$this->attachService($value);
		}
	}

	public function attachService($driver){
		array_push($this->serverList, $driver);		

		if( !is_callable( $driver ) && isset($driver->namespace)  ){
			$namespace = $driver->namespace;
			$this->$namespace = $driver;
			$driver->server = $this;				
			$driver->version = $this->version;
			$className = get_class($driver);
			$className = explode('\\', $className);
			$className = end($className);	

			if(method_exists($driver, '_init'))
			$driver->_init($this);					
		}
	
	}
	
	public function __get($name) {

		foreach ($this->serverList as $key => $value) {				
			if( isset($value->namespace) ){
				return $value->$name;
			}else{
				die('Attribute not exist ['.$name.']');				
			}
		}
		
    }

	public function __call($method,$arguments){		
		
		foreach ($this->serverList as $key => $value) {				
			if( isset($value->namespace) ){			
				$value->server = $this;				
				$value->version = $this->version;
				if(method_exists($value, $method))
				return call_user_func_array(array($value,$method), $arguments);
			}else{
				die('Method not exist ['.$method.']');				
			}
		}	
	}
	
}