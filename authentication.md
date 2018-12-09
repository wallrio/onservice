# Authentication
Interface to authenticate user

## Instance 

```php

use onservice\CreateServer as CreateServer;
use onservice\services\Authentication as Authentication;

$this->authentication = new CreateServer( new Authentication(DRIVER,DRIVER...) );

```


# Drivers available

- [Token - JWT](help/authentication_jwt.md)
- [HTTP Basic logon](help/authentication_httpbasic.md)
- [Authorization](help/authentication_authorization.md)

