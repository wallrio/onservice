<?php

namespace onservice\services\router; 

use onservice\services\router\format\Xml as Xml;

class Response{

	private $listResponse = [
		'finish'=>1,
		'body'=>'',
		'code'=>200,
		'message'=>'Ok',
		'type'=>'text/html',
		'header'=>[],
	];

	public function __construct($body = ''){
		$this->listResponse['body'] = $body;
		return $this;
	}

	public function body($body = ''){
		$this->listResponse['body'] = $body;
		return $this;
	}

	public function code($code = 200){
		$this->listResponse['code'] = $code;
		return $this;
	}

	public function message($message = 'Ok'){
		$this->listResponse['message'] = $message;		
		return $this;
	}

	public function header($key, $value){
		$this->listResponse['header'][$key] = $value;

		return $this;
	}

	public function type($type = 'text/html'){
		$this->listResponse['type'] = $type;
		return $this;
	}

	public function format($format){
		$body = $this->listResponse['body'];

		if($format === 'json')$body = json_encode($body);
		if($format === 'xml') $body = Xml::transform($body);		

		$this->listResponse['body'] = $body;
		return $this;
	}

	public function response(){
		
		$responseArray = [];

		 

		if($this->listResponse['body'] != '') $responseArray['body'] = $this->listResponse['body'];
		if($this->listResponse['code'] != '') $responseArray['code'] = $this->listResponse['code'];
		
		if($this->listResponse['message'] != '') $responseArray['message'] = $this->listResponse['message'];

		if($this->listResponse['type'] != '') $responseArray['type'] = $this->listResponse['type'];
		if($this->listResponse['header'] != '') $responseArray['header'] = $this->listResponse['header'];
		 $responseArray['finish'] = $this->listResponse['finish'];

		 if($responseArray['code'] === 404 && $this->listResponse['message'] === 'Ok'){
		 	$responseArray['message'] = 'Not Found';
		 }

		return $responseArray;
	}



	


}

