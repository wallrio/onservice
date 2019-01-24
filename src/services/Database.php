<?php

namespace onservice\services;
use onservice\services\Database\InterfaceDatabase as InterfaceDatabase;

class Database{

	public $server = null;
	public $namespace = 'database';

	public function __construct($driver){
		$this->driver = $driver;		
	}

	public function __call($method,$arguments){		
		if( method_exists($this->driver, $method) )
			return call_user_func_array(array($this->driver,$method), $arguments);
		else{
			throw new \Exception("Method Not Exist: ".$method, 1);	
		}
	}
}