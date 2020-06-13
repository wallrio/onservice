
# Authentication Using HTTPBasic
Authentication of user using HTTPBasic



## Instance 


```php

use onservice\services\Authentication as Authentication;
use onservice\services\authentication\HTTPBasic as HTTPBasic;


$authentication = new Authentication(new HTTPBasic()) ;
```


## Function to run when user cancel logon

```php

$authentication->httpbasic->cancel(function(){
	// user canceled 
});

```



## Function that open the logon box (when user not is logged)

		
```php

$authentication->httpbasic->check(function($data){
	if($data->username === 'USERNAME_OF_BASE' && $data->password === 'PASSWORD_OF_BASE')
	return true;
});

```

- RETURN:
	- true: When the access is valid
	- false: When the access is not valid
	- empty: When the access is not valid



## Function to run when the user is logged

```php

$authentication->httpbasic->success(function(){
	// user logged 
});

```

## Condition when the user is not logged

```php

if( $authentication->httpbasic->status === false  ) exit;

```

- status = true: when user is logged


## Function to clean the logon access

```php

$authentication->httpbasic->clean();

```
