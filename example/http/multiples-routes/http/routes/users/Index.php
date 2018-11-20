<?php

class Index{		
<<<<<<< HEAD

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
=======
 
	// public $route = '*';

	function __construct(){		
			
		
	}

	public function index($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /users/',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'application/json'
>>>>>>> 934e985ad4c70087d566eb8ad8c6ff64df99aa83
		);	
	}

	public function error($urlPar,$requestPa){
		
		return array(
			'body' 		=> 'Error 404 - users',
			'code'		=> 404,
			'message'	=> 'Not Found',
<<<<<<< HEAD
			'type'		=> 'text/plain'
=======
			'type'		=> 'application/json'
>>>>>>> 934e985ad4c70087d566eb8ad8c6ff64df99aa83
		);
	}
}