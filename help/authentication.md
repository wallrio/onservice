# Authentication
Interface to authenticate user

## Instance 

```php


use onservice\services\Authentication as Authentication;

$authentication = new Authentication(DRIVER,DRIVER...) ;

```

> DRIVER is the method of authenticate

# Drivers available

- [Token Generator - JWT](sub/authentication_jwt.md)
- [HTTP Basic logon](sub/authentication_httpbasic.md)
- [Authorization](sub/authentication_authorization.md)

