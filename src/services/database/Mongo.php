<?php

namespace onservice\services\database;

use onservice\services\database\mongo\Collection as Collection;

class Mongo {

	private $connection;

	public $status;

	public function __construct($config = null){
		


		$this->config = $config;	

		
		$this->connect();	
	}

	public function config(array $parameters = null){


		if(!isset($parameters)){
			return $this->config; 
		}
		$this->config = $parameters;
		if(is_array($parameters) && count($parameters)>0)
		foreach ($parameters as $key => $value) {
			$this->{$key} = $value;	
		}



		$this->connect();
	}


	public function connect(){

		$host = isset($this->config['host'])?$this->config['host']:null;
		$port = isset($this->config['port'])?":".$this->config['port']:null;
		$basename = isset($this->config['basename'])?$this->config['basename']:null;
		$username = isset($this->config['username'])?$this->config['username']:null;
		$password = isset($this->config['password'])?$this->config['password']:null;

		if($basename != null){
			if (class_exists('MongoClient')) {
				$this->connection = new \MongoClient("mongodb://".$username.":".$password."@".$host.$port);
			}else{
				$this->connection = new \MongoDB\Client("mongodb://".$username.":".$password."@".$host.$port);
				
			}
		}
	}



	public function createBase($basename){
		$this->db = $this->connection->selectDB($basename);
		return new Collection($this->db,$basename);
	}

	public function base($base){		
		$this->basename = $base;
		$this->db = $this->connection->selectDatabase($base);
		return new Collection($this->db,$this->basename);
	}

}