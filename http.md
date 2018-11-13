


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


#### Set up a directory to contain the routes


```php
$server->http->routes(DIRECTORY_OF_ROUTES);
```

- Example

```php
$server->http->routes(__DIR__.DIRECTORY_SEPARATOR.'http'.DIRECTORY_SEPARATOR.'routes');
```



#### Example complete

- Code on index.php

```php
use onservice\CreateServer as CreateServer;
use onservice\services\Http as Http;

$server = new CreateServer(	new Http() );

$server->http->routes(__DIR__.DIRECTORY_SEPARATOR.'http'.DIRECTORY_SEPARATOR.'routes');
```

- Structure of Directory/Files (Classes)


![screenshot from 2017-11-25 21-47-41](https://rawgit.com/wallrio/onservice/help/http/master/structure-dir.png)


- Existing routes confirm structure above

	- /users/
		- is broken exists because there is class /users/Index.php

	- /users/logon
		- is broken exists because there is class /users/Logon.php

	- /companies/list
		- is broken exists because there is class /companies/List.php


- File: /users/Index.php

```php

class Index {
	
	
	public function index($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'application/json'
		);	
	}

	public function error($urlPar,$requestPa){
		
		return array(
			'body' 		=> 'Error 404',
			'code'		=> 404,
			'message'	=> 'Not Found',
			'type'		=> 'application/json'
		);
	}

}
```

- To set an error response for non-existent routes, use the method "error", similar to the above example.

- the response of the routes will always be executed in the "index" method of the class.

- File: /users/Logon.php

```php

class Logon {
	
	
	public function index($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /users/logon',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'application/json'
		);	
	}

}
```

- File: /companies/Logon.php

```php

class Logon {
	
	
	public function index($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /companies/List',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'application/json'
		);	
	}

}
```


#### Custom routes 
Create a property named "route" within the class for the desired route, its value enter the remainder of the custom route.

##### Example

```php

class Logon {
	
	public $route = 'user/{iduser}';
	
	public function index($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /companies/List',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'application/json'
		);	
	}

}
```

> the class route above will be "/logon/user/ID_OF_USER"

##### Important
- The directory name is the first level of the route
- The class name is the second level of the route
- To create more levels use the parameter 'route' inside of class