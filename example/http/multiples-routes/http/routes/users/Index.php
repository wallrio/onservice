<?php

class Index{		
 
	// public $route = '*';

	function __construct(){		
			
		
	}

	public function index($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /users/',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'application/json'
		);	
	}

	public function error($urlPar,$requestPa){
		
		return array(
			'body' 		=> 'Error 404 - users',
			'code'		=> 404,
			'message'	=> 'Not Found',
			'type'		=> 'application/json'
		);
	}
}