<?php

namespace onservice\http\routes;

class Index {

	function __construct(){}

	// url: /
	public function index(){
		return function($urlPar,$requestPar){
				
				$response = '<h2>First page</h2>';
				$response .= '<hr>';
				$response .= '<a href="user/logon">Acess user/logon</a><br>';
				$response .= '<a href="user/logout">Acess user/logout</a> (will show an error page)<br>';
				$response .= '<a href="companies/computation">Acess companies/computation</a><br>';
				$response .= '<a href="companies/computation/new">Acess companies/computation/new</a><br>';
				$response .= '<a href="companies/computation/new2">Acess companies/computation/new2</a> (will show an error page)<br>';

				return array(
					'body' 		=> $response,
					'code'		=> 200,
					'message'	=> 'Ok',
					'type'		=> 'text/html'
				);				
		};
	}


	public function __error(){
		return function($urlPar,$requestPar){	
			return array(
				'body' 		=> 'Error 404',
				'code'		=> 404,
				'message'	=> 'Not Found',
				'type'		=> 'application/json'
			);
		};
	}

}