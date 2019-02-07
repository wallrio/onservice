<?php

namespace console;

class Version{
	

	public $order = 0;

	function __construct(){
		
	}

	/** 
		@order: 0
		@description: show version of onservice 
	**/
	public function index(){
		return OnServiceVersion;
	}





}