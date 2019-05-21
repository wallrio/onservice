# Request
Performs HTTP requests


	$response = $server->router->request(URL, PARAMETERS, HEADER_RESPONSE);

| Attribute			| Type 		| description
|-------------------|-----------|---------------------------------------------------|
|URL 				| string 	| destination address of request 					|
|PARAMETERS			| array		| attachments to be sent in the request				|
|HEADER_RESPONSE	| array		| header received from requests 					|



## valores disponiveis para o PARAMETERS

| Attribute		| Type 		| description																 |
|---------------|-----------|----------------------------------------------------------------------------|
|method 		| string 	| get,post,put,delete,... 													 |
|data			| array		| values to be sent															 |
|autenticate	| array		| access information, if the destination is protected with restricted access |	
|follow			| boolean	| define to follow addresses with redirection responses 					 |
|fallback		| function	| defines a function to be executed in the event of a request failure 		 |
|timeout		| integer	| sets a time-out to wait for a response in seconds							 |
|onlyheader		| boolean	| receives the header on return of the method 								 |




##### Example:

```php
use onservice\CreateServer as CreateServer;
use onservice\services\Router as Router;

// create the server
$server = new CreateServer( new Router() );

$response = $server->router->request('wallrio.com');

echo $response;

```

> if not defined, the method will be of type GET

##### Example send Post data:

```php
...

$data = array(
	'username'=>'user',
	'password'=>'123'
);

$response = $server->router->request('domain-to-request.com',array(
	'method' => 'post',	
	'data'=>$data
));
```

##### Example send Post data:

```php
...

$response = $server->router->request('domain-to-request.com',array(
	'method' => 'post',	
	'data'=>$data
));
```


##### Example with authentication:

```php
...

$response = $server->router->request('domain-to-request.com',array(	
	'autenticate'=>'username:password'
));
```


##### Example with fallback:

```php
...

$response = $server->router->request('domain-to-request.com',array(	
	'timeout'=>2,
	'onlyheader'=>true,
	'follow'=>true,
	'custom_url'=>'address-to-fallback.com',
	'fallback'=>function($parameters,$header,$router){
		$custom_value = $parameters['custom_url'];	
		$result = $router->request($custom_value,$parameters);		
		return $result;
	}
));

print_r($response);
```

