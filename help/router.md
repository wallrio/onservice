# Router
service for make API and applications RestFull


## Methods


### method: resource

	$server->router->resource(ROUTE,CALLBACK);

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
use onservice\services\Router as Router;

// create the server
$server = new CreateServer(	new Router() );

$server->router->resource('/',function($urlPar,$requestPar){
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
	
	URL: http://address-server-router/users/fulano/32	
	
```php
use onservice\CreateServer as CreateServer;
use onservice\services\Router as Router;

// create the server
$server = new CreateServer(	new Router() );

$server->router->resource('/users/{name}/{age}}',function($urlPar,$requestPar){

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
	
	URL: http://address-server-router/city/brasil/sao-paulo
	
```php
use onservice\CreateServer as CreateServer;
use onservice\services\Router as Router;

// create the server
$server = new CreateServer(	new Router() );

$server->router->resource('/city/+',function($urlPar,$requestPar){

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
| . | Includes all on the next level (DOT) |
|   | only the current level (Empty) |
| + | includes the current level and all sub-levels (ASTERISK)|

- DOT (.)
```
$server->router->resource('/city/.',FUNCTION);
```

- ASTERISK (*)
```
$server->router->resource('/city/+',FUNCTION);
```

- EMPTY 
```
$server->router->resource('/city',FUNCTION);
```

- WITH BAR (same as the EMPTY) 
```
$server->router->resource('/city/',FUNCTION);
```

#### Verb HTTP OPTIONS:
to work around problems with requests between domain it is possible to use the parameter "ignoreVerbsOptions" to ignore the OPTIONS request of the browser.

```php
$server->router->ignoreVerbsOptions = true;
```

> use this parameter before the request methods

##### Verb HTTP OPTIONS on routes class
use também o parametro "ignoreVerbsOptions" nas anotações dos metodos como "@ignoreVerbsOptions: true"

```php
	/** @route: /url/route/
		@ignoreVerbsOptions: true
	**/ 	
	public function methodRoute($urlPar,$requestPar){	

	}
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

## RouterClass
Use your routes in separate files, grouping them into distinct classes.
This method is useful for use when there are a large number of routes.

- [RouterClass](sub/router_routerclass.md)