<?php

namespace onservice\http\routes;

class Users {

	function __construct(){}

	// url: /user/logon
	public function logon(){
		return array(
			'method' => function($urlPar,$requestPar){
				
				// your code

				return array(
					'body' 		=> 'route: /user/logon',
					'code'		=> 200,
					'message'	=> 'Ok',
					'type'		=> 'application/json'
				);	

			}
		);
	}

	
}