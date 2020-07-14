<?php 

namespace onservice\services\router;

use onservice\essentials\Http as Http;

class Request{

	public function http($url,$parameters = array(),&$header = null){
		$data = $parameters;
		$data['url'] = $url;		
		return Http::request($data,$header,$this);
	}

}