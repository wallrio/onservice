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

		$projectid = isset($parameters['projectid'])?$parameters['projectid']:null;
		$token = isset($parameters['token'])?$parameters['token']:null;

		$urlParse = parse_url($url);

		
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

		$modeGet = null;

		if($urlParse['host'] == 'github.com'){
			$url = $url. '/archive/'.$branch.'.zip';
			$modeGet = 'github';
		}
		if($urlParse['host'] == 'gitlab.com'){
			$modeGet = 'gitlab';

			if($projectid === null){
				die('OnService-Git: missing project ID');
			}
			if($token === null){
				die('OnService-Git: missing token');
			}

			$url = 'https://gitlab.com/api/v4/projects/'.$projectid.'/repository/archive?private_token='.$token;

			// https://gitlab.com/api/v3/projects/11248159/repository/archive?private_token=n1EQF9NYzCHSmvQqxvh9

			// $url = $url. '/-/archive/'.$branch.'/'.$repositoryBranch.'.zip';
		}


		

		$data = array(
			'url'=>$url,					
			'method'=>'get'
		);

		if($username != null && $password != null)
		$data['autenticate'] = "$username:$password";


		$response = HttpCon::request($data);


		if($response == false) return false;

		if($modeGet == 'github'){
			$filename = $branch.'.zip';
			$filepath = $workspace.DIRECTORY_SEPARATOR.$filename;

			file_put_contents($filepath, $response);		
			$filetarget = $workspace;

			Zip::extract($filepath,$filetarget);
			File::rcopy($filetarget.DIRECTORY_SEPARATOR.$repositoryBranch.DIRECTORY_SEPARATOR.$directory, $filetarget.'/');
			unlink($filepath);
			File::rrmdir( $filetarget.DIRECTORY_SEPARATOR.$repositoryBranch.DIRECTORY_SEPARATOR );
		}

		if($modeGet == 'gitlab'){
			$filename = $branch;
			$filepath = $workspace.DIRECTORY_SEPARATOR.$filename;

			//delete temp.tar.gz
			if (file_exists($filepath.'.tar.gz')) {
			    unlink($filepath.'.tar.gz');
			}

			//delete temp.tar
			if (file_exists($filepath.'.tar')) {
			    unlink($filepath.'.tar');
			}

			file_put_contents($filepath.'.tar.gz', $response);		
			$filetarget = $workspace;
			
			$p = new \PharData($filepath.'.tar.gz');
			$p->decompress(); // creates files.tar

		
			$phar = new \PharData($filepath.'.tar');
			$phar->extractTo($workspace.DIRECTORY_SEPARATOR, null, true); // extract all files, and overwrite
			
			//delete temp.tar.gz
			if (file_exists($filepath.'.tar.gz')) {
			    unlink($filepath.'.tar.gz');
			}

			//delete temp.tar
			if (file_exists($filepath.'.tar')) {
			    unlink($filepath.'.tar');
			}

			$dirArray = scandir($workspace.DIRECTORY_SEPARATOR);

			$file = null;
			foreach ($dirArray as $key => $value) {
				if(strpos($value, $repositoryBranch)!== false){
					$file = $value;
					break;
				}
			}

			File::rcopy($workspace.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR.$directory, $workspace.DIRECTORY_SEPARATOR);
			File::rrmdir( $workspace.DIRECTORY_SEPARATOR.$file );

		}


	
		
		
		

		return true;
		
	}


	 
}