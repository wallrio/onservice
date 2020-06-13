
# Router API
This approach is useful for building the RESTful API.

To create an endpoint or a route it is necessary to include the 'resource' method

	$router->resource(METHOD_HTTP,ROUTE,CLASS,METHOD);


- METHOD_HTTP	
	-  HTTP verb (ex: 'GET','POST','DELETE','POST')

- ROUTE 		
	- resource route (ex: '/user', '/company')

- CLASS 		
	- class PHP to response the requestion

- METHOD 		
	- 	class method that will respond to the request, if omitted, 'index' is the method that will respond to the request.


##### Example:

```php
use onservice\services\Router as Router;
use example_namespace\routes\ErrorRoute as ErrorRoute;
use example_namespace\routes\Users as Users;

$router = new Router();

$router->resource('POST','',new Users,'create');
$router->resource('GET','',new Users,'getUser');
		
$router->runClass(new ErrorRoute);

```

- runClass
> If no route is reached, runClass will respond to the request, in the example above the ErrorRoute class will respond to the request using the index method.


#####  class ErrorRoute example:

```php
namespace application\routes;

use onservice\services\router\Response as Response;

class ErrorRoute{
	public function index($uri,$http){

		return (new Response)
			->body(['status'=>'route-not-found'])
			->code(404)
			->type('application/json')			
			->format('json');
	}

}
```

#####  class Users example:

```php
namespace application\routes;

use onservice\services\router\Response as Response;

use example_namespace\routes\users\GetUser as GetUser;

class Users{

	public function Create($uri,$http,&$container){
		return (new Response)
			->body(['status'=>'success','msg'=>'create'])
			->code(200)
			->type('application/json')			
			->format('json');	
	}

	public function getuser($uri,$http,&$container){
		return new GetUser;		
	}

}
```

#####  class GetUser example:

```php
namespace application\routes\users;

class GetUser{

	public function index($uri,$http,&$container){

		return (new Response)
			->body(['status'=>'success','msg'=>'get'])
			->code(200)
			->type('application/json')			
			->format('json');

	}

}
```



##### Example with Group:

```php
use onservice\services\Router as Router;
use example_namespace\routes\ErrorRoute as ErrorRoute;
use example_namespace\routes\Users as Users;

$router = new Router();

$router->group('users')
		->resource('POST','/address',new Users,'setAddress')
		->resource('GET','/contact',new Users,'getContact')
		;
		
$router->runClass(new ErrorRoute);

```


### Middle:
classes included in the Middle will be executed before the classes responsible for the routes, the Middles are useful for creating a method for authentication, authorization, CORS configuration, database configuration and others.

##### Example with Middle Class:

```php
use onservice\services\Router as Router;
use example_namespace\routes\ErrorRoute as ErrorRoute;
use example_namespace\routes\Users as Users;

use example_namespace\middle\Setup as Setup;
use example_namespace\middle\Cors as Cors;
use example_namespace\middle\DatabaseConfig as DatabaseConfig;
use example_namespace\middle\Authorization as Authorization;
use example_namespace\middle\Authentication as Authentication;

$router = new Router();

$router->addMiddle(new Setup);
$router->addMiddle(new Cors);
$router->addMiddle(new DatabaseConfig);
$router->addMiddle(new Authorization);
$router->addMiddle(new Authentication);

$router->group('users')
		->resource('POST','/address',new Users,'setAddress')
		->resource('GET','/contact',new Users,'getContact')
		;
		
$router->runClass(new ErrorRoute);

```

##### class middle example

```php

namespace achoord\middles;

use onservice\services\router\Response as Response;

class Authentication {
	
	public function __construct(){
		
	}
	
	public function onRequest($uri,$http,&$container){

		if( $example_not_unauthorized === true)
			return (new Response)			
			->body(array('status'=>'unauthorized-authentication'))
			->code(401)
			->message('Unauthorized')
			->type('application/json')
			->format('json')
			;
		
	}
}
```

##### class middle CORS example

```php

namespace achoord\middles;

use onservice\services\router\Header as Header;

class Cors {
	
	public function __construct(){}
	
	public function onRequest($uri,$http,&$container){
		
        return (new Header)
			->add('Access-Control-Allow-Origin','*')
			->add('Access-Control-Allow-Methods','*')
			->add('Access-Control-Allow-Credentials','true')
			->add('Access-Control-Max-Age','86400')
			->add('Access-Control-Allow-Headers','*');

	}
	
}
```



### Classes úteis:

##### AllowRoutes
this class is useful to release some routes so as not to go through other Middles classes

```php
use onservice\services\router\AllowRoutes as AllowRoutes;

class {
	public function onRequest($uri,$http,&$container){

		$allowRoutes = [			
			// 'METHOD_HTTP: /ROUTE', // example
			'get:/users/token',
			'put:/manager/users',
		];

		$url = $http->get('url');
		

		if(AllowRoutes::check($url,$allowRoutes))
			return true;


	}
}
```


##### FieldsRequireds
This class is useful for making mandatory fields from the request.


```php

use onservice\services\router\FieldsRequireds as FieldsRequireds;

class CreateUser{

	public function index($uri,$http,&$container){

		if( $responseRequired = FieldsRequireds::check( $http->get('request') ,['title']) )
			return $responseRequired;

	}
}
```

##### PushRoute
This class is useful to create a Gateway API, it will forward the request to another Endpoint.

```php
class CreateUser{

	public function index($uri,$http,&$container){

		$sendRequest = [
			'method'=>'post',
			'data'=>$http->get('request'),
			'header'=>[
				'token'=>"tokenNumber"
			]
		];

		return new PushRoute('http://other-domain:8070/api/v1/create-user',$sendRequest);	
	}
}

```