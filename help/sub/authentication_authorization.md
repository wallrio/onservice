
# Authentication with Authorization
Implements method of authorization of user



## Instance 


```php
use onservice\services\Authentication as Authentication;
use onservice\services\authentication\Authorization as Authorization;


$authentication = new Authentication(new Authorization());
```

## Set the authorization of user current

```php

$authentication->authorization->current('user');

```

## Check the permissions of user current

```php

$authentication->authorization->allowed(array("admin","editor"));

```

- The example above finish the application if user not is allowed

- Run code before finish

```php

$authentication->authorization->allowed(array("admin","editor"),function(){
	// code
	exit;
});

```

## Check the permissions of user current only with operator if (not finish application)

```php

if( !$authentication->authorization->check("admin","editor") ){
	// code
}

```



## Example with JWT Driver 


```php

use onservice\services\Authentication as Authentication;
use onservice\services\authentication\Authorization as Authorization;
use onservice\services\authentication\JWT as JWT;


$authentication = new Authentication(new JWT('key123'),new Authorization());

$tokenParameters = $authentication->token->decode('token received by any parameter');

if($tokenParameters === false){
	echo 'access denied';
	exit;
}else{
	$username = $tokenParameters->username;
	$role = $tokenParameters->role;
	$authentication->authorization->current($role);
	$authentication->authorization->allowed(array("admin","editor"));
	// the process only continue if role of user is admin or editor
	
	echo 'access allowed';
}

```
