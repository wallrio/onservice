<?php

class Logon{		

	public function index($urlPar,$requestPar){		
			
		return array(
			'body' 		=> 'route: /users/logon',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'text/plain'
		);	
	}


	/** @route: /info **/
	public function info($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /users/logon/info',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'text/plain'
		);	
	}

	public function error($urlPar,$requestPa){
		
		return array(
			'body' 		=> 'Error 404 - logon',
			'code'		=> 404,
			'message'	=> 'Not Found',
			'type'		=> 'text/plain'
		);
	}
}