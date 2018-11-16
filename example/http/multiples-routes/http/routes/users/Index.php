<?php

class Index{		

	public function index($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /users/',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'text/plain'
		);	
	}

	/** @route: /all **/
	public function all($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /users/all',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'text/plain'
		);	
	}

	/** @route: /get/{id} **/
	public function getId($urlPar,$requestPar){		
	
		return array(
			'body' 		=> 'route: /users/get/'.$urlPar['id'],
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'text/plain'
		);	
	}

	public function error($urlPar,$requestPa){
		
		return array(
			'body' 		=> 'Error 404 - users',
			'code'		=> 404,
			'message'	=> 'Not Found',
			'type'		=> 'text/plain'
		);
	}
}