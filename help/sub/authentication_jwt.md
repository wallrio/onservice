
# Authentication Using JWT Driver
Authentication of user using Json Web Token



## Instance 


```php

use onservice\CreateServer as CreateServer;
use onservice\services\Authentication as Authentication;
use onservice\services\authentication\JWT as JWT;


$authentication = new Authentication(new JWT(KEY_TOKEN));
```

## Create Token

```php
$token = $authentication->token->encode(ARRAY_PARAMETERS);

```

- Example:

```php
$token = $authentication->token->encode(array(
	'username'=>'Fulano',			// optional
	'name'=>'Fulano da Silva',		// optional
	'email'=>'fulano@email.com',	// optional
));

```

## Convert token
```php
$tokenParameters = $authentication->token->decode($token);		

```


## Example Authenticate

```php

use onservice\services\Authentication as Authentication;
use onservice\services\Authentication\JWT as JWT;


$token = $_REQUEST['token'];

// instance the service
$authentication = new Authentication(new JWT('abc123'));

// decode the token
$tokenParameters = $authentication->token->decode($token);

// condition of access
if($tokenParameters === false){
	echo 'error on token'
}else{
	echo 'success token'
}

```


## Other optional values in Token creation

```php
$token = $authentication->token->encode(array(
	"jti" => "", 								// optional - token id
	"sub" => "",								// optional - token subject or user ID
	"iss" => "http://site-owner.com", 				// optional - The token-generating application domain
    "aud" => "http://site-client.com",					// optional - Defines who can use the token
    "iat" => time(),							// optional - Timestamp from when the token was created
    "exp" => strtotime("+7 day", time()),		// optional - Timestamp of when the token will expire
    "nbf" => 1357000000							// optional - Sets a date for which the token can not be accepted before it
));

```

