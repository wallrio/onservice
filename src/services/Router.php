<?php

namespace onservice\services; 

use onservice\services\router\RouterClass as RouterClass;
use onservice\essentials\File as File;

class Router{

	private $routesPath = null;
	private $prefix = null;
	private $routeFound = true;
	private $listMiddles = [];

	public function __construct(){
		$this->routerclass = new RouterClass();
	}

	public function corsAllowAll(){
		// set CORS to open 
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: *');
		header("Access-Control-Allow-Headers: *");
	}

	public function addMiddle($class){
		$this->listMiddles[] = $class;
		return $this;
	}

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

	
	public function directory($dir = null){

		if($dir === null){
			$dir = getcwd().DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'routes';
		}

		$annoArrayNew = array();

		$this->routesPath = $dir;	


		if(!file_exists($dir)){
			if(!@mkdir($dir,0777,true)){
				die('RouterClass: Permission denied on create '.$dir);
			}
		}

		$dirs = File::findRecursive($dir);
		
		foreach ($dirs as $key => $value) {
			$info = pathinfo($value);
			$extension = $info['extension'];
			if($extension !== 'php') unset($dirs[$key]);
		}		

		$this->attachRoutes($dirs);	
	}

	public function group($prefix){
		$this->prefix = $prefix;
		return $this;
	}


	public function runClass($class,$method = 'index'){

		$listMiddles = null;
		
		if($this->routeFound === false){
			$requestPar = $this->routerclass->getRequest();
			$routeFound = $this->routerclass->runResource($class,[],$method,null,null,$listMiddles,$requestPar);
		}

	}

	public function resource($methodHTTP = null,$url,$class,$method = 'index'){

		$url = $this->prefix.$url;

		$methodRequest = isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:null;		
		$methodRequest = strtolower($methodRequest);

		if(gettype($methodHTTP) === 'string'){
			$methodHTTPArray = explode(',', $methodHTTP);
		}else if(gettype($methodHTTP) === 'array'){
			$methodHTTPArray = $methodHTTP;
		}

		$allow = false;
		foreach ($methodHTTPArray as $key => $value) {
			if($methodRequest === strtolower($value) || strtolower($value) === '*'  )
				$allow = true;
		}

		
		if($allow === true){
		
			$routeFound = $this->routerclass->resource($url,$class,$method,null,null,$this->listMiddles);
		

					
			if($routeFound === false){
				$this->routeFound = false;
				return $this;
			}


			if($routeFound === true){		

				$this->routeFound = true;
			}else{
				$this->routeFound = false;

			}
		}

		return $this;
	}

	
	public function attachRoutes($dirs = null){

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
			
			$dirEnd = $this->routesPath.ucfirst($value);
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
	
			$currentDir = implode(DIRECTORY_SEPARATOR, $dirArray).DIRECTORY_SEPARATOR;

			$libs = $currentDir.'_class';

			$this->loadLibs($libs);


			$namespace = 'onservice\service\router\routerclass'.$keyNamespace.'';
			ob_start();
			echo 'namespace '.$namespace.';';

			echo $content;
			$content2 = ob_get_contents();
			ob_get_clean();
					
			eval( $content2);		
			eval('$route = new \\'.$namespace.'\\'.ucfirst($className).'($this)'.';');	
						
			$route->_dir = $currentDir;
			
			if($routeRef == '')$routeRef='/';

			$customRoute = false;

			

			if( method_exists($route, 'index') ){
				$routeFound = $this->routerclass->resource($routeRef,$route,'index',null,null,$this->listMiddles);			
				
				
			}
			
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

					preg_match_all('#(.*?)\((.*)\)#s', $anValue, $annotationsMacth);
					
					$anName = isset($annotationsMacth[1][0])?$annotationsMacth[1][0]:null;
					$anValue = isset($annotationsMacth[2][0])?$annotationsMacth[2][0]:null;
					
					$annoArrayNew[$anName] = $anValue;
				}
				

				

				$customRequest = isset($annoArrayNew['request'])?$annoArrayNew['request']:null;

				$customRoute = isset($annoArrayNew['route'])?$annoArrayNew['route']:$customRoute;

				$annotationMethod = isset($annoArrayNew['method'])?trim($annoArrayNew['method']):false;
				$annotationMethod = strtolower($annotationMethod);

				$methodRequest = isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:null;


				$annotationMethod = preg_replace('/^(\'(.*)\'|"(.*)")$/', '$2$3', $annotationMethod);

				

				if($customRequest !== null){
					@eval('$checkRequest = ['.$customRequest.'];');
					$annotationMethod = $checkRequest[0];
					$customRoute = $checkRequest[1];
				}

				
				$modearray = false;
				$checkArray = '';
				if($customRoute != ''){
					if( is_string($customRoute) && strpos($customRoute, '[')!==false || is_array($customRoute)){
						$modearray = true;		
						$customRoute = json_encode($customRoute,JSON_UNESCAPED_SLASHES);
									
					}
				}
					
				
				$annotationMethod = strtolower($annotationMethod);

				if($annotationMethod === 'any') $annotationMethod = null;
				

				if($modearray == true){
			
					@eval('$checkArray = '.$customRoute.';');				
	
					if(is_array($checkArray))
					foreach ($checkArray as $key2 => $value2) {
						$customRouteUnique = $value2;
						if($customRouteUnique !== false){
							$customRouteUnique = trim($customRouteUnique);
							$routeRef_end = $routeRef.''.$customRouteUnique;				

							
							

							$routeFound = $this->routerclass->resource($routeRef_end,$route,$method,$annoArrayNew,false,$this->listMiddles);


						}
					}
				}else{

					$customRoute = preg_replace('/^(\'(.*)\'|"(.*)")$/', '$2$3', $customRoute);

					

					if($customRoute !== false){
						$customRoute = trim($customRoute);
						$routeRef_end = $routeRef.''.$customRoute;	


						
						$routeFound = $this->routerclass->resource($routeRef_end,$route,$method,$annoArrayNew,$annotationMethod,$this->listMiddles);
						
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
			
			$dirEnd = $this->routesPath.ucfirst($value);
			$dirEnd = str_replace('//', '/', $dirEnd);
			$content = file_get_contents($dirEnd);
			$content = str_replace('<?php', '', $content);
			$content = str_replace('?>', '', $content);

			$className = explode('/', $key);
			$className = end($className);
			$keyNamespace = dirname($key);
			$keyNamespace = str_replace('/', '\\', $keyNamespace);
			
			if($keyNamespace == '\\')$keyNamespace = "";

			$namespace = 'onservice\service\router\routerclass'.$keyNamespace.'';
			
			
			eval('$route = new \\'.$namespace.'\\'.ucfirst($className).'($this)'.';');
			
			

			if(isset($route->route)) $routeRef = $routeRef.'/'.$route->route;
			
			if($routeRef == '')$routeRef='/';

			if (method_exists($route, 'error')) {						
				$routeFound = $this->routerclass->resource($routeRef.'/+',$route,'error');

			}

		}
		
	}

}