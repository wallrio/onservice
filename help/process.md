# Process
service for create process parallel


### Fork
Creates a parallel process by duplicating the current process

	$server->process->fork($method, $parameters);

- $method =	function to be executed by child process
- $parameters =	informação passada para o processo filho

##### Example basic
```php

	use onservice\CreateServer as CreateServer;
	use onservice\services\Process as Process;

	$server = new CreateServer(	new Process() );

	$server->process->fork(function($parameters,$pid,$stream,$server){
		
		// your code
		
		return RETURN_VALUE;
	},array(
		'name'=>'fulano'
	));


```

- RETURN_VALUE: 
	Description: value to be passed to parent process
	Type: string, number, array, object


##### Capturing the return of the fork
expects and captures the return of forks individually

```php
$server->process->callback(function($response,$stream,$process){	
	
	print_r($response);

});
```


##### Capturing the return of all forks
awaits the completion of all forks and captures the return of all

```php
$server->process->callbackAll(function($response,$stream,$process){	
	
	print_r($response);

});
```


##### Capitalizing fork with infinite loop
creates an infinite loop and captures the return of the forks individually

```php
$server->process->while(function($callback, $stream,$process,$forkChilds){	
		
	sleep(1); // optional
	print_r($callback);

});
```

##### Changing the streaming method
changing the streaming method to write to file

```php
use onservice\services\process\FileStream as FileStream;
$server->process->streamType(new FileStream);
```


##### Defining an identifier
by default the identifier of the stream is changed at each instantiation, so for communication between distinct applications it is necessary to force the identifier.

```php
$server->process->stream->setIdentifier(7);
```


##### Clear stream

```php
$server->process->stream->destroy()
```


##### Criando um processo 'zumbi'
The example below creates a child process and ends soon after, leaving the child process running, regardless of the parent process that created it, usually this 'zombie' process because it will be running endlessly.

```php

	use onservice\CreateServer as CreateServer;
	use onservice\services\Process as Process;

	$server = new CreateServer(	new Process() );

	$server->process->fork(function($parameters,$pid,$stream,$process){
	
		while(true){			
			sleep(1);
			echo '.';			
		}
		
	},array(
		'name'=>'fulano'
	));


```



###### run:
must contain the code to be executed in the process
	
- $parameters =	information passed by the index parameters
- $memory = compartilhamento de informações entre processos
- $server = reference to server class

###### $memory

- save data
	- $memory->save(string,optional index);
	- $server->process->save(string,optional index);

- load data
	- $memory->save(optional index);
	- $server->process->load(optional index);

###### parameters:
option to pass values to the process


### memory

#### Changing the buffer size
by default the buffer size is 16000000 bytes ~ 16 Mb, if you need to increase this size, use the method below by passing the integer value in bytes to the buffer.

- Example for 50 Mb:

	$server->process->memory->setBuffer(50000000);

#### Changing the permission
define the permissions on the memory segment, which are expressed in the same way as the UNIX file system permissions that is given as example 0666.

	$server->process->memory->setPermission(0666);