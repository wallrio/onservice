<?php

namespace onservice\services\router; 
use onservice\essentials\Http as Http;

/**
 * Puxa passa a execução para uma rota externa
 */
class PushRoute{



	function __construct($url = null,$requestPar = null){
		$this->url = $url;
		$this->requestPar = $requestPar;

	}

	/**
	 * método obrigatório
	 * @return [array]
	 */
	public  function run(){
		$requestPar = $this->requestPar;
		$data = null;

		if($requestPar !== null ){
			
			if(isset($requestPar['method'])){
				$method = $requestPar['method'];
				$data = isset($requestPar['data'][$method])?$requestPar['data'][$method]:null;
			}else{
				$method = key($requestPar);
				$data = isset($requestPar[$method])?$requestPar[$method]:null;			
			}			
		}

		if( strtolower($method) === 'get'){		
			$fields_string = http_build_query($data);   
			if(parse_url($this->url, PHP_URL_QUERY)){
				$this->url = $this->url.'&'.$fields_string;
			}else{
				$this->url = $this->url.'?'.$fields_string;
			}	
		}
		
		$response = Http::request(array(
			'url'=>$this->url,
			'method'=>isset($method)?$method:'get',
			'data'=>$data
		),$header);
			

		$headerOthers = $header;

		unset($headerOthers['code']);
		unset($headerOthers['message']);
		unset($headerOthers['type']);

		

		return array(
			'body' 		=> $response,
			'code'		=> $header['code'],
			'message'	=> $header['message'],
			'type'		=> $header['type'],
			'others'    => $headerOthers
		);
	}
}