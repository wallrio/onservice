<?php


namespace onservice\services\Router; 

use onservice\services\Router as Router;
use onservice\essentials\File as File;

class RouterClass{

	public $server = null;
	public $namespace = 'routerclass';
	private $routesPath = null;

	public function __construct(){
		$this->router = new Router();
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

	
	public function start($dir = null){

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

		$this->attachRoutes($dirs);	
	}

	/**
	 * Anex
	 * @param  [type] $dirs [description]
	 * @return [type]       [description]
	 */
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
			$currentDir = getcwd().DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $dirArray).DIRECTORY_SEPARATOR;

			$libs = $currentDir.'_class';
			$this->loadLibs($libs);


			// $namespace = 'onservice\http\routes'.$keyNamespace.'';
			$namespace = 'onservice\service\Router\RouterClass'.$keyNamespace.'';
			ob_start();
			echo 'namespace '.$namespace.';';

			echo $content;
			$content2 = ob_get_contents();
			ob_get_clean();
		
			eval( $content2);		
			eval('$route = new \\'.$namespace.'\\'.ucfirst($className).'($this->server)'.';');	
						
			$route->_dir = $currentDir;
			
			if($routeRef == '')$routeRef='/';

			$customRoute = false;

			if( method_exists($route, 'index') )
			$routeFound = $this->router->resource($routeRef,$route,'index');			
			
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

							$routeFound = $this->router->resource($routeRef_end,$route,$method,$annoArrayNew);
						}
					}
				}else{

					if($customRoute !== false){
						$customRoute = trim($customRoute);
						$routeRef_end = $routeRef.''.$customRoute;				
						
						$routeFound = $this->router->resource($routeRef_end,$route,$method,$annoArrayNew);
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

			// $namespace = 'onservice\http\routes'.$keyNamespace.'';
			$namespace = 'onservice\service\Router\RouterClass'.$keyNamespace.'';
			
			
			eval('$route = new \\'.$namespace.'\\'.ucfirst($className).'($this->server)'.';');
			
			

			if(isset($route->route)) $routeRef = $routeRef.'/'.$route->route;
			
			if($routeRef == '')$routeRef='/';

			if (method_exists($route, 'error')) {		
				$routeFound = $this->router->resource($routeRef.'/+',$route,'error',$annoArrayNew);
			}

		}
		
	}

}