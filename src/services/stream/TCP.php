<?php

namespace onservice\services\Stream;
use onservice\consolecore\PrintConsole as PrintConsole;


 

class TCP{
    
    protected $socket, 
              $socketClient,
              $socketServer,
              $clients = [],
              $clientsCompare = [];

    public $address,
           $port,
           $debug,
           $id,
           $modeOperation,
           $sendReturn,
           $delay = 10000,
           $bufferSize = (1024 * 4);

    public $onConnectMethod,
            $onNewClientMethod,
            $onCloseMethod,
            $onDisconnectMethod,
            $onReceiverLoopMethod,
            $onConnectErrorMethod,
            $onClientDisconnectedMethod,
            $onReceiverMethod,
            $onOpenMethod,
            $onErrorMethod;

    public $seconds = 0;

    /**
     * [conecta ao servidor]
     * @param  [function] $callback        
     */
    public function connect(){

        $this->modeOperation = 'client'; // define o modo de operação cliente ou servidor

        $socketClient = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_set_option($socketClient, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 50000));
        $this->socketClient = $socketClient;
        $con = @socket_connect($socketClient, $this->address, $this->port);     
            


        if($con === false){            
           if($this->onConnectErrorMethod != null){
            $method = $this->onConnectErrorMethod;
            $method($this->address, $this->port);
           }
            return false;
        }
            
        if($this->onConnectMethod !== null){
            $method = $this->onConnectMethod;
            $method((object)array('ip'=>$this->address, 'port'=>$this->port),$this);                      
        } 

        while(true){

            if($this->sendReturn === false)break;
            $result = @socket_read ($socketClient, $this->bufferSize);    
            
            if($this->onReceiverMethod !== null){
                $method = $this->onReceiverMethod;
                $response = $method($result,$this,$socketClient);  
                if($response !== null){
                    $this->send($response);
                }                
            }
            

        }

        if($this->onDisconnectMethod !== null){
            $method = $this->onDisconnectMethod;
            $response = $method((object)array('ip'=>$this->address, 'port'=>$this->port),$this);                  
        }

