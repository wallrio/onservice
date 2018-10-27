

# Http
service for make API and applications RestFull


## Methods


### resource

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

$server->http->resource('/city/*}',function($urlPar,$requestPar){

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
 
## htaccess
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

