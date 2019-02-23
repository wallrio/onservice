<?php

namespace onservice\services\database;

class Mysql{

	public $status;

	public function __construct($host = null,$username = null,$password = null,$basename = null){
		
		$this->host = $host;
		$this->basename = $basename;
		$this->username = $username;
		$this->password = $password;

		$this->config = array(
			'host'=>$host,
			'basename'=>$basename,
			'username'=>$username,
			'password'=>$password,
		);	

		if($host != null && $username != null && $password != null )
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


	public function getScheme($table){
		$q = $this->db->prepare("SHOW COLUMNS FROM ".$table."");
	    $q->execute();
	    $table_fields = $q->fetchAll(\PDO::FETCH_OBJ);

	    $array = array();
	    foreach ($table_fields as $key => $value) {
	    	$name = $value->Field;
	    	unset($value->Field);
	    	$array[$name] = (object) $value;
	    }
	    return (object) $array;
	}

	public function connect(){

		

		if($this->basename != null){

			try{
				$this->db = new \PDO("mysql:host=".$this->host.";dbname=".$this->basename, $this->username, $this->password); 
				return true;
			}catch(\Exception $e){		
				die('onservice[Database:Mysql]: could not connect to base');				
			}

		}else{
			try{
				$this->db = new \PDO("mysql:host=".$this->host.";", $this->username, $this->password); 
				return true;
			}catch(\Exception $e){
				die('onservice[Database:Mysql]: could not connect to database');
				
			}
		}


	}

	public function base($name){
		$this->basename = $name;

		$this->config['basename'] = $name;

		$this->connect();	
	}

	private $tasks = Array();

	public function createBase($base){
		$sql = 'CREATE DATABASE IF NOT EXISTS '.$base.';';
		
		return $this->flush($sql);
	}

	public function delete($table,$where = ''){	

		$sql = 'DELETE FROM '.$table.' '.(($where != '')?' WHERE '.$where:'');
		
		return $this->flush($sql);
	}

	public function update($table,array $fields,$where = ''){	
		
		$fieldsJoin = '';
		$index = 0;
		$listArray = Array();
		foreach ($fields as $key => $value){
			if($index < count($fields) -1 )
				$separator = ',';
			else
				$separator = '';

			$fieldsJoin .= ''.$key .'=:'.$key.' '.$separator;	
			$listArray[':'.$key] = $value;
			$index++;
		}	

		$sql = 'UPDATE '.$table.' SET '.$fieldsJoin.' '.(($where != '')?' WHERE '.$where:'');
		
		return $this->flush($sql,$listArray);
	}

	public function insert($table,array $fields){	

		$fieldsJoin = '';
		$valuesJoin = '';
		$index = 0;
		$listArray = Array();
		foreach ($fields as $key => $value){
			if($index < count($fields) -1 )
				$separator = ',';
			else
				$separator = '';

			$fieldsJoin .= ''.$key .''.$separator;
			$valuesJoin .= ':'.$key .''.$separator;

			$listArray[':'.$key] = $value;

			$index++;
		}	

		$sql = 'INSERT INTO '.$table.' ('.$fieldsJoin.') VALUES('.$valuesJoin.')';


		return $this->flush($sql,$listArray);

	}

	public function select($table, $where = ''){	

		$sql = 'SELECT * FROM '.$table.' ' .(($where != '')?' WHERE '.$where:'');

		return $this->flush($sql);

	}

	public function createTable($table,array $fields){	

		$sqlJoin = '';
		$index = 0;
		foreach ($fields as $key => $value){
			if($index < count($fields) -1 )
				$separator = ',';
			else
				$separator = '';

			$sqlJoin .= $key .' '.$value.$separator;
			$index++;
		}	

		$sql = "CREATE TABLE IF NOT EXISTS ".$table." ( ".$sqlJoin." );";

		return $this->flush($sql);
	}

	public function query($sql,$array = null){	
		$sth = $this->db->prepare($sql);
		$sth->execute($array);
		$count = $sth->rowCount();
		$result = $sth->fetch();
		return $result;
	}

	public function flush($sql,$array = null){
		
			$sth = $this->db->prepare($sql);
			$sth->execute($array);
			$count = $sth->rowCount();
			$result = $sth->fetchAll(\PDO::FETCH_OBJ);
			return $result;
		
	}

}