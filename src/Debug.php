<?php

namespace onservice; 

class Debug{
	
	function __construct(){
		error_reporting(E_ALL);
		ini_set("display_errors", true);
		ini_set("display_startup_erros",true);
	}

	
}