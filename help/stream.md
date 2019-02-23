# Stream
service for make stream applications 

## Instance 

```php

use onservice\CreateServer as CreateServer;
use onservice\services\Stream as Stream;
use onservice\services\stream\TCP as TCP;
use onservice\services\stream\HTTPServer as HTTPServer;
use onservice\services\stream\HTTPClient as HTTPClient;

$server = new CreateServer(	new Stream(IP,PORT,DRIVER) );
```

> DRIVER is the method of communication

# Drivers available

- [TCP](help/stream_tcp.md)
- [HTTPServer](help/stream_httpserver.md)
- [HTTPClient](help/stream_httpclient.md)

