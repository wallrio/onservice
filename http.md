


# Http
service for make API and applications RestFull


## Methods


### method: resource

	$server->http->resource(ROUTE,CALLBACK);

Used to respond to an HTTP request

	CALLBACK	=	(URL_PARAMETERS,REQUEST_PARAMETERS)	
	
###### URL_PARAMETERS:
contains the requested values of the route

###### REQUEST_PARAMETERS:
| Key | Value |
|--|--|
| method | get or post |
| url |route URL request|
	

##### Example:
```php
use onservice\CreateServer as CreateServer;
use onservice\services\Http as Http;

// create the server
$server = new CreateServer(	new Http() );

$server->http->resource('/',function($urlPar,$requestPar){
	// your code
	return array(
		'body' 		=> 'body response',
		'code'		=> 200,
		'message'	=> 'Ok',
		'type'		=> 'text/html'
	);
});
```

##### Example 2:

Request in browser
	
	URL: http://address-server-http/users/fulano/32	
	
```php
use onservice\CreateServer as CreateServer;
use onservice\services\Http as Http;

// create the server
$server = new CreateServer(	new Http() );

$server->http->resource('/users/{name}/{age}}',function($urlPar,$requestPar){

	// $urlPar = Array ( [name] => fulano [age] => 32 )
	// $requestPar = Array ( [method] => get [data] => Array() [url] => /users/fulano  )
	
	return array(
		'body' 		=> 'body response',
		'code'		=> 200,
		'message'	=> 'Ok',
		'type'		=> 'text/html'
	);
});
```

##### Example 3:

Request in browser
	
	URL: http://address-server-http/city/brasil/sao-paulo
	
```php
use onservice\CreateServer as CreateServer;
use onservice\services\Http as Http;

// create the server
$server = new CreateServer(	new Http() );

$server->http->resource('/city/*',function($urlPar,$requestPar){

	// $urlPar = Array ( [country] => brasil )
	// $requestPar = Array ( [method] => get [url] => /city/brasil/sao-paulo )
	
	$img = 'STRING_CONTENT_BASE64';
	$img = base64_decode($img);
	
	return array(
		'body' 		=> $img,
		'code'		=> 200,
		'message'	=> 'Ok',
		'type'		=> 'image/png'
	);
});
```



#### special characters:
| Key | Value |
|--|--|
| . | includes all sub-levels (DOT) |
|   | only the current level (Empty) |
| * | includes the current level and all sub-levels (ASTERISK)|

- DOT (.)
```
$server->http->resource('/city/.',FUNCTION);
```

- ASTERISK (*)
```
$server->http->resource('/city/*',FUNCTION);
```

- EMPTY 
```
$server->http->resource('/city',FUNCTION);
```

- WITH BAR (same as the EMPTY) 
```
$server->http->resource('/city/',FUNCTION);
```


#### htaccess example:
For the correct operation of this service, it is necessary to configure url rewriting.
If you use Apache as the server, here's an example of a configuration, which should be inserted into the file .htaccess

```
<IfModule mod_rewrite.c>
	RewriteEngine on	
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-l								
	RewriteRule ^(.+)$ index.php [L]					
</IfModule>
```
-----

### method: routes 
Use your routes in separate files, grouping them into distinct classes.
This method is useful for use when there are a large number of routes.

- First:  Set up a directory to contain the routes

```php
$server->http->routesDir(DIRECTORY_PATH);
```


- Second:  define in an array the classes that will define the routes

```php
$server->http->routes(ARRAY_OF_ROUTES);
```

- Example
```php
$server->http->routes(array('Users','Companies'));
```

#### Example complete

- Code on index.php

```php
use onservice\CreateServer as CreateServer;
use onservice\services\Http as Http;

$server = new CreateServer(	new Http() );

$server->http->routesDir(__DIR__.DIRECTORY_SEPARATOR.'routes');
$server->http->routes(array('Index','Users','Companies'));
```

- Directory/Files (Classes)

```
----app
	 |
	 |---routes
	 |     |
	 |     |---Index.php
	 |     |---Users.php
	 |     |---Companies.php
	 |
	 |
	 |---index.php
```


-- Index Class (optional)
The "Index" class is optional, if you want to create a route in the root it will be necessary to use it.
To use, it is mandatory for the class name to be "Index" and to have a method with the name "index"



```php
namespace onservice\http\routes;

class Index {
	
	// url: /
	public function index(){
		return function($urlPar,$requestPar){

			return array(
				'body' 		=> 'First page',
				'code'		=> 200,
				'message'	=> 'Ok',
				'type'		=> 'text/html'
			);	

		};
	}

	public function __error(){
		return function($urlPar,$requestPar){	
			return array(
				'body' 		=> 'Error 404',
				'code'		=> 404,
				'message'	=> 'Not Found',
				'type'		=> 'application/json'
			);
		};
	}

}
```

- To set an error response for non-existent routes, use the "__error", similar to the above example.


-- Code on Classe Users.php

```php

namespace onservice\http\routes;

class Users // part of route: /users/
{
	function __construct(){}

	// end of route: /user/logon
	public function logon(){

		return function($urlPar,$requestPar){	
			$response = json_encode(array('status'=>'success'));
			return array(
				'body' 		=> $response,
				'code'		=> 200,
				'message'	=> 'Ok',
				'type'		=> 'application/json'
			);
		};
		
	}

	// custom route: /user/get/ID_OF_USER/email
	public function get(){
		return array(
			'route'=>'{id}/email',
			'method' => function($urlPar,$requestPar){
				
				$userid = $urlPar['id'];							
				
				$response = json_encode(array('email'=>$mail));

				return array(
					'body' 		=> $response,
					'code'		=> 200,
					'message'	=> 'Ok',
					'type'		=> 'application/json'
				);
			}
		);
	}
}

```

#### Example the method with multiples routes

```php

public function register(){
		return array(
			// custom route: /register
			array(							
				'method' => function($urlPar,$requestPar){

					$response = json_encode(array('status'=>'missing id of register'));

					return array(
						'body' 		=> $response,
						'code'		=> 403,
						'message'	=> 'Ok',
						'type'		=> 'application/json'
					);	
				}
			),
			
			// custom route: /register/ID
			array(			
				'route'=>'/{id}',
				'method' => function($urlPar,$requestPar){

					$deviceId = $urlPar['id'];

					$response = json_encode(array('status'=>'success'));

					return array(
						'body' 		=> $response,
						'code'		=> 200,
						'message'	=> 'Ok',
						'type'		=> 'application/json'
					);	
				}
			)
		);
	}

```


##### Important
- It is mandatory to use the 'namespace' with the name 'onservice\http\routes'
- The class name is the first level of the route
- The method name is the second level of the route
- To create more levels use the parameter 'route'