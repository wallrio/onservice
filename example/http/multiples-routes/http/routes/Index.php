<?php

class Index{		
 

	public function index($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'text/plain'
		);	
	}

	/** @route: /test **/
	public function test($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /test',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'text/plain'
		);	
	}
	
	public function error($urlPar,$requestPa){
		
		return array(
			'body' 		=> 'Error 404',
			'code'		=> 404,
			'message'	=> 'Not Found',
			'type'		=> 'text/plain'
		);
	}
	
}