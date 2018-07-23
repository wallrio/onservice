<?php

namespace onservice\services\LongPolling;

USE \PDO as PDO;
USE \PDOException as PDOException;

class MysqlPersistence implements InterfaceLongPollingPersistence{

	private $PDO;

	public function __construct(array $parameters, $options = null ){
		
		$host = isset($parameters['host'])?$parameters['host']:null;
		$basename = isset($parameters['basename'])?$parameters['basename']:null;
		$username = isset($parameters['username'])?$parameters['username']:null;
		$password = isset($parameters['password'])?$parameters['password']:null;
		$port = isset($parameters['port'])?$parameters['port']:'3306';
		
		try {
			$PDO = new PDO('mysql:host='.$host.';port='.$port.';dbname='.$basename,$username,$password,$options);
			$PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	 
		   
	    } catch(PDOException $e) { 
	    	try {
		    	$PDO = new PDO("mysql:host=$host", $username, $password, $options);
				$PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	 	    	
			} catch(PDOException $e) {				
	        	echo json_encode(array('status'=>'error-no-connect','msg'=>$e->getMessage()));
	        	die();
			}
	    }

	    $this->PDO = $PDO;

		$sql = "CREATE DATABASE IF NOT EXISTS $basename"; 						
		$stmt = $PDO->prepare($sql);				
		$stmt->execute();
		$count = $stmt->rowCount();
		if($count > 0){
			$stmt = $PDO->prepare('use '.$basename);
			$stmt->execute();

			 $sql ="CREATE table IF NOT EXISTS users(
		     id VARCHAR( 25 ) PRIMARY KEY,		    
		     created INT( 30 ), 
		     time INT( 30 ), 
		     options LONGTEXT);" ;
		     $stmt = $PDO->prepare($sql);
			 $stmt->execute();

			 $sql ="CREATE table IF NOT EXISTS messages(
		     id int NOT NULL AUTO_INCREMENT PRIMARY KEY,	    
		     sender VARCHAR( 256 ),
		     receiver VARCHAR( 256 ),
		     date VARCHAR( 15 ),
		     message LONGTEXT NOT NULL);" ;
		     $stmt = $PDO->prepare($sql);
			 $stmt->execute();

		     $stmt = $PDO->prepare($sql);
			 $stmt->execute();
		    
		}
				
	}





	
	public function updateStatus($id){
		$stmt = $this->PDO->prepare('UPDATE users SET time = :time WHERE id=:id');
		$stmt->execute(array(
		':id' => $id,
		':time' => time()
		));
	}

	public function cleanMessage($id,$list){
		$list = json_decode($list);
		
		$executeArray = Array();

		$where = '';
		foreach ($list as $key => $value) {
			$operator = ' ';
			if($key < count($list)-1)
			$operator = ' OR ';
			$where .= 'id=:id'.$key.$operator;

			$executeArray[':id'.$key] = $value->id;
		}

		
		$stmt = $this->PDO->prepare('DELETE FROM messages WHERE '.$where);
		$stmt->execute($executeArray);

		return true;
	}

	public function checkMessage($id,&$notify){

		$stmt = $this->PDO->prepare('SELECT * FROM messages WHERE receiver=:receiver');
		$stmt->execute(array(
		':receiver' => $id
		));
		$count = $stmt->rowCount();
		if($count < 1) return false;

		$messages = $stmt->fetchAll(PDO::FETCH_OBJ);
		
		if( count($messages) > 0 ){
			$notify = $messages;
			return true;
		}
		
		return false;

	}

	public function recordMessage($from,$to,$message){


		$stmt = $this->PDO->prepare('INSERT INTO messages(sender,receiver,date,message) VALUES(:sender,:receiver,:date,:message)');
		$stmt->execute(array(		
		':sender' => $from,
		':receiver' => $to,
		':date' => time(),
		':message' => $message
		));


		return true; 
	
	}

	public function createUser($id,$options = '{}'){
		
		

		$stmt = $this->PDO->prepare('SELECT * FROM users WHERE id=:id');
		$stmt->execute(array(
		':id' => $id
		));
		$count = $stmt->rowCount();

		if($count < 1){

			$stmt = $this->PDO->prepare('INSERT INTO users VALUES(:id,:created,:time,:options)');
			$stmt->execute(array(
			':id' => $id,
			':created' => time(),
			':time' => time(),
			':options' => $options,
			));
		}


	}

	public function users(){
		$stmt = $this->PDO->prepare('SELECT * FROM users ');
		$stmt->execute();

		$result = $stmt->fetchAll(PDO::FETCH_OBJ);

	
		$users = Array();

		foreach ($result as $key => $value) {

			$time = $value->time;
			$options = $value->options;

			$status = 'online';
			if( time() > ($time + 7) )
			$status = 'offline';

			$users[] = array(
				'id'=>$value->id,
				'status'=>$status,
				'lastactive'=>$time,
				'options'=>$options
			);

		}

		if( count($users) > 0 ){	
			return $users;
		}

		return false;				
	}

}