<?php

namespace onservice\services\Database;

// use onservice\services\Database\mongo\Essentials as Essentials;
// use onservice\services\Database\mongo\Document as Document;
use onservice\services\Database\mongo\Collection as Collection;
// use onservice\services\Database\mongo\Security as Security;

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