# Authentication
Interface to authenticate user

## Instance 

```php

use onservice\CreateServer as CreateServer;
use onservice\services\Authentication as Authentication;

$this->authentication = new CreateServer( new Authentication(DRIVER,DRIVER...) );

```

> DRIVER is the method of authenticate

# Drivers available

- [Token Generator - JWT](help/authentication_jwt.md)
- [HTTP Basic logon](help/authentication_httpbasic.md)
- [Authorization](help/authentication_authorization.md)

