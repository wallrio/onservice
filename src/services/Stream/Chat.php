<?php

namespace onservice\services\Stream;
use onservice\consolecore\PrintConsole as PrintConsole;


 

class Chat{

    
    protected $socket, 
              $client_socket,
              $clients = [];

    public $address,
           $port,
           $debug,
           $id,
           $modeOperation,
           $createCommands,
           $sendReturn,
           $delay = 0,
           $bufferSize = (1024);
           // $bufferSize = (1024 * 2);

    public $onConnectMethod,
            $onNewClientMethod,
            $onCloseMethod,
            $onServerCloseMethod,
            $onConnectErrorMethod,
            $onClientDisconnectedMethod,
            $onStartMethod,
            $onErrorMethod;

    public $seconds = 0;
    public $clientLoopClose = false;

    /**
     * [conecta ao servidor]
     * @param  [function] $callback        
     */
    public function connect($callback){

        $this->modeOperation = 'client'; // define o modo de operação cliente ou servidor

        $client_socket = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_set_option($client_socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 50000));
        $this->client_socket = $client_socket;
        $con = @socket_connect($client_socket, $this->address, $this->port);     
            
        if($con === false){            
           if($this->onConnectErrorMethod != null){
            $method = $this->onConnectErrorMethod;
            $method($this->address, $this->port);
           }
            return false;
        }
            
        if($this->id !== null) $this->id = time();


        $this->send('id***'.$this->id);
        // usleep(10000);
        // $this->send('get-clients***');
            
        // usleep(1000);

        while(true){

            if($this->clientLoopClose === true)break;
            if($this->sendReturn === false)break;
            $result = socket_read ($client_socket, $this->bufferSize);

            $result = $this->clientCommandsRequests($result);                    

            if($callback!== null){
                $response = $callback($result,$this);
                if($response !== null){
                    $this->send($response);
                }
            }

            $this->checkConnectionClient();
        }

        if($this->onServerCloseMethod !== null){
            $method = $this->onServerCloseMethod;
            $response = $method($this);                  
        }

