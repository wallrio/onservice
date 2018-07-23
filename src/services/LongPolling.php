<?php

namespace onservice\services;

class LongPolling{

	private $updatetime = null;
	private $startinfo = null;
	private $persistenceDriver = null;

	public $namespace = 'longpolling';

	public function __construct($persistenceDriver){
		$this->persistenceDriver = $persistenceDriver;
		$this->startinfo = json_encode($_SERVER);
	}

	private $receivedCallbacks = Array();

	public function received($callbacks){
		$this->receivedCallbacks[] = $callbacks;
	}

	/**
	 * @param  [type]
	 * @return [type]
	 */
	public function getQueryString($par = null){
		$get_string = isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:null;
		parse_str($get_string, $get_array);
		return isset($get_array[$par])?$get_array[$par]:null;;
	}

	// configura a conexão
	public function config(array $parameters){
		$this->startinfo = isset($parameters['startinfo'])?$parameters['startinfo']:null;
		$this->updatetime = isset($parameters['updatetime'])?$parameters['updatetime']:5;
	}

	
	// envia mensagem para o cliente
	public function send($content = ''){
		if(is_array($content)){
			echo json_encode($content);
			return true;
		}else if(is_object($content)){
			echo json_encode($content);
			return true;
		}
		echo ($content);
	}


	// atualiza o status de conexão do cliente (time)
	public function updateStatus($id){
		return $this->persistenceDriver->updateStatus($id);
	}
	
	// cria a caixa do cliente
	public function createUser($id,$options = '{}'){
		return $this->persistenceDriver->createUser($id,$options);
	}

	// grava mensagens na caixa do cliente
	public function recordMessage($from,$to,$message){
		return $this->persistenceDriver->recordMessage($from,$to,$message);
	}

	// apara mensagens recebidas pelo cliente
	public function cleanMessage($id,$messagesArray){
		return $this->persistenceDriver->cleanMessage($id,$messagesArray);
	}

	public function users(){
		return $this->persistenceDriver->users();
	}

	
	/**
	 * verifica se existem novas mensagens para o cliente
	 * @param  [type] $id      [description]
	 * @param  [type] &$notify [description]
	 * @return [type]          [description]
	 */
	public function checkMessage($id,&$notify){
		return $this->persistenceDriver->checkMessage($id,$notify);
	}


	// intercepta algumas mensagens
	public function intercept(){

		$signal = $this->getQueryString('signal');
		$id = $this->getQueryString('id');
		$to = $this->getQueryString('to');
		$data = $this->getQueryString('data');
		$options = $this->getQueryString('options');

		if($signal == 'connect'){			
			$this->createUser($id,$options);
			$this->send(array('status'=>'success','fromSignal'=>$signal,'message'=>$this->startinfo));
			exit;

		}else if($signal == 'disconnect'){		
			$this->send(array('status'=>'success','fromSignal'=>$signal));
			exit;

		}else if($signal == 'message'){		

			if($to === 'null'){	
				foreach ($this->receivedCallbacks as $key => $value) {
					$value($id,$data,$this);
				}
				$result = true;
			}else{
				$result = $this->recordMessage($id,$to,$data);
			}

			
			if( $result === true ){
				$this->send(array('status'=>'success','fromSignal'=>$signal,'to'=>$to,'data'=>$data));
			}else{
				$this->send(array('status'=>'error','fromSignal'=>$signal));
			}
			exit;

		}else if($signal == 'receivedconfirm'){		
			$result = $this->cleanMessage($id,$data);

			if( $result === true ){
				$this->send(array('status'=>'success','fromSignal'=>$signal,'data'=>$data));
			}else{
				$this->send(array('status'=>'error','fromSignal'=>$signal));
			}			
			exit;
		}

	}


	/**
	 * @return [type]
	 */
	public function start(){


		$this->intercept();

		$countActive = 0;	
		$timeStart = time();
		do{
			clearstatcache();

			$signal = $this->getQueryString('signal');
			$id = $this->getQueryString('id');
			$data = $this->getQueryString('data');

			// verifica se existe novas mensagem para o id informado
			if( $this->checkMessage($id,$notify) ){							
				$this->send(array('status'=>'message','data'=>$notify));
				exit;
			}

			if( $countActive >= ($this->updatetime*2)  ){
				$this->updateStatus($id);
				$this->send(array('status'=>'success','fromSignal'=>$signal,'id'=>$id));
				exit;
			}

			$countActive++;
			usleep(500000);
			
		}while(true);

	}
	

}