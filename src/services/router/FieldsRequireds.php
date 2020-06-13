<?php 

namespace onservice\services\router;

use onservice\services\router\Response as Response;

class FieldsRequireds {
	
	public function __construct(){}
	
	static public function check($innerRequest,$fieldsRequireds){		

		if(!is_array($innerRequest) && !is_object($innerRequest)) $innerRequest = [];

		$fields = [];
		foreach ($fieldsRequireds as $key => $value) {				
			if( !array_key_exists( $value , $innerRequest) || empty($innerRequest->{$value}) ){
				$fields[] = $value;				
			}
			
		}	

		if( count($fields) < 1)
			return false;


		return (new Response)
				->body(array('status'=>'missing-parameters','msg'=>$fields))
				->code(404)
				->type('application/json')			
				->format('json');
	}
}