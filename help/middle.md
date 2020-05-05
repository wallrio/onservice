
# Middle
Creates an earlier layer in the application to separate global implementations

## Instance

	new Middle(RequestPath, RequestParameters)


##### Example:
```php

use onservice\services\Middle as Middle;

Middle::single(function($urlPar,$requestPar){
	
	return array(
		'body'		=> json_encode(array('status'=>'forbidden')),
		'code'		=> 403,
		'type'		=> 'application/json'
	);

});

```


### JWT authentication - example

```php
use onservice\services\Middle as Middle;
use onservice\services\Authentication as Authentication;
use onservice\services\authentication\JWT as JWT;

Middle::single(function($urlPar,$requestPar){

	$token = isset($requestPar['data']['get']['token'])?$requestPar['data']['get']['token']:null;

	$authentication = new Authentication(new JWT('Key_token')) ;
	$tokenParameters = $authentication->token->decode($token);

	if($tokenParameters === false)
		return array(
			'body'		=> json_encode(array('status'=>'forbidden')),
			'code'		=> 403,
			'type'		=> 'application/json'
		);
	else{		
		return $tokenParameters;
	}
});

```

### run without interruption - example
```php

use onservice\services\Middle as Middle;

Middle::single(function($urlPar,$requestPar){
	
	// your code

});

```