        return true;
    }



    public function finish($callback = null){
        @socket_close($this->socketServer);

        if($callback != null)
            $callback($this);
    }


    /**
     * [open a listener for connections]
     * server role
     * @param  [function] $callback 
     * @return [null]     
     */
    public function start(){


        $this->modeOperation = 'server';
        // remove o timeout do script
        set_time_limit(0);

        $address = $this->address;
        $port = $this->port;

        // cria o socket
        $socketServer = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        // essencial para correto funcionamento de multiplos clientes, define timeout para a função socket_read
        socket_set_option($socketServer, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 50000));

        // vincula o socket com o endereço ip e porta
        $resultBind = @socket_bind($socketServer, $address, $port); //or die('Could not bind to address');
            
        if($resultBind === false){
            if($this->onErrorMethod != null){
                $method = $this->onErrorMethod;
                $method((object)array('ip'=>$address,'port'=>$port,'socket'=>$socketServer),$this);
            }
            return;
        }

        // inicia a escuta no socket criado
        socket_listen($socketServer);

        $ip = getHostByName(getHostName());
        $this->clients = array(array('socket'=>$socketServer,'ip'=>$ip,'port'=>$port));
        $this->clientsCompare = array(array('socket'=>$socketServer,'ip'=>$ip));

    
        if($this->onOpenMethod !== null){
            $method = $this->onOpenMethod;
            $response = $method((object)array('ip'=>$address,'port'=>$port,'socket'=>$socketServer),$this);                  
        }



        // loop aceitando novas conexões
        while(true){

            if($this->onReceiverLoopMethod !== null){
                $method = $this->onReceiverLoopMethod;
                $response = $method(reset($this->clients),$this);                  
                if($response === false){
                    break;
                }
            }

            
            usleep($this->delay);                    

            $read = [];
            foreach ($this->clients as $key => $value) {                
                $read[] = $value['socket'];
            }


            $this->checkConnection();

            // verifica se há novas mensagens
            if ( socket_select($read, $write, $except, 0) < 1) continue;

            $newConnection = false;
            $input = null;
            // aceita a conexão
            if (in_array($socketServer, $read)){
                $client = socket_accept($socketServer);

                socket_getpeername($client, $ip, $port);
                $clientData = array('ip'=>$ip,'port'=>$port,'socket'=>$client);
                $this->clients[] = $clientData;  
                $this->clientsCompare[] = $clientData;  
                $this->checkNewClients($clientData);         
        
            }



            // loop lendo novas mensagens
            foreach ($this->clients as $key => $value) {
                if($key < 1)continue;
                $socket = $value['socket'];
                $ip = $value['ip'];
                


                $input = @socket_read($socket ,$this->bufferSize  );         

                if($input === false){                    
                    unset($this->clients[$key]);
                    continue;
                } 

                if (!empty($input)){  

                    if($this->socket === null )
                    $this->socket = (object) array('ip'=>null,'port'=>null,'message'=>null);

                    $this->socket->ip = $ip;
                    $this->socket->port = $port;
                    $this->socket->message = $input;
                    
                    if($this->onReceiverMethod !== null){
                        $method = $this->onReceiverMethod;
                        $response = $method($input,$this,$socket);   
                        if($response !== null){
                            $this->sendToSocket($socket,$response);
                        }               
                    }

               

                    

                }  

                        
            }



        }

        socket_close($socketServer);

        if($this->onCloseMethod !== null){
            $method = $this->onCloseMethod;
            $response = $method((object)reset($this->clients),$this);                  
        }

        
    }


    function closeSocket($inputSocket){
        $this->sendReturn = false;
        @socket_close($inputSocket);
        $this->removeClient($inputSocket);
    }


    /**
     * [removes the client from the list of clients]
     * @param  [resource] $inputSocket [socket of client]
     * @return [null]
     */
    function removeClient($inputSocket){
        foreach ($this->clients as $keyDesc => $valueDesc) {            
            if($inputSocket == $valueDesc['socket']){
                unset($this->clients[$keyDesc]);
                
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
    

        if($this->onNewClientMethod !== null){
            usleep(1000);
            $method = $this->onNewClientMethod;
            $response = $method((object)$data,$this);      
            
        }

    }


    /**
     * [verifies that the server is connected]
     * @return [null]
     */
    public function checkConnection(){
        
        if($this->seconds >= (100) ){
            $this->seconds = 0 ;
     
            $read = [];
            foreach ($this->clientsCompare as $key => $value) {
                if( !isset($this->clients[$key]) ){
                    $read[$key] = $this->clientsCompare[$key];
                }
            }
            
            foreach ($read as $key => $value) {
                if($this->onClientDisconnectedMethod !== null){
                    $method = $this->onClientDisconnectedMethod;
                    $response = $method((object)$this->clientsCompare[$key],$this);
                }
                unset($this->clientsCompare[$key]);
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
    public function onDisconnect($callback){               
        $this->onDisconnectMethod = $callback;
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
    public function onOpen($callback){               
        $this->onOpenMethod = $callback;
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

    public function onReceiverLoop($callback){               
        $this->onReceiverLoopMethod = $callback;
    }

    public function onError($callback){               
        $this->onErrorMethod = $callback;
    }

    public function onReceiver($callback){               
        $this->onReceiverMethod = $callback;
    }

    



    /**
     * [Send a message to a specific customer, by socket resource]
     * server role
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
     * [Send message to server ]
     * client role
     * @param  [string] $message [message to be sent to the server]
     * @return [boolean]
     */
    public function send($message){                           
        if($this->modeOperation === 'server') return false;

        $this->sendReturn = @socket_write($this->socketClient, $message, strlen($message) );    
        if($this->sendReturn === false){

            if($this->onDisconnectMethod !== null){
                $method = $this->onDisconnectMethod;
                $response = $method((object)array('ip'=>$this->address, 'port'=>$this->port,'socket'=>$this->socketClient),$this);                  
            }
            
            return false;
        }else{
            return true;
        }
    }

    

    /**
     * [Sends a message to all connected clients]
     * server/client role
     * @param  [string] $msg [message to be sent to the customer]
     * @return [boolean]      
     */
    function sendAll($msg){      
        // executes if called by server
        foreach($this->clients as $client){
            $socket = $client['socket'];            
            @socket_write($socket,$msg,strlen($msg));
        }
        return true;
    }
}