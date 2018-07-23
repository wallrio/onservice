<?php

namespace onservice\services;
use onservice\services\Database\InterfaceDatabase as InterfaceDatabase;

class Database{
	public $namespace = 'database';

	public function __construct(InterfaceDatabase $driver){
		$this->driver = $driver;		
	}

	public function test(){
		echo 'abc';
	}

	public function config(array $parameters = null){
		return $this->driver->config($parameters);
	}


	public function flush(){
		return $this->driver->flush();
	}

	public function query($sql,$useFlush = false){
		return $this->driver->query($sql,$useFlush);
	}

	public function createBase($base){
		return $this->driver->createBase($base);
	}
	public function createTable($table,array $fields){
		return $this->driver->createTable($table,$fields);
	}

	public function insert($table,array $fields){
		return $this->driver->insert($table,$fields);
	}

	public function update($table,array $fields,$where = ''){
		return $this->driver->update($table,$fields,$where);
	}

	public function delete($table,$where = ''){
		return $this->driver->delete($table,$where);
	}

	public function select($table,$where = ''){
		return $this->driver->select($table,$where);
	}

	
}