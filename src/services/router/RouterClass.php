<?php

namespace onservice\services\router;

require __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR."version.php";

use onservice\CreateServer as CreateServer;
use onservice\essentials\Http as Http;

use onservice\services\router\QueryFilterAccess as QueryFilterAccess;
use onservice\services\router\RequestFilterAccess as RequestFilterAccess;



class RouterClass{
	
	public $server,
		   $namespace = 'routerclass',		 
		   $version = OnServiceVersion,  
		   $ignoreVerbsOptions = true;

	

	public function __construct(){
		// set CORS to open 
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: *');
		header("Access-Control-Allow-Headers: *");
	}


	public function checkRequest($route,&$args,&$requestPath,$annotationMethod = false){
		
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



		if(dirname($SCRIPT_NAME) === '/')
			$requestPath = $REDIRECT_URL;
		else
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

			if(strlen($annotationMethod) === 0 || ($annotationMethod === $method))
			return true;
		}
		
		return false;	
	}



	public function parse_raw_http_request($input)
{

  // grab multipart boundary from content type header
  preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
  $boundary = isset($matches[1])?$matches[1]:null;

  if($boundary === null) return false;
  // split content by boundary and get rid of last -- element
  $a_blocks = preg_split("/-+$boundary/", $input);
  array_pop($a_blocks);

  // loop data blocks
  foreach ($a_blocks as $id => $block)
  {
    if (empty($block))
      continue;

    // you'll have to var_dump $block to understand this and maybe replace \n or \r with a visibile char

    // parse uploaded files
    if (strpos($block, 'application/octet-stream') !== FALSE)
    {
      // match "name", then everything after "stream" (optional) except for prepending newlines 
      preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
    }
    // parse all other fields
    else
    {
      // match "name" and optional value in between newline sequences
      preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
    }
    $a_data[$matches[1]] = isset($matches[2])?$matches[2]:'';
  }        


  return isset($a_data)?$a_data:[];
}


	public function getRequest(){

		$method = isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:null;
		$codeStatus = isset($_SERVER['REDIRECT_STATUS'])?$_SERVER['REDIRECT_STATUS']:null;
		$QUERY_STRING = isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:null;
		$CONTENT_TYPE = isset($_SERVER['CONTENT_TYPE'])?$_SERVER['CONTENT_TYPE']:null;

		$return['method'] = strtolower($method);
		
		$INPUT = json_decode(file_get_contents("php://input"), true) ?: [];


		if( $return['method'] === 'get' ){

			if(!empty($_GET)){

				$return['request-get'] = $_GET;	

				if(count($_GET)>0)
				foreach ($_GET as $key => $value) {
					$return['request'][$key] = $_GET[$key];							
				}

			}else{	

				$data = (file_get_contents("php://input"));
					

				if(strpos($CONTENT_TYPE, 'application/x-www-form-urlencoded')!== false){
					parse_str($data,$get_array);
				}else if(strpos($CONTENT_TYPE, 'application/json')!== false){
					$get_array = json_decode($data,true);
				}else{							
					$get_array = $this->parse_raw_http_request($data);
				}

				$return['request-get'] = $get_array;

				if( is_array($get_array) && count($get_array)>0)
				foreach ($get_array as $key => $value) {
					$return['request'][$key] = $get_array[$key];							
				}

			}
		}else if( $return['method'] === 'post' ){


			if(!empty($_POST)){

				$return['request-post'] = $_POST;	

				if(count($_POST)>0)
				foreach ($_POST as $key => $value) {
					$return['request'][$key] = $_POST[$key];							
				}

			}else{	


				
				$data = (file_get_contents("php://input"));

				if(strpos($CONTENT_TYPE, 'application/x-www-form-urlencoded')!== false){
					parse_str($data,$get_array);
				}else if(strpos($CONTENT_TYPE, 'application/json')!== false){
					$get_array = json_decode($data,true);
				}else{							
					$get_array = $this->parse_raw_http_request($data);
				}

				$return['request-post'] = $get_array;

				if( is_array($get_array) && count($get_array)>0)
				foreach ($get_array as $key => $value) {
					$return['request'][$key] = $get_array[$key];							
				}
			}

		}else if( $return['method'] === 'put' ){

			$data = (file_get_contents("php://input"));
				
			if(strpos($CONTENT_TYPE, 'application/x-www-form-urlencoded')!== false){
				parse_str($data,$get_array);
			}else if(strpos($CONTENT_TYPE, 'application/json')!== false){
				$get_array = json_decode($data,true);
			}else{							
				$get_array = $this->parse_raw_http_request($data);
			}

			$return['request-put'] = $get_array;

			if( is_array($get_array) && count($get_array)>0)
			foreach ($get_array as $key => $value) {
				$return['request'][$key] = $get_array[$key];							
			}
			
		}else if( $return['method'] === 'delete' ){
			$data = (file_get_contents("php://input"));

			if(strpos($CONTENT_TYPE, 'application/x-www-form-urlencoded')!== false){
				parse_str($data,$get_array);
			}else if(strpos($CONTENT_TYPE, 'application/json')!== false){
				$get_array = json_decode($data,true);
			}else{			
				$get_array = $this->parse_raw_http_request($data);
			}
			
			$return['request-delete'] = $get_array;

			if( is_array($get_array) && count($get_array)>0)
			foreach ($get_array as $key => $value) {
				$return['request'][$key] = $get_array[$key];							
			}
			
		}else{

			$data = (file_get_contents("php://input"));

			if(strpos($CONTENT_TYPE, 'application/x-www-form-urlencoded')!== false){
				parse_str($data,$get_array);
			}else if(strpos($CONTENT_TYPE, 'application/json')!== false){
				$get_array = json_decode($data,true);
			}else{			
				$get_array = $this->parse_raw_http_request($data);
			}
			

			if( is_array($get_array) && count($get_array)>0)
			foreach ($get_array as $key => $value) {
				$return['request'][$key] = $get_array[$key];							
			}
			


		}

		$headers = apache_request_headers();
		$return['header'] = [];
		foreach ($headers as $header => $value) {
		    $return['header'][strtolower($header)] = $value;
		}

		return $return;
	}

	public function checkContinue($response){
		
		if(method_exists($response, 'response'))
		$response = $response->response();

	

		if( isset($response['finish']) && $response['finish'] === 1 ){
			return false;
		}
		

		return true;
	}

	public function resource($route,$callback,$methodMode = false,$annoArrayNew = array(),$annotationMethod = false,$listMiddles = []){
		
		
		
		$requestPar = $this->getRequest();
		
		if($this->checkRequest($route,$parameters,$requestPath,$annotationMethod) === false){
			return false;
		}else{
			

				$routeGet = $route;
				
			foreach ($parameters as $key => $value) {
				$routeGet = str_replace('{'.$key.'}', $value, $routeGet);
			}
			

			$routeGet = str_replace('/+', '', $routeGet);			
			$routeGet = str_replace('/.', '', $routeGet);		

			if( substr($routeGet, strlen($routeGet)-1)==='/' )
			$routeGet = substr($routeGet, 0,strlen($routeGet)-1);

			$routeTarget = str_replace($routeGet, '', $requestPath);
			$routeTarget = str_replace('//', '/', $routeTarget);


			$requestPar['url'] = $requestPath;
			$requestPar['endpoint'] = $routeTarget;


		
			$response = $this->runResource($callback,$parameters,$methodMode,$annoArrayNew,$annotationMethod ,$listMiddles,$requestPar);

			exit;
			return $response;
		}
	
	}


	public function runResource($callback,$parameters,$methodMode,$annoArrayNew = array(),$annotationMethod = false,$listMiddles = [],$requestPar = []){
		
		

		header('Server: onService/'.$this->version);

			$parametersHandle = new QueryFilterAccess($parameters);
				$requestHandle = new RequestFilterAccess($requestPar);

	


			// Middle ------------------------------------------------
			$blockResponse = false;
			$parametersNext = new Container;
			if(is_array($listMiddles) || is_object($listMiddles))
			foreach ($listMiddles as $keyMiddle => $classMiddle) {
				$controllerMiddle = new $classMiddle();

				

				$resultMiddle = $controllerMiddle->onRequest($parametersHandle,$requestHandle,$parametersNext);


				if($this->checkContinue($resultMiddle) === false){
					$blockResponse = true;
				}
	

				if(method_exists($resultMiddle, 'response')){	

					$response = $resultMiddle->response();
					$header = isset($response['header'])?$response['header']:null;
					if(is_array($header) && count($header)>0){				
						unset($header['Transfer-Encoding']);

						foreach ($header as $key => $value) {									
							header($key.': '.$value);
						}
					}
				}

			}


						
			$requestHandle->set('middle',$parametersNext);


			if($blockResponse === false){
				
				$parametersHandle = new QueryFilterAccess($parameters);
				$requestHandle = new RequestFilterAccess($requestPar);

				if( $methodMode !== false && $methodMode !== null ){				
					if(method_exists($callback, $methodMode))
					$response = $callback->$methodMode($parametersHandle,$requestHandle,$parametersNext);
				}else{				
					$response = $callback($parametersHandle,$requestHandle,$parametersNext);
				}


		

				if(isset($response))
				if(method_exists($response, 'response'))
				$response = $response->response();

			}
			
			if(gettype($response) === 'object'){
				$methodMode = 'index';
				if(method_exists($response, $methodMode)){

					$parametersHandle = new QueryFilterAccess($parameters);
					$requestHandle = new RequestFilterAccess($requestPar);

					$response = $response->$methodMode($parametersHandle,$requestHandle,$parametersNext);
					if(isset($response))
					if(method_exists($response, 'response'))
					$response = $response->response();
				}else{
					$response = [];
				}
			}

			if($response === false){
				return false;
			}


			$body = isset($response['body'])?$response['body']:null;
			$code = isset($response['code'])?$response['code']:200;
			$protocol = isset($response['protocol'])?$response['protocol']:'HTTP/1.0';
			$message = isset($response['message'])?$response['message']:'Not Found';
			$contentype = isset($response['type'])?$response['type']:'text/html';
			$header = isset($response['header'])?$response['header']:null;

		
			header($protocol.' '.$code.' '.$message);
			
			
			if(gettype($body) !== 'string')$body = print_r($body,true);

			$bodyLength = strlen($body);
			header('Content-Length: '.$bodyLength);
			header('Content-Type:'.$contentype);

			

			if(is_array($header) && count($header)>0){		
				foreach ($header as $key => $value) {
					
					header($key.':'.$value);
				}
			}



			if($body) echo $body;
	
			return true;
	}

}