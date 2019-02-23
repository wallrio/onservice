# Stream Using TCP Driver
abstracts the communication using TCP Procotol

## Instance 

```php
use onservice\CreateServer as CreateServer;
use onservice\services\Stream as Stream;
use onservice\services\stream\TCP as TCP;

$server = new CreateServer(	new Stream(IP,ADDRESS,new TCP) );
```


## Server Mode

### start
abre a conexão

	$server->stream->start();

### finish
fecha a conexão

```php
	$server->stream->finish();
```

### onReceiver
executa quando recebe novas mensagens

```php
	$server->stream->onReceiver(function($message,$scopeStream,$socketClient){
		return "Message to sender";
	});
```

### onReceiverLoop
executa em loop enquanto o servidor estiver ativo

```php
	$server->stream->onReceiverLoop(function($serverData,$scopeStream){
		return "Message to sender";
	});
```

### onClientDisconnected
executa quando um cliente é desconectado

```php
	$server->stream->onClientDisconnected(function($clientData,$scopeStream){
		
	});	
```

### onError
executa quando ocorre algum erro

```php
	$server->stream->onError(function($serverData,$scopeStream){
		
	});	
```

### onOpen
executa quando o servidor iniciar a conexão

```php
	$server->stream->onOpen(function($serverData,$scopeStream){
		
	});	
```

### onClose
executa quando o servidor fecha a conexão

```php
	$server->stream->onClose(function($serverData,$scopeStream){
		
	});	
```

### onNewClient
executa quando um cliente se conecta com o servidor

```php
	$server->stream->onNewClient(function($clientData,$scopeStream){
		
	});	
```

### sendAll
Envia mensagem para todos os clientes conectados.

```php
	$server->stream->sendAll($message);	
```



### sendToSocket
Envia mensagem para um cliente especifico baseado no socket resource.

```php
	$server->stream->sendToSocket($socketClient,$message);	
```


## Example 

```php
use onservice\CreateServer as CreateServer;
use onservice\services\Stream as Stream;
use onservice\services\stream\TCP as TCP;

$server = new CreateServer(	new Stream('0.0.0.0',8080,new TCP) );

$server->stream->onReceiver(function($message,$scopeStream,$socketClient){

	echo "Received From Client:".$message."\n";

	if($message == 'echo'){
		$stream->sendToSocket($socket,$message);		
		return;
	}
	
});

$server->stream->start();


```



## Client Mode



### connect
conecta com o servidor

```php
	$server->stream->connect();	
```


### onConnect
executa quando o cliente se conecta com o servidor

```php
	$server->stream->onNewClient(function($clientData,$scopeStream){
		
	});	
```


### onDisconnect
executa quando o cliente se desconecta do servidor

```php
	$server->stream->onDisconnect(function($clientData,$scopeStream){
		
	});	
```

### onConnectError
executa quando ocorre algum erro na conexão com o servidor


```php
	$server->stream->onConnectError(function($ip,$port){
		
	});	
```

### onReceiver
executa quando recebe novas mensagens


```php
	$server->stream->onReceiver(function($message,$scopeStream,$socketClient){
		return "Message to sender";
	});	
```


### send
Envia mensagem para todos o servidor.

```php
	$server->stream->send($message);	
```

## Example 

```php
use onservice\CreateServer as CreateServer;
use onservice\services\Stream as Stream;
use onservice\services\stream\TCP as TCP;

$server = new CreateServer(	new Stream('0.0.0.0',8080,new TCP) );

$server->stream->onReceiver(function($message,$scopeStream,$socketClient){

	echo "Received From Server:".$message."\n";

	return "Received!";
	
});

$server->stream->connect();


```
