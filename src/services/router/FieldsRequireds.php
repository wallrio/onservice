<?php 

namespace onservice\services\router;

use onservice\services\router\Response as Response;

class FieldsRequireds {
	
	public function __construct(){}
	
	static public function check($innerRequest,$fieldsRequireds,$codeHttp = 404,$messageHttp = null){		

		if(!is_array($innerRequest) && !is_object($innerRequest)) $innerRequest = [];

		$fields = [];
		foreach ($fieldsRequireds as $key => $value) {				
			if( !property_exists( $innerRequest, $value) || empty($innerRequest->{$value}) ){
				$fields[] = $value;				
			}
			
		}	

		if( count($fields) < 1)
			return false;


		$response = (new Response);


		$response->body(array('status'=>'missing-parameters','msg'=>$fields));
		$response->code($codeHttp);
		if($messageHttp !== null)$response->message($messageHttp);	
		$response->type('application/json');		
		$response->format('json');

		return $response;
	}
}