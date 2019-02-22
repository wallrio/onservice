# Pipe
Sequential operations

## Methods

### pipe

   return $server->pipe(VALUE_INPUT);

- return:
	returns the class context CreateServer;

- VALUE_INPUT: 
	- type: any

### pipeAdd

   return $server->pipeAdd(VALUE_INPUT);

- return:
	returns the class context CreateServer;

- VALUE_INPUT: 
	- type: string|number|array

### pipeService

   return $server->pipeService(VALUE_INPUT);

- return:
	returns the class context CreateServer;

- VALUE_INPUT: 
	- type: service class onservice 


##### Example basic:
```php
use onservice\CreateServer as CreateServer;
use onservice\services\Pipe as Pipe;

$server = new CreateServer(new Pipe);

$server->pipe('Fulano da Silva')
	   ->pipe(function($response,$server){ // remove space				
			return str_replace('-','',$response);	
		});

echo $server->pipe->response();
```



##### Example include value:

> pipeAdd()

```php	
$server->pipe('This')->pipeAdd(' is')->pipeAdd(' a')->pipeAdd(' example');
echo $server->pipe->response();
```

##### Example include value 2:

> pipeAdd()

```php	
$server->pipe(array('contact'=>'email@domain.com'))
	->pipeAdd(array('contact'=>'network1.com/account'))
	->pipeAdd(array('contact'=>'network2.com/account'))
	->pipeAdd(array('contact'=>'network3.com/account'));

```

##### Example include value 3:

> pipeAdd()

```php	
$server->pipe(array(1))
	->pipeAdd(2)
	->pipeAdd(3);

```



##### Example load Services:

> pipeService()

```php	
$server->pipeService(new Router)
	   ->pipeService(new Git)
	   ->pipeService(new Settings);
```




##### Example load Services:
```php	

// example class
class StringTransform{
	public function pipe($response){				
		$response = str_replace(' ', '-', $response);
		return $response;
	}
}

$server->pipe('Fulano da Silva')
	   ->pipe(new StringTransform());

echo $server->pipe->response(); 

```


