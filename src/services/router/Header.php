<?php

namespace onservice\services\router; 


class Header{

	private $listResponse = [
		'finish'=>0,
		'header'=>[],
	];

	public function __construct(){
		return $this;
	}



	public function add($key, $value){
		$this->listResponse['header'][$key] = $value;

		return $this;
	}

	public function response(){
		
		$responseArray = [];

		if($this->listResponse['header'] != '') $responseArray['header'] = $this->listResponse['header'];
		$responseArray['finish'] = $this->listResponse['finish'];

		return $responseArray;
	}


}

