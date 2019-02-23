<?php

namespace onservice\services\stream;

use onservice\services\stream\TCP as TCP;
use onservice\consolecore\PrintConsole as PrintConsole;

	
class HTTPServer extends TCP{

	private $onReceiverForce = null;
	public $includeHTML = '';

	function __construct(){
		parent::onError(function($dataServer,$stream) {
			$ip = $dataServer->ip;
			$port = $dataServer->port;
			
			echo "\n";
	        echo PrintConsole::write(" Error on open $ip:$port: ",array('bold'=>false,'forecolor'=>'red'));
	        echo "\n\n";

		});
	}
	
	public function onClientDisconnected($callback = null){
		parent::onClientDisconnected(function($dataClient,$stream) use ($callback){
			$ip = $dataClient->ip;
			$port = $dataClient->port;
			$socket = (string) $dataClient->socket;

	        if($callback !== null) $callback($dataClient,$stream);
		});
	}


	public function onNewClient($callback = null){
		parent::onNewClient(function($dataClient,$stream) use ($callback){
			$ip = $dataClient->ip;
			$port = $dataClient->port;
			$socket = (string) $dataClient->socket;

	        if($callback !== null) $callback($dataClient,$stream);
		});
	}

	public function onClose($callback = null){
		parent::onClose(function($dataServer,$stream) use ($callback){

			$ip = $dataServer->ip;
			$port = $dataServer->port;
			$socket = (string) $dataServer->socket;

	        if($callback !== null) $callback($dataServer,$stream);
		});
	}

	public function onOpen($callback = null){
		parent::onOpen(function($dataServer,$stream) use ($callback){
			$ip = $dataServer->ip;
			$port = $dataServer->port;
			$socket = (string) $dataServer->socket;

			echo "\n";
	        echo PrintConsole::write(" Server listening on $ip:$port ",array('bold'=>false,'forecolor'=>'cian'));
	        echo "\n\n";

	        if($callback !== null) $callback($dataServer,$stream);
		});
	}

	

	public function onReceiver($callback){
		$this->onReceiverForce = $callback;
	}

	public function start($pathRoot = null){
		
			parent::onReceiver(function($messageInput,$stream,$socketClient) use ($pathRoot) {

		        echo PrintConsole::write(" Request:",array('bold'=>false,'forecolor'=>'yellow'));
		        $resposeBrowserCheck = $this->checkBrowserRequest($messageInput);
		        
		        $EndPoint = null ;
		        if($resposeBrowserCheck !== false){

			        $request = $resposeBrowserCheck['Request'];
			        $posInit = strpos($request, ' ');
			        $posFinish = strpos($request, ' HTTP/');
			        $EndPoint = substr($request, $posInit,$posFinish-$posInit);
			        $EndPoint = trim($EndPoint);
			        $messageInput = trim($EndPoint);
		        }
		   
		        echo " ".$messageInput."\n";

		        if($EndPoint == null)
		        	$EndPoint = $messageInput;

		        if($this->onReceiverForce === null)
			        $messageOutput = $this->process($pathRoot,$EndPoint,$stream);
				else{
					$callback = $this->onReceiverForce;
					$messageOutput = $callback($EndPoint,$stream);
				}



				$code = isset($messageOutput['code'])?$messageOutput['code']:202;
				$message = isset($messageOutput['message'])?$messageOutput['message']:'Ok';
				$contentType = isset($messageOutput['content-type'])?$messageOutput['content-type']:'text/html';
				$date = isset($messageOutput['date'])?$messageOutput['date']:date("D M j G:i:s Y");
				$body = isset($messageOutput['body'])?$messageOutput['body']:'';


				$body = $body.$this->includeHTML;

				$this->header = "HTTP/1.1 ".$code." ".$message." \r\n" .
		            "Date: ".$date." \r\n" .
		            "Server: onService \r\n" .		        
		            "Content-Type: ".$contentType." \r\n\r\n";
			

				$stream->sendToSocket($socketClient,$this->header.$body);
				$stream->closeSocket($socketClient);

			});

		
		parent::start();

	}


	public function process($pathRoot = 'http/',$url,$stream){
		
			if($url == '/'){
				$url = 'index.html';
				$filename = $pathRoot.''.$url;
				if(!file_exists($filename)) $url = 'index.php';
			}

			$filename = $pathRoot.''.$url;
			$filename = str_replace('//', '/', $filename);

			if(file_exists($filename)){

				ob_start();
				include $filename;
				$content = ob_get_contents();
				ob_end_clean();
			
				$code = 200;
				$message = 'Ok';
				$contentType = $stream->mime_content_type($filename);
			}else{
				$code = 404;
				$message = 'Not Found';
				$content = '';
				$contentType = '';
			}

			return array(
				'code' => $code,
				'message' => $message,
				'body' => $content,
				'content-type' => $contentType
			);
		
	}

	public function includeHTML($message){
		$this->includeHTML = $message;
	}

	public function checkBrowserRequest($message){
 
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



	public function mime_content_type($filename) {

		        $mime_types = array(

		            'txt' => 'text/plain',
		            'htm' => 'text/html',
		            'html' => 'text/html',
		            'php' => 'text/html',
		            'css' => 'text/css',
		            'js' => 'application/javascript',
		            'json' => 'application/json',
		            'xml' => 'application/xml',
		            'swf' => 'application/x-shockwave-flash',
		            'flv' => 'video/x-flv',

		            // images
		            'png' => 'image/png',
		            'jpe' => 'image/jpeg',
		            'jpeg' => 'image/jpeg',
		            'jpg' => 'image/jpeg',
		            'gif' => 'image/gif',
		            'bmp' => 'image/bmp',
		            'ico' => 'image/vnd.microsoft.icon',
		            'tiff' => 'image/tiff',
		            'tif' => 'image/tiff',
		            'svg' => 'image/svg+xml',
		            'svgz' => 'image/svg+xml',

		            // archives
		            'zip' => 'application/zip',
		            'rar' => 'application/x-rar-compressed',
		            'exe' => 'application/x-msdownload',
		            'msi' => 'application/x-msdownload',
		            'cab' => 'application/vnd.ms-cab-compressed',

		            // audio/video
		            'mp3' => 'audio/mpeg',
		            'qt' => 'video/quicktime',
		            'mov' => 'video/quicktime',

		            // adobe
		            'pdf' => 'application/pdf',
		            'psd' => 'image/vnd.adobe.photoshop',
		            'ai' => 'application/postscript',
		            'eps' => 'application/postscript',
		            'ps' => 'application/postscript',

		            // ms office
		            'doc' => 'application/msword',
		            'rtf' => 'application/rtf',
		            'xls' => 'application/vnd.ms-excel',
		            'ppt' => 'application/vnd.ms-powerpoint',

		            // open office
		            'odt' => 'application/vnd.oasis.opendocument.text',
		            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		        );
		        $ar = explode('.',$filename);
		        $ext = strtolower(array_pop($ar));

		        if (array_key_exists($ext, $mime_types)) {
		            return $mime_types[$ext];
		        }
		        elseif (function_exists('finfo_open')) {
		            $finfo = finfo_open(FILEINFO_MIME);
		            $mimetype = finfo_file($finfo, $filename);
		            finfo_close($finfo);
		            return $mimetype;
		        }
		        else {
		            return 'application/octet-stream';
		        }
		    }
		

}