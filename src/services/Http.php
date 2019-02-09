<?php

namespace onservice\services;

use onservice\essentials\File as File;

class Http{
	
	public $server,
		   $namespace = 'http',
		   $ignoreVerbsOptions = false;

	private $routesPath = null;

	public function __construct(){}


	public function loadLibs($dir = null){
		if(!file_exists($dir)) return false;
		$dirArray = File::findRecursive($dir);	
		$this->dirArray = $dirArray;

		foreach ($this->dirArray as $key => $value) {
			$key = substr($key, 1);
			$array = explode('/', $key);

			$pathClass = $dir.$value;
				$pathClass = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $pathClass);

			require_once $pathClass;
		}
	}

	// cria as rotas
	public function routes($dir = null){

		if($dir === null){
			$dir = getcwd().DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'routes';
		}

		$annoArrayNew = array();

		$this->routesPath = $dir;	

		if(!file_exists($dir)) mkdir($dir);

		$dirs = File::findRecursive($dir);


		foreach ($dirs as $key => $value) {

			$isIndex = false;
			$routeRef = $key;
			$routeRef = explode('/', $routeRef);
			if( strtolower(end($routeRef)) == 'index'){
				unset($routeRef[count($routeRef)-1]);
				$isIndex = true;
			}
			$routeRef = implode('/', $routeRef);

			
			if( substr($value, 0,1) == '_' ) continue;
			
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

			$dirArray = explode(DIRECTORY_SEPARATOR, $dirEnd);
			unset($dirArray[count($dirArray)-1]);
			// $route->dir = implode(DIRECTORY_SEPARATOR, $dirArray).DIRECTORY_SEPARATOR;
			$currentDir = getcwd().DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $dirArray).DIRECTORY_SEPARATOR;

			$libs = $currentDir.'_class';
			$this->loadLibs($libs);


			$namespace = 'onservice\http\routes'.$keyNamespace.'';
			ob_start();
			echo 'namespace '.$namespace.';';

			echo $content;
			$content2 = ob_get_contents();
			// $content2 = str_replace('__LOCAL__', '\''.$currentDir.'\'', $content2);
			ob_get_clean();
		
			eval( $content2);		
			eval('$route = new \\'.$namespace.'\\'.ucfirst($className).'($this->server)'.';');
			
			
			
			$route->_dir = $currentDir;
			


			if($routeRef == '')$routeRef='/';

			$customRoute = false;

			if( method_exists($route, 'index') )
			$routeFound = $this->server->http->resource($routeRef,$route,'index');			
			
			$mthodsArray = get_class_methods($route);

			foreach ($mthodsArray as $key => $value) {
				
				$method = $value;						
				$r = new \ReflectionClass( $namespace.'\\'.ucfirst($className) );
				$doc = $r->getMethod($method)->getDocComment();
				preg_match_all('#@(.*?)(\*\*\/|\n)#s', $doc, $annotations);
				$annoArray = $annotations[1];

				if(is_array($annoArray) && count($annoArray)<1)
					continue;


				$annoArrayNew = [];				
				foreach ($annoArray as $anKey => $anValue) {
					$anName = substr($anValue, 0, strpos($anValue, ':'));
					$annoArrayNew[$anName] = substr($anValue, strpos($anValue, ':')+1);
				}
				
				$customRoute = isset($annoArrayNew['route'])?$annoArrayNew['route']:$customRoute;
				
				$modearray = false;
				$checkArray = '';
				if($customRoute != ''){
					if(strpos($customRoute, '[')!=false)	
					$modearray = true;							
				}
					


				if($modearray == true){
					@eval('$checkArray = '.$customRoute.';');				
				

					foreach ($checkArray as $key2 => $value2) {
						$customRoute = $value2;
						if($customRoute !== false){
							$customRoute = trim($customRoute);
							$routeRef_end = $routeRef.''.$customRoute;				

							$routeFound = $this->server->http->resource($routeRef_end,$route,$method,$annoArrayNew);
						}
					}
				}else{



				
					if($customRoute !== false){
						$customRoute = trim($customRoute);
						$routeRef_end = $routeRef.''.$customRoute;				
						
						$routeFound = $this->server->http->resource($routeRef_end,$route,$method,$annoArrayNew);
					}
				}

			}
			
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
			
			
			eval('$route = new \\'.$namespace.'\\'.ucfirst($className).'($this->server)'.';');
			
			

			if(isset($route->route)) $routeRef = $routeRef.'/'.$route->route;
			
			if($routeRef == '')$routeRef='/';

			if (method_exists($route, 'error')) {		
				$routeFound = $this->server->http->resource($routeRef.'/+',$route,'error',$annoArrayNew);
			}

		}
		
	}

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
			header('Server: onService/'.$this->version);
			header('Content-Length: '.strlen($body));
			header('Content-Type:'.$contentype);

			if($body) echo $body;

			exit;
		}
	
	}
}