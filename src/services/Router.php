<?php

namespace onservice\services;

class Router{
	
	public $server,
		   $namespace = 'router',
		   $version = OnServiceVersion,
		   $ignoreVerbsOptions = false;

	

	public function __construct(){}

	public function checkRequest($route,&$args,&$requestPath){
		
		$route = str_replace('//', '/', $route);
		$REQUEST_SCHEME = isset($_SERVER['REQUEST_SCHEME'])?$_SERVER['REQUEST_SCHEME']:null;
		$HTTP_HOST = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:null;
		$REQUEST_URI = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:null;
		$REDIRECT_URL = isset($_SERVER['REDIRECT_URL'])?$_SERVER['REDIRECT_URL']:null;
		$SCRIPT_NAME = isset($_SERVER['SCRIPT_NAME'])?$_SERVER['SCRIPT_NAME']:null;
			

		$REDIRECT_URL = explode('?', $REDIRECT_URL);
		$REDIRECT_URL = $REDIRECT_URL[0];

		$requestPath = str_replace(dirname($SCRIPT_NAME), '', $REDIRECT_URL);
		$routeArray = explode('/', $route);
		
		$requestPathArray = explode('/', $requestPath);
		$routeArray = array_filter($routeArray);
		$routeArray = array_values($routeArray);
		$requestPathArray = array_filter($requestPathArray);
		$requestPathArray = array_values($requestPathArray);

		if(count($routeArray)<1) $routeArray = array('/');
		if(count($requestPathArray)<1) $requestPathArray = array('/');

		
		
		$showerAll = false;
		$shower = true;
		$index = 0;
		$parameters = array();


		$found = false;
		$countFound = 0;
		foreach ($routeArray as $key => $value) {
		$asteriskFound = false;

			preg_match_all('/{(.*)}/m', $value , $matches);
		
			if( isset($requestPathArray[$key])){				
				if($value == $requestPathArray[$key]){
					$countFound++;
				}else{
					if($value == '+'){
						$countFound++;
						$asteriskFound = true;
					}else if($value == '.'){
						$countFound++;
					}
				}

			}

			if( count($matches[1]) > 0){
				$countFound++;

				$valueFiltred = str_replace('{'.$matches[1][0].'}', '', $value);
				if( isset($requestPathArray[$key]) ){
					$requestPathArrayFiltred = substr($requestPathArray[$key], 0,strlen($valueFiltred));				
					$requestPathArrayFiltred2 = str_replace($requestPathArrayFiltred, '', $requestPathArray[$key]);
					$parameters[ $matches[1][0] ] = isset($requestPathArrayFiltred2)?$requestPathArrayFiltred2:null;
				}else{
					
				}
			}

	
		}


		if( $countFound === count($routeArray) &&  ( count($routeArray) === count($requestPathArray)) || ( $asteriskFound == true && ($countFound >= count($routeArray))) ){
			$args = $parameters;
			return true;
		}
		
		return false;	
	}


	public function getRequest(){

		$method = isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:null;
		$codeStatus = isset($_SERVER['REDIRECT_STATUS'])?$_SERVER['REDIRECT_STATUS']:null;
		$QUERY_STRING = isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:null;
		$CONTENT_TYPE = isset($_SERVER['CONTENT_TYPE'])?$_SERVER['CONTENT_TYPE']:null;

		$return['method'] = strtolower($method);
	
		if(isset($_GET) && count($_GET)>0)
		$return['data']['get'] = $_GET;
		if(isset($_POST) && count($_POST)>0){
			$return['data']['post'] = $_POST;
		}else{
			$INPUT = json_decode(file_get_contents("php://input"), true) ?: [];
			if(count($INPUT)>0)
			$return['data'][ 'post' ] = $INPUT;
		}

		return $return;
	}

	public function resource($route,$callback,$methodMode = false,$annoArrayNew = array() ){
		
	

		if($this->checkRequest($route,$parameters,$requestPath)){

			$requestPar = $this->getRequest();


			if( isset($annoArrayNew['ignoreVerbsOptions']) ){
				if( filter_var($annoArrayNew['ignoreVerbsOptions'], FILTER_VALIDATE_BOOLEAN) === true )
					if($requestPar['method']=='options') return false;	

			}else{
				if($this->ignoreVerbsOptions === true){
					if($requestPar['method']=='options') return false;	
				}
			}

				
			

			$routeGet = $route;
				
			foreach ($parameters as $key => $value) {
				$routeGet = str_replace('{'.$key.'}', $value, $routeGet);
			}
			

			$routeGet = str_replace('/+', '', $routeGet);			
			$routeGet = str_replace('/.', '', $routeGet);			
			$routeTarget = str_replace($routeGet, '', $requestPath);

			$requestPar['url'] = $requestPath;
			$requestPar['endpoint'] = $routeTarget;


			if( $methodMode !== false && $methodMode !== null ){				
				if(method_exists($callback, $methodMode))
				$response = $callback->$methodMode($parameters,$requestPar,$this->server);
			}else{				
				$response = $callback($parameters,$requestPar,$this->server);
			}

			// verifica se response é uma classe, exemplo de uso com a classe PullRoute  
			if(@get_class($response)){
				if (method_exists($response, 'run')) {
					$response = $response->run();					


				}else{
					die('Missing method (run) on ['.get_class($response).']');
				}
			}

			$body = isset($response['body'])?$response['body']:null;
			$code = isset($response['code'])?$response['code']:200;
			$message = isset($response['message'])?$response['message']:'Ok';
			$contentype = isset($response['type'])?$response['type']:'text/html';
			
			$others = isset($response['others'])?$response['others']:null;
			

			header('HTTP/1.1 '.$code.' '.$message);
			header('Server: onService/'.$this->version);
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
}