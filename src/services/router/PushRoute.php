<?php

namespace onservice\services\router; 
use onservice\essentials\Http as Http;

/**
 * Puxa passa a execução para uma rota externa
 */
class PushRoute{



	function __construct($url = null,$httpRequest = null){
		$this->url = $url;
		$this->httpRequest = $httpRequest;

	}

	/**
	 * método obrigatório
	 * @return [array]
	 */
	public  function response(){
		$httpRequest = $this->httpRequest;
		$data = null;


		$method = isset($httpRequest['method'])?$httpRequest['method']:null;
		$data = isset($httpRequest['data'])?$httpRequest['data']:[];
		$headerRequest = isset($httpRequest['header'])?$httpRequest['header']:null;

		if( strtolower( $method ) === 'get'){		
			$fields_string = http_build_query($data);   

			if(strlen($fields_string)>0)
			if(parse_url($this->url, PHP_URL_QUERY)){
				$this->url = $this->url.'&'.$fields_string;
			}else{
				$this->url = $this->url.'?'.$fields_string;
			}	
		}
		
		$requestOptions = [
			'url'=>$this->url,
			'method'=>isset($method)?$method:'get',
			'header'=>$headerRequest,
			'data'=>$data,
		];
	
		$response = Http::request($requestOptions,$header);

		$headerOthers = $header;

		return array(
			'body' 		=> $response,
			'code'		=> $header['Request']['code'],
			'message'	=> $header['Request']['message'],
			'type'		=> $header['Content-Type'],
			'others'    => $headerOthers,
			'finish'    => 1
		);
	}
}