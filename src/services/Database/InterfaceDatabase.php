<?php

namespace onservice\services\Database;

interface InterfaceDatabase{

	public function config(array $parameters);
	public function createBase($base);
	public function createTable($table,array $fields);
	public function select($table, $where);
	public function insert($table,array $fields);
	public function delete($table,$where = '');
	public function update($table,array $fields,$where = '');
	
}