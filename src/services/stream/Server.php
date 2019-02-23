<?php

namespace onservice\services\stream;

class Server{
	
	public $driver = null;
	public $address = null;
	public $port = null;

	public function listen($callback){
		$this->receiverStream = $callback;
		$this->driver->address = $this->address;
		$this->driver->port = $this->port;
		$this->driver->listen($callback);
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

}