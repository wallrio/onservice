# Process
service for create process parallel


## Fork
Creates a parallel process by duplicating the current process

	$process->fork($method, $parameters);

- $method =	function to be executed by child process
- $parameters =	informação passada para o processo filho

##### Example basic
```php

	use onservice\services\Process as Process;

	$process = new Process();

	$process->fork(function($parameters, $pidChild, $streamClass,$contextProcess,$serverClass){
		
		// your code
		
		return RETURN_VALUE;
	});


```

- RETURN_VALUE: 
	- Description: value to be passed to parent process
	- Type: string, number, array, object



##### Example - passing parameters into the child process
```php
	
	use onservice\services\Process as Process;

	$process = new Process();

	$process->fork(function($parameters, $pidChild, $streamClass,$process){
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
$process->callback(function($response,$stream,$process){	
	
	print_r($response);

});
```


##### Capturing the return of all forks
awaits the completion of all forks and captures the return of all

```php
$process->callbackAll(function($response,$stream,$process){	
	
	print_r($response);

});
```


##### Capitalizing fork with infinite loop
creates an infinite loop and captures the return of the forks individually

```php
$process->while(function($callback, $stream,$process,$forkChilds){	
		
	sleep(1); // optional
	print_r($callback);

	// to exit the loop use the return with any value
	// return true 
});
```

> to exit the loop use the return with any value

##### Changing the streaming method
changing the streaming method to write to file

```php
use onservice\services\process\FileStream as FileStream;
$process->streamType(new FileStream);
```


##### Defining an identifier
by default the identifier of the stream is changed at each instantiation, so for communication between distinct applications it is necessary to force the identifier.

```php
$process->stream->setIdentifier(7);
```


##### Clear stream

```php
$process->stream->destroy()
```


##### Creating a Process 'zumbi'
The example below creates a child process and ends soon after, leaving the child process running, regardless of the parent process that created it, usually this 'zombie' process because it will be running endlessly.

```php

	use onservice\services\Process as Process;

	$process = new Process();

	$process->fork(function($parameters, $pidChild, $streamClass,$process){
	
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
	- $stream->save(string,optional index, optional segment);
	- $process->stream->save(string,optional index, optional segment);

- string: values of all types
- index: registry ID
- index: segment ID 


- load data
	- $stream->load(optional index, optional segment);
	- $process->stream->load(optional index, optional segment);


```php
use onservice\services\Process as Process;

$process = new Process();

$process->fork(function($parameters, $pidChild, $streamClass,$process){

	$stream->save('test');
	$server->process->stream->save('test','a1');
	
});


$response = $process->callback(function($responseFromChilds, $streamClass, $forkChildsList, $process){	

	echo $stream->load();
	echo "\n";
	echo $server->process->stream->load('a1');

});

echo $response;

```


## Stream Communication of process

- [Memory](sub/process_memory.md)
- [FileStream](sub/process_filestream.md)

