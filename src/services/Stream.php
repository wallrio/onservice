<?php

namespace onservice\services;

class Stream{

	public $server = null;
	public $namespace = 'stream';	
	private $addrress = '127.0.0.1';
	private $port = 3333;
	private $receiverStream = null;
	public $bufferSize = (1024 * 8);
	
	public function __construct($addrress = '127.0.0.1', $port = 3333,$protocol = 'tcp'){
		$this->addrress = $addrress;
		$this->port = $port;
		$this->protocol = $protocol;

	}

	public function listen($callback){

		$this->receiverStream = $callback;
		if($this->protocol == 'tcp')
			$this->startTCP();
		else if($this->protocol == 'udp')
			$this->startUDP();
	}

	public function startUDP(){

		$methodStream = $this->receiverStream;
		
		$socket = stream_socket_server("udp://".$this->addrress.":".$this->port, $errno, $errstr, STREAM_SERVER_BIND);
		
		if (!$socket) die("$errstr ($errno)");
		
		do {
			$pkt = stream_socket_recvfrom($socket, $this->bufferSize, 0, $peer);		
			$result = $methodStream($pkt);			
		}while($pkt !== false);
	}

	public function startTCP(){

		$methodStream = $this->receiverStream;
		$socket = socket_create(AF_INET, SOCK_STREAM, 0); //IPv4, TCP
		socket_bind($socket, $this->addrress, $this->port);
		socket_listen($socket);

		while (true) {
		  $ret = socket_accept($socket);
		  $input = socket_read($ret, $this->bufferSize);
		  $result = $methodStream($input);
		  socket_write($ret, $result, strlen($result));
		  socket_close($ret);
		}

	}

	public function sendUDP($data = ''){
		$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP); 
		$len = strlen($data);
		socket_set_option($socket, SOL_SOCKET, SO_BROADCAST, 1);
		socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>5, "usec"=>0));
		socket_sendto($socket, $data, $len, 0, $this->addrress, $this->port);
		socket_close($socket);
	}

	public function send($data = ''){
		$protocol = $this->protocol;
		if($protocol == 'tcp')
			$this->sendTCP($data);
		else if($protocol == 'udp')
			$this->sendUDP($data);
	}

	public function sendTCP($data = ''){
		$socket = socket_create(AF_INET, SOCK_STREAM, 0);
		$con = socket_connect($socket, $this->addrress, $this->port);		
		$len = strlen($data);
		socket_write($socket, $data, $len);	
		$result = socket_read ($socket, $this->bufferSize);		
		socket_close($socket);
		return $result;
	}


	
}