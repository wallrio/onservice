# Stream Using TCP Driver
abstracts the communication using TCP Procotol

## Instance 

```php
use onservice\services\Stream as Stream;
use onservice\services\stream\TCP as TCP;

$stream = new Stream(IP,ADDRESS,new TCP);
```


## Server Mode

### start
abre a conexão

	$stream->start();

### finish
fecha a conexão

```php
	$stream->finish();
```

### onReceiver
executa quando recebe novas mensagens

```php
	$stream->onReceiver(function($message,$scopeStream,$socketClient){
		return "Message to sender";
	});
```

### onReceiverLoop
executa em loop enquanto o servidor estiver ativo

```php
	$stream->onReceiverLoop(function($serverData,$scopeStream){
		return "Message to sender";
	});
```

### onClientDisconnected
executa quando um cliente é desconectado

```php
	$stream->onClientDisconnected(function($clientData,$scopeStream){
		
	});	
```

### onError
executa quando ocorre algum erro

```php
	$stream->onError(function($serverData,$scopeStream){
		
	});	
```

### onOpen
executa quando o servidor iniciar a conexão

```php
	$stream->onOpen(function($serverData,$scopeStream){
		
	});	
```

### onClose
executa quando o servidor fecha a conexão

```php
	$stream->onClose(function($serverData,$scopeStream){
		
	});	
```

### onNewClient
executa quando um cliente se conecta com o servidor

```php
	$stream->onNewClient(function($clientData,$scopeStream){
		
	});	
```

### sendAll
Envia mensagem para todos os clientes conectados.

```php
	$stream->sendAll($message);	
```



### sendToSocket
Envia mensagem para um cliente especifico baseado no socket resource.

```php
	$stream->sendToSocket($socketClient,$message);	
```


## Example 

```php

use onservice\services\Stream as Stream;
use onservice\services\stream\TCP as TCP;

$stream = new Stream('0.0.0.0',8080,new TCP);

$stream->onReceiver(function($message,$scopeStream,$socketClient){

	echo "Received From Client:".$message."\n";

	if($message == 'echo'){
		$stream->sendToSocket($socket,$message);		
		return;
	}
	
});

$stream->start();


```



## Client Mode



### connect
conecta com o servidor

```php
	$stream->connect();	
```


### onConnect
executa quando o cliente se conecta com o servidor

```php
	$stream->onNewClient(function($clientData,$scopeStream){
		
	});	
```


### onDisconnect
executa quando o cliente se desconecta do servidor

```php
	$stream->onDisconnect(function($clientData,$scopeStream){
		
	});	
```

### onConnectError
executa quando ocorre algum erro na conexão com o servidor


```php
	$stream->onConnectError(function($ip,$port){
		
	});	
```

### onReceiver
executa quando recebe novas mensagens


```php
	$stream->onReceiver(function($message,$scopeStream,$socketClient){
		return "Message to sender";
	});	
```


### send
Envia mensagem para todos o servidor.

```php
	$stream->send($message);	
```

## Example 

```php
use onservice\services\Stream as Stream;
use onservice\services\stream\TCP as TCP;

$stream = new Stream('0.0.0.0',8080,new TCP);

$stream->onReceiver(function($message,$scopeStream,$socketClient){

	echo "Received From Server:".$message."\n";

	return "Received!";
	
});

$stream->connect();


```
