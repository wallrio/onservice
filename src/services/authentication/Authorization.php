<?php

namespace onservice\services\authentication;

class Authorization{

	public  $server = null,
			$namespace = 'authorization',
			$currentRole,
			$roles,
			$status = false;


	public function current($role){
		$this->currentRole = $role;
	}

	public function setRoles(){
		$args = func_get_args();
		$this->roles = $args;
	}

	public function check(){
		$args = func_get_args();
		if( !in_array($this->currentRole, $args) ){
			return false;
		}
		return true;
	}

	public function allowed(array $roles, $method = null){

		if( !in_array($this->currentRole, $roles) ){
			if($method!= null)
				$method();
			else
				exit;
		}

	}

}