
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
use onservice\services\Http as Http;

$server = new CreateServer( new Http() );

```



After providing the service to the server, you can use all the features that the service provides.

##### Example

```ph
$server->http->resource('/',function($urlPar,$requestPar){

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
use onservice\services\Http as Http;
use onservice\services\Process as Process;

$server = new CreateServer( new Process(), new Http(),... );

```



# Available services

- [Http](http.md)
- [LongPolling](longpolling.md)
- [Process](process.md)
- [Database](database.md)
- [Stream](stream.md)
- [Authentication](authentication.md)
- [Settings](settings.md)


## License

The OnService is licensed under the MIT license. See [License File](LICENSE) for more information.