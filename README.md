
# OnService

Interface for creating servers and services.

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install Directly.

```bash
$ composer require wallrio/onservice "*"
```


## Usage

Creating the server.

```php
<?php

require 'vendor/autoload.php';

use onservice\CreateServer as CreateServer;

$server = new CreateServer();

```

## Usage the service

When creating the server attach the service of interest

```php

use onservice\CreateServer as CreateServer;
use onservice\services\Router as Router;

$server = new CreateServer( new Router() );

```



After providing the service to the server, you can use all the features that the service provides.

##### Example

```ph
$server->router->resource('/',function($urlPar,$requestPar){

        $html = 'First Page';
        $html .= '<hr>';

        return array(
            'body'=>$html,
            'code'=>200
        );
});

```


### Multiple services


```php

use onservice\CreateServer as CreateServer;
use onservice\services\Router as Router;
use onservice\services\Process as Process;

$server = new CreateServer( new Process(), new Router(),... );

```



# Available services

- [Router](help/router.md)
- [LongPolling](help/longpolling.md)
- [Process](help/process.md)
- [Database](help/database.md)
- [Stream](help/stream.md)
- [Authentication](help/authentication.md)
- [Settings](help/settings.md)
- [Git](help/git.md)
- [Console](help/console.md)
- [Pipe](help/pipe.md)
- [Middle](help/middle.md)


## License

The OnService is licensed under the MIT license. See [License File](LICENSE) for more information.