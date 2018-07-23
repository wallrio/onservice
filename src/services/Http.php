<?php

namespace onservice\services;

class Http{
	
	public $namespace = 'http';

	public function __construct(){}

	public function checkRequest($route,&$args,&$requestPath){
		
		$REQUEST_SCHEME = isset($_SERVER['REQUEST_SCHEME'])?$_SERVER['REQUEST_SCHEME']:null;
		$HTTP_HOST = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:null;
		$REQUEST_URI = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:null;
		$SCRIPT_NAME = isset($_SERVER['SCRIPT_NAME'])?$_SERVER['SCRIPT_NAME']:null;
		
		$REQUEST_URI = explode('?', $REQUEST_URI);
		$REQUEST_URI = $REQUEST_URI[0];

		$requestPath = str_replace(dirname($SCRIPT_NAME), '', $REQUEST_URI);
		$routeArray = explode('/', $route);
		$requestPathArray = explode('/', $requestPath);
		$routeArray = array_filter($routeArray);
		$routeArray = array_values($routeArray);
		$requestPathArray = array_filter($requestPathArray);
		$requestPathArray = array_values($requestPathArray);

		if(count($routeArray)<1) $routeArray = array('/');
		if(count($requestPathArray)<1) $requestPathArray = array('/');

		$shower = true;
		$index = 0;
		$parameters = array();
		foreach ($routeArray as $key => $value) {
			$index++;
			
			preg_match_all('/{(.*)}/m', $value , $matches);


			if( isset($requestPathArray[$key]) && count($matches[1]) > 0){
				$valueFiltred = str_replace('{'.$matches[1][0].'}', '', $value);
				$requestPathArrayFiltred = substr($requestPathArray[$key], 0,strlen($valueFiltred));
				$requestPathArrayFiltred2 = str_replace($requestPathArrayFiltred, '', $requestPathArray[$key]);
				$parameters[ $matches[1][0] ] = isset($requestPathArrayFiltred2)?$requestPathArrayFiltred2:null;
			}else{
				$valueFiltred = $value;
				$requestPathArrayFiltred = isset($requestPathArray[$key])?$requestPathArray[$key]:null;
			}

		
			if (  $valueFiltred != $requestPathArrayFiltred  && $value != '*' ) {
					$shower = false;				
			}
			

		}

		if($shower === true){
			$args = $parameters;

			 return true;
		}
		
		return false;
	}

	public function getRequest(){

		$method = isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:null;
		$codeStatus = isset($_SERVER['REDIRECT_STATUS'])?$_SERVER['REDIRECT_STATUS']:null;
		$QUERY_STRING = isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:null;

		parse_str($QUERY_STRING, $objectQueryString);

		$return['method'] = strtolower($method);
		if(count($objectQueryString)>0)
			$return['data'] = $objectQueryString;
		
		return $return;

	}

	public function resource($route,$callback){
		
		if($this->checkRequest($route,$parameters,$requestPath)){

			$requestPar = $this->getRequest();
			$requestPar['url'] = $requestPath;

			$response = $callback($parameters,$requestPar,$this->server);

			$body = isset($response['body'])?$response['body']:null;
			$code = isset($response['code'])?$response['code']:200;
			$message = isset($response['message'])?$response['message']:'Ok';
			$contentype = isset($response['type'])?$response['type']:'text/html';

			header('HTTP/1.1 '.$code.' '.$message);
			header('Server: onService/0.0.1');
			header('Content-Length: '.strlen($body));
			header('Content-Type:'.$contentype);

			if($body)
				echo $body;

			exit;
		}
	
	}
}