<?php

namespace onservice\services\database;

// use onservice\services\database\mongo\Essentials as Essentials;
// use onservice\services\database\mongo\Document as Document;
use onservice\services\database\mongo\Collection as Collection;
// use onservice\services\database\mongo\Security as Security;

class Mongo {

	private $connection;

	public function __construct($basename = null){
		$this->connection = new \MongoClient('localhost:27017');
		$this->basename = $basename;
	}

	public function createBase($basename){
		$this->db = $this->connection->selectDB($basename);
		return new Collection($this->db,$basename);
	}

	public function base($base){		
		$this->basename = $base;
		return new Collection($this->db,$this->basename);
	}

}