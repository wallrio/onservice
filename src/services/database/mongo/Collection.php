<?php

namespace onservice\services\database\mongo;

class Collection{

	public function __construct($db = null, $basename = null){
		$this->db = $db;
		$this->basename = $basename;
	}

	public function createCollection($name){
		$collection = $this->db->createCollection($name);
		return (object) array(
			'document' => new Document($this->db,$name,$collection)
		);
	}

	

	public function deleteCollection($name){
		return  $this->db->selectCollection($name)->drop();
	}

	public function collection($name){
		$collection =  $this->db->selectCollection($name);
		return (object) array(
			'document' => new Document($this->db,$name,$collection)
		);
	}
}