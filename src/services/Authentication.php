<?php

namespace onservice\services;


class Authentication{


	public function __construct($driver = null){
		$this->driver = $driver;	

		$argv = func_get_args();

		foreach ($argv as $key => $value) {
			if ( class_exists(get_class($this->driver)) == true	){			
				$namespace = $value->namespace;
				$this->$namespace = $value;
			}
		}

	}

	public function __call($method,$arguments){		
		
		if( method_exists($this->driver, $method) ){			
			return call_user_func_array(array($this->driver,$method), $arguments);
		}
		else{
			throw new \Exception("Method Not Exist: ".$method, 1);	
		}
	}
}