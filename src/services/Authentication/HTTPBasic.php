<?php

namespace onservice\services\Authentication;

class HTTPBasic{

	public  $server = null,
			$namespace = 'httpbasic',
			$status = false;

	private $cancelMethod,
			$successMethod,
			$checkMethod;

	function __construct(){
		if(isset($_COOKIE['HTTPBasic']) && $_COOKIE['HTTPBasic'] !== ''){
			$this->status = true;
		}
	}

	public function clean(){		
    	setcookie('HTTPBasic', ''); 
		unset($_SERVER['PHP_AUTH_USER']);
		unset($_SERVER['PHP_AUTH_PW']);
	}

	public function cancel($method){
		$this->cancelMethod = $method;		
	}

	public function success($method){
		$this->successMethod = $method;
	}

	public function check($method){
		$this->checkMethod = $method;
	}


	public function showInput(){
		header('WWW-Authenticate: Basic realm="Authentication"');
		header('HTTP/1.0 401 Unauthorized');
	}

	public function run(){
		
		if (!isset($_SERVER['PHP_AUTH_USER'])  ) {
		    $this->showInput();
		   
		    if($this->cancelMethod != null){
		    	$this->clean();
		    	$method = $this->cancelMethod;
		    	$method();
		    }

		    exit;
		} else {
		
			if($this->checkMethod != null){

				if(isset($_COOKIE['HTTPBasic']) && $_COOKIE['HTTPBasic'] !== ''){
					$method =  $this->successMethod;
					$result = $method((object) array('username'=>$_SERVER['PHP_AUTH_USER'],'password'=>$_SERVER['PHP_AUTH_PW']));

				}else{
					
					$method =  $this->checkMethod;
					$result = $method((object) array('username'=>$_SERVER['PHP_AUTH_USER'],'password'=>$_SERVER['PHP_AUTH_PW']));

					if($result === true){	
						setcookie('HTTPBasic',time());	
						$method =  $this->successMethod;
						$result = $method((object) array('username'=>$_SERVER['PHP_AUTH_USER'],'password'=>$_SERVER['PHP_AUTH_PW']));
					}else{
						if(!isset($_COOKIE['HTTPBasic'])){
							$this->showInput();
						}												
						exit;
					}

				}
			}
	
		}
	} 

}