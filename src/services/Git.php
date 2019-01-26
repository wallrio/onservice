<?php

namespace onservice\services;

use onservice\essentials\Http as HttpCon;
use onservice\essentials\Zip as Zip;
use onservice\essentials\File as File;

class Git{

	public $server = null;
	public $namespace = 'git';

	public function __construct(){}



	public function get(array $parameters){
		$url = isset($parameters['url'])?$parameters['url']:null;
		$branch = isset($parameters['branch'])?$parameters['branch']:'master';
		$workspace = isset($parameters['workspace'])?$parameters['workspace']:null;
		$username = isset($parameters['username'])?$parameters['username']:null;
		$password = isset($parameters['password'])?$parameters['password']:null;
		$clearworkspace = isset($parameters['clearworkspace'])?$parameters['clearworkspace']:false;
		$directory = isset($parameters['directory'])?$parameters['directory']:'';

		if( $clearworkspace === true ){
			File::rrmdir( $workspace );
		}

		if(!file_exists($workspace)){
			if(!@mkdir($workspace,0777,true)){
				die("<strong>Permission denied, not created the directory</strong> ".$workspace);
			}
		}
	
		$url = str_replace('.git', '', $url);

		$urlArray = explode('/', $url);
		$repository = end($urlArray);
		$repositoryBranch = $repository.'-'.$branch;		

		$url = $url. '/archive/'.$branch.'.zip';

		$data = array(
			'url'=>$url,					
			'method'=>'get'
		);

		if($username != null && $password != null)
		$data['autenticate'] = "$username:$password";

		$response = HttpCon::request($data);
		if($response == false) return false;

		$filename = $branch.'.zip';
		$filepath = $workspace.DIRECTORY_SEPARATOR.$filename;
		file_put_contents($filepath, $response);
		$filetarget = $workspace;
		Zip::extract($filepath,$filetarget);
		File::rcopy($filetarget.DIRECTORY_SEPARATOR.$repositoryBranch.DIRECTORY_SEPARATOR.$directory, $filetarget.'/');
		unlink($filepath);
		File::rrmdir( $filetarget.DIRECTORY_SEPARATOR.$repositoryBranch.DIRECTORY_SEPARATOR );

		return true;
		
	}


	 
}