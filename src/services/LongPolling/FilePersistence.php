<?php

namespace onservice\services\LongPolling;

class FilePersistence implements InterfaceLongPollingPersistence{

	private $workDir;

	public function __construct($workDir = 'workdir'){
		$this->workDir = $workDir;
	}


	public function updateStatus($id = null){
		$dir = $this->workDir.'/'.$id.'';



		$fileStatus = $dir.'/status.json';
		$this->fileStatus = $fileStatus;

		if(file_exists($fileStatus)){		
			$content = file_get_contents($fileStatus);
			$content = json_decode($content);
			$content->time = time();
		}else{
			$content = array('time'=>time());
		}

		file_put_contents($fileStatus, json_encode($content));
	}

	public function cleanMessage($id,$list){
		
		$list = json_decode($list);
		
		$dir = $this->workDir.'/'.$id.'';
		$fileMonitor = $dir.'/messages.json';

		
		if(!file_exists($fileMonitor)) return false;

		$content = file_get_contents($fileMonitor);
		$content = json_decode($content);
			
		foreach ($content as $key => $value) {
			$idmsg = $value->id;
			foreach($list as $key2 => $value2 ){
				if($value2->id === $idmsg){
					unset($content[$key2]);
				}
			}
		}
		
		$dir = $this->workDir.'/'.$id.'';
		$fileMonitor = $dir.'/messages.json';

		file_put_contents($fileMonitor, json_encode($content));

		return true;
	}

	public function checkMessage($id,&$notify){


		$dir = $this->workDir.'/'.$id.'';
		$fileMonitor = $dir.'/messages.json';

	
		if(!file_exists($fileMonitor))
			return false;

			$content = file_get_contents($fileMonitor);
			$content = json_decode($content);

	
		if( count($content) > 0 ){
			$notify = $content;
			return true;
		}

		return false;
	}

	public function users(){


		$dir = $this->workDir.DIRECTORY_SEPARATOR;
		
	
		if(!file_exists($dir))
			return false;

		$dirArray = scandir($dir);

		$users = Array();
		foreach ($dirArray as $key => $value) {
			if($value == '.' || $value == '..' || !is_dir($dir.$value) ) continue;

			if(file_exists($dir.$value.DIRECTORY_SEPARATOR.'status.json')){
				$content = file_get_contents($dir.$value.DIRECTORY_SEPARATOR.'status.json');
				$content = json_decode($content);
				
				$time = $content->time;
				$options = isset($content->options)?$content->options:'{}';
				$status = 'online';
				if( time() > ($time + 7) )
				$status = 'offline';
			}else{				
				$time = '';
				$status = '';
				$options = '{}';
			}
			

			$users[] = array(
				'id'=>$value,
				'status'=>$status,
				'lastactive'=>$time,
				'options'=>$options
			);
		}

			

	
		if( count($users) > 0 ){	
			return $users;
		}

		return false;
	}

	public function recordMessage($from,$to,$message){

		$dir = $this->workDir.'/'.$to.'';
		$fileMonitor = $dir.'/messages.json';

		

		if(file_exists($fileMonitor)){			
			$content = file_get_contents($fileMonitor);
			$content = json_decode($content);
		}else{
			$content = [];
			mkdir($dir,0777,true);
		}

		$content[count($content)] = array('id'=>time(),'receiver'=>$to,'sender'=>$from,'date'=>time(),'message'=>$message);

	
		file_put_contents($fileMonitor, json_encode(array_values($content)) );
		
		return true; 
	}

	public function createUser($id,$options = '{}'){
		$dir = $this->workDir.'/'.$id.'';

		if(!file_exists($dir)) 
			if(@mkdir($dir,0777,true) === false){
    			die(json_encode(array('status'=>'error-no-writtable')));
			};
	
		$fileMonitor = $dir.'/messages.json';
		$this->fileMonitor = $fileMonitor;

		if(!file_exists($fileMonitor)) file_put_contents($fileMonitor, '[]');


		$fileStatus = $dir.'/status.json';
		$this->fileStatus = $fileStatus;

		if(file_exists($fileStatus)){
			$dataContent = file_get_contents($fileStatus);
			$dataContent = json_decode($dataContent);
			$dateCreated = $dataContent->created;
		}

		$data = array(
			'id' => $id,
			'created' => isset($dateCreated)?$dateCreated:time(),
			'time' => time(),
			'options' => $options,
		);

		file_put_contents($fileStatus, json_encode($data));

	}

}