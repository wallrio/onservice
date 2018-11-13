<?php

namespace onservice\services;

class Http{
	
	public $server = null;
	public $namespace = 'http';
	private $routesPath = null;

	public function __construct(){}



	// busca as rotas no diretório recursivamente 
	public function findRecursive($dir,$parent = ''){
		$dirArray = scandir($dir);
		$newArray = [];
		
		foreach ($dirArray as $key => $value) {
			if($value == '.' || $value == '..') unset($dirArray[$key]);
		}
		foreach ($dirArray as $key => $value) {
			if(is_dir($dir.DIRECTORY_SEPARATOR.$value)){
				if(substr($value, 0,1)=='_')continue;
				$newArray2 = $this->findRecursive($dir.DIRECTORY_SEPARATOR.$value,$parent.'/'.$value);
				$newArray = array_merge($newArray,$newArray2);		
			}else{
				if(substr($value, 0,1)=='_')continue;
				$valueName = $value;			
				$valueName = $parent.'/'.$value;

				$valueName = strtolower($valueName);
				$valueName = str_replace('.php', '', $valueName);
				$newArray[$valueName] = $parent.'/'.$value;
			}
		}
		return $newArray;
	}

	// cria as rotas
	public function routes($dir = null){

		$this->routesPath = $dir;	

		$dirs = $this->findRecursive($dir);

		foreach ($dirs as $key => $value) {

			$isIndex = false;
			$routeRef = $key;
			$routeRef = explode('/', $routeRef);
			if( strtolower(end($routeRef)) == 'index'){
				unset($routeRef[count($routeRef)-1]);
				$isIndex = true;
			}
			$routeRef = implode('/', $routeRef);

			
			$dirEnd = $dir.ucfirst($value);
			$dirEnd = str_replace('//', '/', $dirEnd);
			$content = file_get_contents($dirEnd);
			$content = str_replace('<?php', '', $content);
			$content = str_replace('?>', '', $content);

			
			$className = explode('/', $key);
			$className = end($className);
			$keyNamespace = dirname($key);
			$keyNamespace = str_replace('/', '\\', $keyNamespace);
			
			if($keyNamespace == '\\')$keyNamespace = "";

			$namespace = 'onservice\http\routes'.$keyNamespace.'';
			ob_start();
			echo 'namespace '.$namespace.';';
			echo $content;
			$content2 = ob_get_contents();
			ob_get_clean();
			eval( $content2);		
			eval('$route = new \\'.$namespace.'\\'.ucfirst($className).';');
			
			// custom route in class
			if(isset($route->route)) $routeRef = $routeRef.'/'.$route->route;
			
			if($routeRef == '')$routeRef='/';
			$routeFound = $this->server->http->resource($routeRef,$route,'index');			
		}
		

		// realinha para o final caso exista classe Index
		$listDirs = [];
		foreach ($dirs as $key => $value) {
			$routeRef = explode('/', $key);

			if( strtolower(end($routeRef)) == 'index'){				
				$listDirs[$key] = $dirs[$key];
				unset($dirs[$key]);
			}
		}
		arsort($listDirs);
		foreach ($listDirs as $key => $value) $dirs[$key] = $value;

		// cria rota de erro quando existir o methdo de erro (error)
		foreach ($dirs as $key => $value) {
			$isIndex = false;
			$routeRef = $key; 
			$routeRef = explode('/', $routeRef);
			if( strtolower(end($routeRef)) == 'index'){
				unset($routeRef[count($routeRef)-1]);
				$isIndex = true;
			}
			$routeRef = implode('/', $routeRef);

			
			$dirEnd = $dir.ucfirst($value);
			$dirEnd = str_replace('//', '/', $dirEnd);
			$content = file_get_contents($dirEnd);
			$content = str_replace('<?php', '', $content);
			$content = str_replace('?>', '', $content);

			
			$className = explode('/', $key);
			$className = end($className);
			$keyNamespace = dirname($key);
			$keyNamespace = str_replace('/', '\\', $keyNamespace);
			
			if($keyNamespace == '\\')$keyNamespace = "";

			$namespace = 'onservice\http\routes'.$keyNamespace.'';
			
			eval('$route = new \\'.$namespace.'\\'.ucfirst($className).';');
			
			// custom route in class
			if(isset($route->route)) $routeRef = $routeRef.'/'.$route->route;
			
			if($routeRef == '')$routeRef='/';

			if (method_exists($route, 'error')) {		
				$routeFound = $this->server->http->resource($routeRef.'/*',$route,'error');
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

	public function resource($route,$callback,$methodMode = false){
		
		

		if($this->checkRequest($route,$parameters,$requestPath)){

			$requestPar = $this->getRequest();
			$requestPar['url'] = $requestPath;

			if( $methodMode !== false ){
				if(method_exists($callback, $methodMode))
				$response = $callback->$methodMode($parameters,$requestPar,$this->server);
			}else{
				$response = $callback($parameters,$requestPar,$this->server);
			}

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