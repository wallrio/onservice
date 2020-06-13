<?php 

namespace onservice\services\router; 



class AllowRoutes{

	/**
	 * verifica se a url requisitada esta na lista de url permitidas diretamente
	 * @param  [string] 	$urlRequest      	
	 * @param  [array] 		$listAllowRoutes 	
	 * @return [boolean]                  		
	 */
	static public function check2($urlRequest,$listAllowRoutes){
		$urlArray = explode('/', $urlRequest);
		$urlArray = array_filter($urlArray);
		$urlArray = array_values($urlArray);

		$cont = false;
		foreach ($listAllowRoutes as $key => $value) {

			$valueSplit = explode(':', $value);
			$method = $valueSplit[0];
			$route = $valueSplit[1];
			
			$valueArray = explode('/', $route);
			$valueArray = array_filter($valueArray);
			$valueArray = array_values($valueArray);
			
			if( count($urlArray) === count($valueArray) ){
				foreach ($urlArray as $key2 => $value2) {
					if($urlArray[$key2] === '*' || $urlArray[$key2] === $valueArray[$key2] ){
					
						$cont = true;
					}
				}
			}			
		}

		if($cont === true) return true;
		
		return false;
	}

	static public function check($urlRequest,$listAllowRoutes){
		foreach ($listAllowRoutes as $key => $value) {

			

			$result = self::checkRequest($urlRequest,$value);
				
			if($result === true){
				return true;
			}
		}

		return false;
	}

	static public function checkRequest($route,$requestPath){
		
		$route = str_replace('//', '/', $route);
		$REQUEST_SCHEME = isset($_SERVER['REQUEST_SCHEME'])?$_SERVER['REQUEST_SCHEME']:null;
		$HTTP_HOST = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:null;
		$REQUEST_URI = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:null;
		$REDIRECT_URL = isset($_SERVER['REDIRECT_URL'])?$_SERVER['REDIRECT_URL']:null;
		$SCRIPT_NAME = isset($_SERVER['SCRIPT_NAME'])?$_SERVER['SCRIPT_NAME']:null;
		$method = isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:null;
		$method = strtolower($method);

		$REDIRECT_URL = explode('?', $REDIRECT_URL);
		$REDIRECT_URL = $REDIRECT_URL[0];

		if(empty($REDIRECT_URL)){
			$REDIRECT_URL = $REQUEST_URI;
		
			$REDIRECT_URL = explode('?', $REDIRECT_URL);
			$REDIRECT_URL = $REDIRECT_URL[0];
		}



		$routeArray = explode('/', $route);
		
		
		$requestPathArray = explode('/', $requestPath);
		$routeArray = array_filter($routeArray);
		$routeArray = array_values($routeArray);
		$requestPathArray = array_filter($requestPathArray);
		$requestPathArray = array_values($requestPathArray);

		if(count($routeArray)<1) $routeArray = array('/');
		if(count($requestPathArray)<1) $requestPathArray = array('/');

		$methodList = $requestPathArray[0];
		$methodList = strtolower($methodList);
		$methodList = str_replace(':', '', $methodList);

		unset($requestPathArray[0]);
		$requestPathArray = array_filter($requestPathArray);
		$requestPathArray = array_values($requestPathArray);

		$showerAll = false;
		$shower = true;
		$index = 0;
		$parameters = array();


		$found = false;
		$countFound = 0;
		foreach ($requestPathArray as $key => $value) {
		$asteriskFound = false;

			preg_match_all('/{(.*)}/m', $value , $matches);
			
	

			if( isset($routeArray[$key])){				
				

				if($value == $routeArray[$key]){
					$countFound++;
				}else{
					if($value == '+'){
						$countFound++;
						$asteriskFound = true;
					}else if($value == '.'){
						$countFound++;
					}
				}

			}else{
				

				if($value == '+'){
					$countFound++;
					$asteriskFound = true;
				}else if($value == '.'){
					$countFound++;
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


		
		if( $countFound === count($requestPathArray) ){

			if(strlen($methodList) === 0 || ($methodList === $method))
			return true;
		}
		
		return false;	
	}

}