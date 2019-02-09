


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

$server->http->resource('/city/+',function($urlPar,$requestPar){

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
$server->http->resource('/city/.',FUNCTION);
```

- ASTERISK (*)
```
$server->http->resource('/city/+',FUNCTION);
```

- EMPTY 
```
$server->http->resource('/city',FUNCTION);
```

- WITH BAR (same as the EMPTY) 
```
$server->http->resource('/city/',FUNCTION);
```

#### Verb HTTP OPTIONS:
to work around problems with requests between domain it is possible to use the parameter "ignoreVerbsOptions" to ignore the OPTIONS request of the browser.

```php
$server->http->ignoreVerbsOptions = true;
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
-----

### method: routes 
Use your routes in separate files, grouping them into distinct classes.
This method is useful for use when there are a large number of routes.


#### Set up a directory to contain the routes


```php
$server->http->routes('DIRECTORY_OF_ROUTES');
```

> DIRECTORY_OF_ROUTES = directories where the classes of the routes will be hosted, if omitted will be defined "http/routes/"

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


![screenshot from 2017-11-25 21-47-41](https://raw.githubusercontent.com/wallrio/onservice/master/help/http/structure-dir.png)


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
			'type'		=> 'text/plain'
		);	
	}

	public function error($urlPar,$requestPa){
		
		return array(
			'body' 		=> 'Error 404',
			'code'		=> 404,
			'message'	=> 'Not Found',
			'type'		=> 'text/plain'

		);
	}

}
```

- To set an error response for non-existent routes, use the method "error", similar to the above example.

- The response of the routes will always be executed in the "index" method of the class.


- File: /users/Logon.php

```php

class Logon {
	
	
	public function index($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /users/logon',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'text/plain'

		);	
	}

}
```

- File: /companies/Logon.php

```php

class Logon {
	
	
	public function index($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /companies/logon',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'text/plain'
		);	
	}

}
```


#### Custom routes 
It is possible to create custom routes within your classes, all classes accept custom routes:

- create methods with a name of your choice, except for the name "index".
- set the method route in annotations (comments)

##### Example

```php

class Logon {
	
	/** @route: /user/wallrio **/
	public function getUserDataFromWallrio($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /logon/user/wallrio',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'text/plain'
		);	
	}

}
```

> the class route above will be "/logon/user/wallrio"

##### Dynamic routes - Example

```php

class Logon {
	
	/** @route: /user/{iduser} **/
	public function getUserDataFromID($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /logon/user/ID_OF_USER',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'text/plain'
		);	
	}

}
```


##### Dynamic routes with fixed routes - Example
It is possible to create dynamic routes, and set some exceptions with other destinations.
To do this create the dynamic route, and above them create the fixed route.

> Fixed routes should always be above the dynamics


##### Multiples routes as array - Example


```php
/**@route: ["/user/{iduser}","/user/all"] **/
```

- Example:

```php

class Logon {
	
	/** @route: ["/user/{iduser}","/user/all"]  **/
	public function getUserDataFromID($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /logon/user/ID_OF_USER',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'text/plain'
		);	
	}

}
```


```php

class Logon {

	/** @route: /user/wallrio **/
	public function getUserDataFromWallrio($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /logon/user/wallrio',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'text/plain'
		);	
	}

	/** @route: /user/{iduser} **/
	public function getUserDataFromID($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /logon/user/ID_OF_USER',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'text/plain'
		);	
	}

}
```

> the class route above will be "/logon/user/ID_OF_USER"

##### Notes on the routes

- The directory name is the first level of the route
- The class name is the second level of the route
- To create more levels use annotattions of methods

- For each class is implicit the "namespace onservice\http\routes\ROUTE_CURRENT"



#### Classes of assistance
To assist in the implementation and coding of your routes, here is a brief step to work with custom classes.

1. in the directory of your route create a subdirectory with the name '_class'

2. In the '_class' directory create your classes, create subdirectories if necessary.

3. directory structure example

```php
/--src
  |
  |--route
     |
     |--example-route
         |
         |--_class
             |
             |--MyClass.php

```

4. Name the 'namespace' of your classes from the word '_class', and follow whatever directory you are in.

- Example to class '/src/route/example-route/_class/MyClass.php'
	
```php
namespace _class;

class MyClass{

}
```

- Example to class '/src/route/example-route/_class/secondDir/secondClass.php'
	
```php
namespace _class\secondDir;

class secondClass{

}
```

5. To use the created class, call it on your route as follows.

```php
use _class\MyClass as MyClass;
```


