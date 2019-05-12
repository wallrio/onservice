<?php

namespace console;

class Version{
	
	public $description = 'Exibe a versão do OnService';
	public $order = 0;

	function __construct(){}

	public function index(){
		return OnServiceVersion;
	}

	

}