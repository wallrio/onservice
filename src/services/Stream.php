<?php

namespace onservice\services;

use onservice\services\Stream\Server as Server;
use onservice\services\Stream\TCP as TCP;

class Stream {

	public $server = null;
	public $namespace = 'stream';	
	public $driver = null;
	public $debug = null;
	public $id = null;
	public $createCommands;


	private $address = '127.0.0.1';
	private $port = 3333;


	public function __construct($address = '127.0.0.1', $port = 3333, $driver = null){
		$this->address = $address;
		$this->port = $port;
		if($driver === null ){
			$this->driver = new TCP;
		}else{
			$this->driver = $driver;
		}
		
		$this->driver->address = $address;
		$this->driver->port = $port;	
	}
	

	public function __call($method, $args){
		
		$this->driver->debug = $this->debug;
		$this->driver->createCommands = $this->createCommands;
		$this->driver->id = $this->id;

		if( method_exists($this->driver, $method) ){			
			return call_user_func_array(array($this->driver,$method), $args);
		}
  	}
	
	
}