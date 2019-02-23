<?php

namespace onservice\services\database;

use onservice\services\database\mongo\Collection as Collection;

class Mongo {

	private $connection;

	public function __construct(){
		$this->connection = new \MongoClient('localhost:27017');		
	}

	public function createBase($basename){
		$this->db = $this->connection->selectDB($basename);
		return new Collection($this->db,$basename);
	}

	public function base($base){		
		$this->basename = $base;
		$this->db = $this->connection->selectDB($base);
		return new Collection($this->db,$this->basename);
	}

}