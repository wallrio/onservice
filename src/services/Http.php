<?php

namespace onservice\services;

class Http{
	
	public $server = null;
	public $namespace = 'http';
	private $routesPath = null;

	public function __construct(){}

	public function routesDir($dir){
		$this->routesPath = $dir;
	}

	public function routes(array $contanerContent,$dir = null){

		if($dir == null) $dir = $this->routesPath;
			
		if( in_array('index',$contanerContent)){
			unset($contanerContent[array_search('index', $contanerContent)]);
			$contanerContent = array_values($contanerContent);
			array_push($contanerContent, 'Index');
		} 

		if( in_array('Index',$contanerContent) ){
			unset($contanerContent[array_search('Index', $contanerContent)]);
			$contanerContent = array_values($contanerContent);
			array_push($contanerContent, 'Index');
		}	

		foreach ($contanerContent as $key => $value) {
			
			$classFound = true;
			$routeFound = false;
			if(!file_exists($dir.DIRECTORY_SEPARATOR.ucfirst($value).'.php')) continue;

			require $dir.DIRECTORY_SEPARATOR.ucfirst($value).'.php';

			eval('$route = new \onservice\http\routes\\'.ucfirst($value).'();');
			$class_methods = get_class_methods($route);
			foreach ($class_methods as $key2 => $value2) {
				
				$routeCurrent = $route->$value2();
				
				if( gettype($routeCurrent) == 'object' ){
					$arrayRoutes = array(array('method'=>$routeCurrent));
				}else if( gettype($routeCurrent) == 'array' ){
					$arrayRoutes = $routeCurrent;
				}else{
					$arrayRoutes = array(array('method'=>$routeCurrent));
				}

				if($value == 'Index'){
					if($value2 == 'index') 
						$routeRef = '/';
					else
						$routeRef = '/'.strtolower($value2);
				}else{
					$routeRef = '/'.strtolower($value).'/'.strtolower($value2);
				}

				
	
				foreach ($arrayRoutes as $key3 => $value3) {
					$routeFinish = $value3;
					
					if(gettype($routeFinish) == 'array'){					
						
						$routeRef = $routeRef.'/'. (isset($routeFinish['route'])?$routeFinish['route']:'');
						$methodRef = isset($routeFinish['method'])?$routeFinish['method']:null;

					}else{
						$methodRef = $routeFinish;
					}
					
					$routeRef = preg_replace('#//#si', '/', $routeRef); 
					$routeRef = preg_replace('#//#si', '/', $routeRef); 
				
					if($routeRef == null || $methodRef == null) continue;
		
					$routeFound = $this->server->http->resource($routeRef,$methodRef);
				}

			}
			
			if(method_exists($route, '__error')){

				$routeCurrent = $route->__error();			
				$methodRef = $routeCurrent;				
				$routeRef = '*';
	
				$this->server->http->resource($routeRef,$methodRef);
			}
		
			
		}
	}

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


		$showerAll = false;
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


			if (  $valueFiltred != $requestPathArrayFiltred  && $value != '.' ) {
					if($value !== '*')
					$shower = false;	

					if($value === '*')
					$showerAll = false;	

				
			}else if (  $valueFiltred != $requestPathArrayFiltred  && $value != '*' ) {
					if($value === '.')
					$showerAll = true;		
						
			}else{
				if($value !== ''){					
					$shower = true;	
					$showerAll = true;		
				}
			}
			

		}

		if($showerAll === true){
			$args = $parameters;
			if(count($requestPathArray) < count($routeArray) || count($requestPathArray) > count($routeArray))
				return false;
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