        return true;
    }



    public function clientCommandsRequests($input){

        if(strpos($input, '***')!== false){

            $inputArray = explode('***', $input);
            $command = $inputArray[0];
            $message = isset($inputArray[1])?$inputArray[1]:null;

            switch($command) {
                case 'onconnect':         

                    if($this->onConnectMethod !== null){
                        $method = $this->onConnectMethod;
                        $method(json_decode($message),$this);              
                        usleep(100);
                    }                   
                    $input = '';
                    break;
                case 'test':              
                    socket_write($socket, 'Test OK!');
                    $input = '';
                    break;
                case 'echo':              
                    socket_write($socket, $message);
                    $input = '';
                    break;
                case 'send-all':
                    $this->sendAll($message);
                    $input = '';
                    break;       
                default:                
                    break;
            }
        }

        return $input;
    }


    public function clientDisconected($dataClient){
        $ip = $dataClient['ip'];
        echo PrintConsole::write(" [".date("d-m-Y H:i:s")."] ",array('bold'=>false,'forecolor'=>'white'));
        echo PrintConsole::write("Client [".$ip."] disconnected",array('bold'=>false,'forecolor'=>'red'));
        echo "\n";

        if($this->onClientDisconnectedMethod !== null){
            $method = $this->onClientDisconnectedMethod;
            $response = $method($dataClient,$this);                  
        }
    }

    /**
     * [open a listener for connections]
     * server role
     * @param  [function] $callback 
     * @return [null]     
     */
    public function listen($callback){

        $this->modeOperation = 'server';
        // remove o timeout do script
        set_time_limit(0);

        $address = $this->address;
        $port = $this->port;

        // cria o socket
        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        // essencial para correto funcionamento de multiplos clientes, define timeout para a função socket_read
        socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 50000));

        // vincula o socket com o endereço ip e porta
        socket_bind($sock, $address, $port) or die('Could not bind to address');
         
        // inicia a escuta no socket criado
        socket_listen($sock);

        $ip = getHostByName(getHostName());
        $this->clients = array(array('socket'=>$sock,'ip'=>$ip));

        echo "\n";
        echo PrintConsole::write(" Server listening on $ip:$port",array('bold'=>false,'forecolor'=>'cian'));
        echo "\n\n";

        if($this->onStartMethod !== null){
            $method = $this->onStartMethod;
            $response = $method(reset($this->clients),$this);                  
        }

        // loop aceitando novas conexões
        while(true){

            usleep($this->delay);
   
            $read = [];
            foreach ($this->clients as $key => $value) {                
                $read[] = $value['socket'];
            }

            // verifica se há novas mensagens
            if ( socket_select($read, $write, $except, 0) < 1) continue;

            $newConnection = false;
            $input = null;
            // aceita a conexão
            if (in_array($sock, $read)){
                $client = socket_accept($sock);

                socket_getpeername($client, $ip, $port);
                $clientData = array('socket'=>$client,'ip'=>$ip,'port'=>$port);
                $this->clients[] = $clientData;  
                $this->checkNewClients($clientData);         
        
            }



            // $this->checkConnection();

            // loop lendo novas mensagens
            foreach ($this->clients as $key => $value) {
                if($key < 1)continue;
                $socket = $value['socket'];
                $ip = $value['ip'];
           
                $input = socket_read($socket ,$this->bufferSize  );         

               

                if (!empty($input)){  

                   if( $this->CommandsDefault($input,$socket) ){
                        continue;
                   }

                   if( $this->Commands($input,$socket) ){
                        continue;
                   }

                    echo PrintConsole::write(" [".date("d-m-Y H:i:s")."] ",array('bold'=>false,'forecolor'=>'white'));
                    echo PrintConsole::write("From:",array('bold'=>false,'forecolor'=>'yellow'));
                    echo $ip;
                    echo "\n";
                    echo PrintConsole::fixedStringSize("",23);
                    echo PrintConsole::write("Message: ",array('bold'=>false,'forecolor'=>'yellow'));
                    echo $input."\n\n";
                
                    $output = $callback($input,$this);

                    if(gettype($output) === 'object')
                        $output = $output->response($input,$this);

                    $checkClientResult = socket_write($socket,$output,strlen($output));


                    $resposeBrowserCheck = $this->checkBrowserRequest($input);


                    if( $resposeBrowserCheck[0] === true ){
                                                    
                            $this->removeSocket($client);
                            socket_close($client);      
                            $this->clientDisconected($clientData);
                                  
                        
                        continue;  
                    }


                    if($checkClientResult === false){                            
                        $this->clientDisconected($this->clients[$key]);
                        unset($this->clients[$key]);
                        continue;
                    }

                }           
            }



        }

        socket_close($sock);

        if($this->onCloseMethod !== null){
            $method = $this->onCloseMethod;
            $response = $method(reset($this->clients),$this);                  
        }
    }


    /**
     * [removes the client from the list of clients]
     * @param  [resource] $inputSocket [socket of client]
     * @return [null]
     */
    function removeSocket($inputSocket){
        $clientsSockets = [];
        foreach ($this->clients as $keyDesc => $valueDesc) {
            $socket = $valueDesc['socket'];
            if($inputSocket == $socket){
                $this->clientDisconected($this->clients[$keyDesc]);
                unset($this->clients[$keyDesc]);
                break;
            }
        }    
    }


    /**
     * [checkNewClients description]
     * @param  [array] $data [new client data]
     * @return [null]
     */
    function checkNewClients($data){
        $socket = $data['socket'];
        $ip = $data['ip'];
        $port = $data['port'];

        echo PrintConsole::write(" [".date("d-m-Y H:i:s")."] ",array('bold'=>false,'forecolor'=>'white'));
        echo PrintConsole::write("New client connected: $ip:$port",array('bold'=>false,'forecolor'=>'green'));
        echo "\n";

        if($this->onNewClientMethod !== null){
            usleep(1000);
            $method = $this->onNewClientMethod;
            $response = $method($data,$this);      
            if($response !== false)
            $this->sendToSocket($socket, $response);
        }

    }

    /**
     * [verifies that the server is connected]
     * @return [null]
     */
    public function checkConnectionClient(){
        if($this->seconds >= (4000) ){
            $this->seconds = 0 ;
        
            $checkClientResult = @socket_write($this->client_socket, "-ping-");
            if($checkClientResult === false){                            
                $this->clientLoopClose = true;
            }   
        }
        $this->seconds++; 
    }

   /**
    * [verifies that the client is connected]
    * @return [null]
    */
    public function checkConnection(){
        
        if($this->seconds >= (4) ){
            $this->seconds = 0 ;
            foreach ($this->clients as $key => $value) {
                $socket = $value['socket'];
                $ip = $value['ip'];
                if($key >0){
                    $checkClientResult = @socket_write($socket, "-ping-");
                    if($checkClientResult === false){                            

                        $this->clientDisconected($this->clients[$key]);
                        
                        unset($this->clients[$key]);

                    }
                }
            }
        }
        $this->seconds++; 
    }


    /**
     * [executes when the server exits]
     * client role
     * @param  [function] $callback 
     * @return [null]
     */
    public function onServerClose($callback){               
        $this->onServerCloseMethod = $callback;
    }
    
    /**
     * [executes when a client is connected]
     * client role
     * @param  [function] $callback 
     * @return [null]
     */
    public function onConnect($callback){               
        $this->onConnectMethod = $callback;
    }

     /**
     * [executes when the server exits]
     * server role
     * @param  [function] $callback 
     * @return [null]
     */
    public function onClose($callback){               
        $this->onCloseMethod = $callback;
    }

    /**
     * [executes when a client is connected]
     * server role
     * @param  [function] $callback 
     * @return [null]
     */
    public function onConnectError($callback){               
        $this->onConnectErrorMethod = $callback;
    }

    /**
     * [executes when the server starts]
     * server role
     * @param  [function] $callback 
     * @return [null]
     */
    public function onStart($callback){               
        $this->onStartMethod = $callback;
    }

    /**
     * [executes when a client is connected]
     * server role
     * @param  [function] $callback 
     * @return [null]
     */
    public function onNewClient($callback){               
        $this->onNewClientMethod = $callback;
    }

    /**
     * [executes when a client is disconnected]
     * server role
     * @param  [function] $callback 
     * @return [null]
     */
    public function onClientDisconnected($callback){               
        $this->onClientDisconnectedMethod = $callback;
    }

    

    /**
     * [Send a message to a specific customer, by socket resource]
     * @param  [resource] $socket [socket of client]
     * @param  [string] $message   [message to be sent to the customer]
     * @return [boolean]         
     */
    public function sendToSocket($socket, $message){                           
        $this->sendReturn = @socket_write($socket, $message, strlen($message) );    
        if($this->sendReturn === false){
            return false;
        }else{
            return true;
        }
    }

    /**
     * [verifies that the client is a browser]
     * server role
     * @param  [string] $message [message from client]
     * @return [array]          [boolean / headerFromBrowser]
     */
    public function checkBrowserRequest($message){
        
        // if (empty($message) ) return array(true,null);      

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
            return array(true,$newArray);
        }
        else{
            return array(false,$newArray);
        }
    }


    /**
     * [Envia para o cliente a sinalização de conexão]
     * @param  [type] $socket [description]
     * @return [type]         [description]
     */
    public function sendOnConnection($socket){        
        $clients = [];
        foreach ($this->clients as $key => $value) {
            unset($value['socket']);
            $clients[$key] = $value;
        }
        $startJson = json_encode(array(
            'clients'=>$clients
        ));                               
        $this->sendToSocket($socket, 'onconnect***'.$startJson);
    }


    /**
     * [Performs standard system commands]
     * server role
     * @param [string] $input  [message received by the customer]
     * @param [resource] $socket [socket of client]
     */
    public function CommandsDefault($input,$socket){


        if(strpos($input, '***')!== false){

            $inputArray = explode('***', $input);
            $command = $inputArray[0];
            $message = isset($inputArray[1])?$inputArray[1]:null;
            $messageArray = explode('|||', $message);

            switch($command) {
                case 'id':         
                    foreach ($this->clients as $key => $value) {
                        if($value['socket'] == $socket){                        
                            $this->clients[$key]['id'] = $message;
                        }
                    }    
                    $this->sendOnConnection($socket);                
                    break;
                case 'get-clients':              
                    

                    break;
                case 'test':              
                    socket_write($socket, 'Test OK!');
                    break;
                case 'echo':              
                    socket_write($socket, $message);
                    break;
                case 'send-to':

                    $id = $messageArray[0];
                    $message = $messageArray[1];                    
                    $this->sendTo($id,$message);
                
                case 'send-all':
                    $this->sendAll($message);
                    
                    break;       
                default:                
                    break;
            }
        }
    }

    /**
     * [executes user-defined commands]
     * server role
     * @param [string] $input  [message received by the customer]
     * @param [resource] $socket [socket of client]
     */
    public function Commands($input,$socket){
        if(is_array($this->createCommands) && count($this->createCommands) > 0)
            if(isset($this->createCommands[$input])){

                reset($this->createCommands[$input]);
                $type =  key($this->createCommands[$input]);
                $message = end($this->createCommands[$input]);
                if($type == 'response'){
                    socket_write($socket, $message);
                }else if($type == 'function'){                    
                    call_user_func($message,$socket,$this);                    
                }
            }
    }


    /**
     * [Send message to server ]
     * @param  [string] $message [message to be sent to the server]
     * @return [boolean]
     */
    public function send($message){                           
        if($this->modeOperation === 'server') return false;

        $this->sendReturn = socket_write($this->client_socket, $message, strlen($message) );    
        if($this->sendReturn === false){
            return false;
        }else{
            return true;
        }
    }

    /**
     * [Send a message to a specific customer]
     * server/client role
     * @param  [string] $id   [id of client]
     * @param  [type] $message [message to be sent to the customer]
     * @return [boolean]
     */
    public function sendTo($id, $message){              
        if($this->modeOperation == 'server'){

            $socket = null;
            foreach ($this->clients as $key => $value) {
                if(isset($value['id']))
                if($value['id'] == $id){                        
                    $socket = $this->clients[$key]['socket'];
                    break;
                }
            }  

            if($socket == null) return false;
            
            $this->sendReturn = @socket_write($socket, $message, strlen($message) );    
            if($this->sendReturn === false){
                return false;
            }else{
                return true;
            }
        }else{
            $message = 'send-to***'.$id.'|||'.$message;
            $this->sendReturn = @socket_write($this->client_socket, $message, strlen($message) );    
            if($this->sendReturn === false){
                return false;
            }else{
                return true;
            }
        }
    }

    /**
     * [Sends a message to all connected clients]
     * server/client role
     * @param  [string] $msg [message to be sent to the customer]
     * @return [boolean]      
     */
    function sendAll($msg){      
        if($this->modeOperation == 'server'){
            // executes if called by server
            foreach($this->clients as $client){
                $socket = $client['socket'];            
                @socket_write($socket,$msg,strlen($msg));
            }
            return true;
        }else{
            // executes if called by client
            $msg = "send-all:::".$msg;
            $this->sendReturn = @socket_write($this->client_socket, $msg,strlen($msg));    
            if($this->sendReturn === false){
                return false;
            }else{
                return true;
            }
        }
    }
}