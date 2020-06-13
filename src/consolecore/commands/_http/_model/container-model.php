<?php



class Index {

	function __construct($server){}


	public function index($urlPar,$requestPar){

		$response = json_encode(array('status'=>'missing parameters'));
		$code = 202;
		$message = 'success';

		return array(
			'code'=>$code,
			'message'=>$message,
			'body'=>$response,
			'type'=>'application/json'
		);
	}

	public function error($urlPar,$requestPa){
		
		return array(
			'body' 		=> 'Error 404',
			'code'		=> 404,
			'message'	=> 'Not Found',
			'type'		=> 'text/plain'
		);
	}
	
	/** @route: /new-route/ **/
	public function newRoute($urlPar,$requestPar){
			
		$response = json_encode(array('status'=>'success'));	
		$code = 202;
		$message = 'success';

		return array(
			'code'=>$code,
			'message'=>$message,
			'body'=>$response,
			'type'=>'application/json'
		);
	}

}