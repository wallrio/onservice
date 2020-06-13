<?php 

namespace onservice\services\router;

class Container{

	private $parameters;

	public function __construct(){
		
		$this->parameters = new \StdClass;


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
			if( is_numeric(substr($value, 0,1)) )
				return null;
			$join .= '->'.$value;
		}
		$join = substr($join, 2);
		
		eval('$ret = isset($this->parameters->'.$join.')?$this->parameters->'.$join.':null;');

		return $ret;
	}

}