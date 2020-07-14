<?php 

namespace onservice\services\router;

class RequestFilterAccess{

	private $parameters;

	public function __construct($parameters){

		$parameters = json_encode($parameters);
		$parameters = json_decode($parameters,false);

		$this->parameters =(object) $parameters;
	}


	public function set($key,$content){


		if($key === null) return $this->parameters;

		$keyArray = explode('/', $key);

		$join = '';
		$index = 0;
		foreach ($keyArray as $key => $value) {
			$join .= $value.'->';

			$joinAdjust = substr($join, 0,strlen($join)-2);

			eval(' if(!isset($this->parameters->'.$joinAdjust.')) $this->parameters->'.$joinAdjust.' = new \StdClass ;');

			$index++;
		}
		$joinAdjust = substr($join, 0,strlen($join)-2);
		
		eval('$this->parameters->'.$joinAdjust.' = $content ;');

	}

	
	public function get($key = null){

		if( str_replace(' ', '', $key) === '' || $key === null) return $this->parameters;

		$keyArray = explode('/', $key);

		$join = '';
		foreach ($keyArray as $key => $value) {
			$join .= '->'.$value;
		}
		$join = substr($join, 2);
		
		eval('$ret = isset($this->parameters->'.$join.')?$this->parameters->'.$join.':null;');

		$ret = json_encode($ret);
		$ret = urldecode($ret);
		$ret = json_decode($ret);

		return $ret;
	}
}