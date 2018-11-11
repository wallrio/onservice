<?php

namespace onservice\http\routes;

class Companies {

	function __construct(){}

	public function computation(){
		return array(
			// custom route: /computation
			array(							
				'method' => function($urlPar,$requestPar){

					return array(
						'body' 		=> 'route: /companies/computation',
						'code'		=> 200,
						'message'	=> 'Ok',
						'type'		=> 'application/json'
					);		
				}
			),
			
			// custom route: /computation/new
			array(			
				'route'=>'/new',
				'method' => function($urlPar,$requestPar){

					return array(
						'body' 		=> 'route: /companies/computation/new',
						'code'		=> 200,
						'message'	=> 'Ok',
						'type'		=> 'application/json'
					);
				}
			)
		);
	}

}