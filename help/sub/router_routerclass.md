# RouterClass
Use your routes in separate files, grouping them into distinct classes.
This method is useful for use when there are a large number of routes.


##### Example:
```php
use onservice\CreateServer as CreateServer;
use onservice\services\router\RouterClass as RouterClass;

// create the server
$server = new CreateServer(new RouterClass);

$server->routerclass->start('DIRECTORY_OF_ROUTES');
```

> DIRECTORY_OF_ROUTES = directories where the classes of the routes will be hosted, if omitted will be defined "src/routes/"

- Example

```php
$server->routerclass->start();
```

- Example 2

```php
$server->routerclass->start(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'routes');
```



#### Example complete

- Code on index.php

```php
use onservice\CreateServer as CreateServer;
use onservice\services\Router as Router;

$server = new CreateServer(	new Router() );

$server->routerclass->start(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'routes');
```

- Structure of Directory/Files (Classes)


![screenshot from 2017-11-25 21-47-41](https://raw.githubusercontent.com/wallrio/onservice/master/help/sub/http/structure-dir.png)


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

- Note: The valid route annotation must contain (/**) at the beginning and (**/) at the end

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

- For each class is implicit the "namespace onservice\service\router\routerclass\ROUTE_CURRENT"

##### Define Method on route
include the @method attribute with the Http verb to define by which request the method will respond


```php

class Logon {

	/** @route: /user/
		@method: get 
	**/
	public function getUser($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /logon/user/',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'text/plain'
		);	
	}

	/** @route: /user/
		@method: post
	**/
	public function createUser($urlPar,$requestPar){		
		
		return array(
			'body' 		=> 'route: /logon/user/',
			'code'		=> 200,
			'message'	=> 'Ok',
			'type'		=> 'text/plain'
		);	
	}

}
```

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


## PushRoute
Transfers a request to an external route.
Useful for creating a Gateway API.

### Example
```php

use onservice\services\router\PushRoute as PushRoute;

class Logon {

	/** @route: /user **/
	public function user($urlPar,$requestPar){				
		return new PushRoute('http://domain.com:8081/logon/user',$requestPar);	
	}
}	
```

> The above example transfers the request to '/logon/user' to 'http://domain.com:8081/logon/user', and in a transparent way, the request author does not know and does not have access to the end-point 'http://domain.com:8081/logon/user'.

### Example 2
```php

use onservice\services\router\PushRoute as PushRoute;

class Logon {

	/** @route: /user **/
	public function user($urlPar,$requestPar){				
		return new PushRoute('http://domain.com:8081/logon/user');
	}
}	
```

### Example 3
```php

use onservice\services\router\PushRoute as PushRoute;

class Logon {

	/** @route: /user **/
	public function user($urlPar,$requestPar){				
		return new PushRoute('http://domain.com:8081/logon/user',['post'=> ['name'=>'Fulano'] ]);
	}
}	
```

### Example 4
```php

use onservice\services\router\PushRoute as PushRoute;

class Logon {

	/** @route: /user **/
	public function user($urlPar,$requestPar){				
		return new PushRoute('http://domain.com:8081/logon/user?name=Fulano');
	}
}	
```

