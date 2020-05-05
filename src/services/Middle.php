<?php

namespace onservice\services;

use onservice\services\Router as Router;

class Middle{

	public $server = null;
	public $namespace = 'middle';
	public static $version = OnServiceVersion;

	public function __construct(){}

	static public function single($callback = null){

		$router = new Router;
		$requestPar = $router->getRequest();
		$checkRequest = $router->checkRequest('*',$args,$requestPath);

		if($callback)
			$response = $callback($requestPath,$requestPar);


		if( $response !== false ){
			if(!is_array($response)){				
				$GLOBALS['middle'] = $response;
				return;		
			}
		}
		else if(  !empty($response)  ){
			return;
		}else{

		}
	

		$body = isset($response['body'])?$response['body']:null;
		$code = isset($response['code'])?$response['code']:403;
		$message = isset($response['message'])?$response['message']:'Forbidden';
		$contentype = isset($response['type'])?$response['type']:'text/plain';
		
		$others = isset($response['others'])?$response['others']:null;
		
		header('HTTP/1.1 '.$code.' '.$message);
		header('Server: onService/'.self::$version);
		header('Content-Length: '.strlen($body));
		header('Content-Type:'.$contentype);
		
		if(is_array($others) && count($others)>0){				
			unset($others['Transfer-Encoding']);
			foreach ($others as $key => $value) {
				header($key.':'.$value);
			}
		}

		if($body) echo $body;

		exit;

	}

}