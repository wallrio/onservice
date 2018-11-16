<?php

class Index{		
 

	public function index($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /companies/',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'text/plain'
		);	
	}

	public function error($urlPar,$requestPa){
		
		return array(
			'body' 		=> 'Error 404 - companies',
			'code'		=> 404,
			'message'	=> 'Not Found',
			'type'		=> 'text/plain'
		);
	}
}