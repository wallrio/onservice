<?php

namespace onservice\services\stream;

use onservice\services\stream\TCP as TCP;
use onservice\consolecore\PrintConsole as PrintConsole;

class HTTPClient extends TCP{

	public $commandStart, $headerParameters;

	public function onReceiver($callback = null){
		parent::onConnect(function($dataServer,$stream) {
			$ip = $dataServer->ip;
			$port = $dataServer->port;
			
			echo "\n";
	        echo PrintConsole::write("Connected on $ip:$port",array('bold'=>false,'forecolor'=>'green'));
	        echo "\n";

	        $this->request();

		});

		parent::onReceiver(function($response, $stream,$socket) use ($callback){


			$headerContent = $response;
			$finishHeaderPos = strpos($headerContent, "\r\n\r\n");
			$headerContent = substr($headerContent, 0,$finishHeaderPos);
				
			
			$bodyContent = $response;
			$finishHeaderPos = strpos($bodyContent, "\r\n\r\n");			
			$bodyContent = substr($bodyContent, $finishHeaderPos+4);

			// on gzip enabled
			$bodyContent = file_get_contents('compress.zlib://data:who/cares;base64,'. base64_encode($bodyContent));

			if($this->commandStart == 'header'){
				$headerContent = $this->headertoArray($headerContent);	
				$headerContent = json_encode($headerContent);
				$responseFinish = $headerContent;

			}else if($this->commandStart == 'body'){
				$responseFinish = $bodyContent;		

			}else if($this->commandStart == 'parse'){
				
				$doc = new \DOMDocument();
				$doc->loadHTML($bodyContent);

				$dom = $this->makeDom($doc);
				
				$responseFinish = array(
					'header' => $headerContent,
					'document' => $dom
				);
			}else if($this->commandStart == 'decompress'){
				$responseFinish = $headerContent."\r\n\r\n".$bodyContent;
			}else{
				$responseFinish = $response;
			}

	        if($callback !== null) $callback($responseFinish,$stream);
			

	        $stream->closeSocket($socket);

		});
	}
	

	public function makeDom($element){
		$dom = array();
		foreach ($element->childNodes as $item){
			$nodeName = $item->nodeName;
			$nodeValue = $item->nodeValue;
			
			if($item->childNodes){
				$dom[$nodeName] = $this->makeDom($item);				
			}else{
				$dom[$nodeName] = $nodeValue;
			}
		}

		return $dom;
	}

	public function onDisconnect($callback = null){
		parent::onDisconnect(function($dataServer,$stream) use ($callback){
			$ip = $dataServer->ip;
			$port = $dataServer->port;

	        if($callback !== null) $callback($dataServer,$stream);
		});
	}

	public function connect($command = null,array $headerParameters = array() ){

		$this->commandStart = $command;
		$this->headerParameters = $headerParameters;
			

		parent::connect();
	}



	public function request(){
		$adress = $this->address;
		$port = $this->port;
		$adressArray = explode('/', $adress);
		$ip = $adressArray[0];
		$url = isset($adressArray[1])?$adressArray[1]:'/';

		$Connection = isset($this->headerParameters['Connection'])?$this->headerParameters['Connection']:'keep-alive';
		$CacheControl = isset($this->headerParameters['Cache-Control'])?$this->headerParameters['Cache-Control']:'max-age=0';
		$UserAgent = isset($this->headerParameters['User-Agent'])?$this->headerParameters['User-Agent']:'OnService';
		$Accept = isset($this->headerParameters['Accept'])?$this->headerParameters['Accept']:null;
		$AcceptEncoding = isset($this->headerParameters['Accept-Encoding'])?$this->headerParameters['Accept-Encoding']:null;
		$AcceptLanguage = isset($this->headerParameters['Accept-Language'])?$this->headerParameters['Accept-Language']:null;
		$Cookie = isset($this->headerParameters['Cookie'])?$this->headerParameters['Cookie']:null;

		$array = array();
		$array[] = 'GET / HTTP/1.1';
		$array[] = 'Host: '.$ip.':'.$port;				
		$array[] = 'Connection: '.$Connection;
		$array[] = 'User-Agent: '.$UserAgent;

		if($CacheControl !== null) $array[] = 'Cache-Control: '.$CacheControl;
		if($Accept !== null) $array[] = 'Accept: '.$Accept;
		if($AcceptEncoding !== null) $array[] = 'Accept-Encoding: '.$AcceptEncoding;
		if($AcceptLanguage !== null) $array[] = 'Accept-Language: '.$AcceptLanguage;
		


		$message = implode("\r\n", $array);
		$message = $message ."\r\n\r\n";

		$this->send($message);

	}


	public function headertoArray($message){
 
        $messageArray = explode("\n", $message);
        $method = $messageArray[0];
        $newArray = array();
        $newArray['Request'] = trim($method);

        unset($messageArray[0]);
        array_values($messageArray);
     
        foreach ($messageArray as $key => $value) {
            $array = explode(':', $value,2);
            $val = isset($array[1])?$array[1]:'';
            $newArray[$array[0]] = trim($val);
        }

        if(strpos($method, 'HTTP/') !== false){
            return $newArray;
        }
        else{
            return false;
        }
    }

}