<?php

namespace onservice\services\authentication;
use onservice\services\authentication\jwt\JWT as coreJWT;

class JWT{

	public $server = null;
	public $namespace = 'token';
	public $key;

	public function __construct($key = 'JWTOnService'){
		$this->key = $key;		
	}

	public function encode(array $tokenParameters = array() ){			
		return coreJWT::encode($tokenParameters, $this->key);	
	}

	public function decode($token,$algorithms = array('HS256') ){	
		try {
			return  coreJWT::decode($token, $this->key, $algorithms);
		} catch (\Exception $e) {
			return false;
		}	
	}
}