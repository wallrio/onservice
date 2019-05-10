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
	});


```

- RETURN_VALUE: 
	- Description: value to be passed to parent process
	- Type: string, number, array, object



##### Example - passing parameters into the child process
```php

	use onservice\CreateServer as CreateServer;
	use onservice\services\Process as Process;

	$server = new CreateServer(	new Process() );

	$server->process->fork(function($parameters,$pid,$stream,$server){
		$name = $parameters['name'];
		$language = $parameters['language'];

		$nameFull = $name.' da Silva';
		$languageFull = 'pt-'.$language;

		return array('name'=>$nameFull,'language'=>$languageFull);
	},array(
		'name'=>'fulano',
		'language'=>'br'
	));


```


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


##### $stream

- save data
	- $stream->save(string,optional index);
	- $server->process->stream(string,optional index);

- load data
	- $stream->save(optional index);
	- $server->process->stream(optional index);


```php
use onservice\CreateServer as CreateServer;
use onservice\services\Process as Process;

$server = new CreateServer(	new Process() );

$server->process->fork(function($parameters,$pid,$stream,$process){

	$stream->save('test');
	$server->process->stream->save('test','a1');
	
});


$server->process->callback(function($response,$stream,$process){	

	echo $stream->load();
	echo "\n";
	echo $server->process->stream->load('a1');

});
```



