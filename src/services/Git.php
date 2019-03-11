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
		$repositorySha = null;

		if($urlParse['host'] == 'github.com'){
			$url = $url. '/archive/'.$branch.'.zip';
			$modeGet = 'github';

			$data = array(
				'url'=>$url,					
				'method'=>'get'
			);

			if($username != null && $password != null)
			$data['autenticate'] = "$username:$password";
		}


		if($urlParse['host'] == 'gitlab.com'){
			$modeGet = 'gitlab';

			if($projectid === null){
				die('OnService-Git: missing project ID');
			}
			if($token === null){
				die('OnService-Git: missing token');
			}

			// start: get SHA from Branch
			$data = array(
				'url'=>'https://gitlab.com/api/v4/projects/'.$projectid.'/repository/branches?private_token='.$token,					
				'method'=>'get'
			);
			$response = HttpCon::request($data);
			$listBranchs = json_decode($response);
			foreach ($listBranchs as $key => $value) {
				if($value->name == $branch){
					$sha = $value->commit->id;
				}
			}
			// start: get SHA from Branch
			
			$repositorySha = $repository.'-'.$sha;

			$url = 'https://gitlab.com/api/v4/projects/'.$projectid.'/repository/archive.tar.gz?'.'sha='.$sha.'&private_token='.$token;

			$data = array(
				'url'=>$url,					
				'method'=>'get'
			);
		}



	
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
		
			if (file_exists($filepath.'.tar.gz')) unlink($filepath.'.tar.gz');
			if (file_exists($filepath.'.tar')) unlink($filepath.'.tar');
			
			file_put_contents($filepath.'.tar.gz', $response);		
			$filetarget = $workspace;
			
			$p = new \PharData($filepath.'.tar.gz');
			$p->decompress();

			$phar = new \PharData($filepath.'.tar');
			$phar->extractTo($workspace.DIRECTORY_SEPARATOR, null, true); 
	
			if (file_exists($filepath.'.tar.gz')) unlink($filepath.'.tar.gz');
			if (file_exists($filepath.'.tar')) unlink($filepath.'.tar');
			
			$dirArray = scandir($workspace.DIRECTORY_SEPARATOR);

			$file = null;
			foreach ($dirArray as $key => $value) {
				if(strpos($value, $repositorySha)!== false){
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