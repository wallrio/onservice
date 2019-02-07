# Stream Using HTTPClient Driver
To create a client HTTP (Request site content)

## Instance 

```php
use onservice\CreateServer as CreateServer;
use onservice\services\Stream as Stream;
use onservice\services\Stream\HTTPClient as HTTPClient;

$server = new CreateServer(	new Stream(IP,ADDRESS,new HTTPClient) );
```

## onReceiver
receives the content of the site

```php
	$server->stream->onReceiver(function($message,$scopeStream){
		print_r($message);
	});
```

## connect
connects and performs the requisition of the content

	$server->stream->connect(MODE_REQUEST,ARRAY_PARAMETER);

- MODE_REQUEST (string): 
mode of showing.

	- 'header':	only displays the header.
	- 'body':	only displays the body of the page.
	- 'parse':	it exempts all content from the response as an array.
	- 'decompress':	if gzip is enabled, unzip the document body.
	- null:	displays the content of the response purely.

- ARRAY_PARAMETER (array):
parameters to send on requesition.

	- User-Agent
	- Cache-Control
	- Connection
	- Accept
	- Accept-Encoding
	- Accept-Language


## Example complete 

```php
use onservice\CreateServer as CreateServer;
use onservice\services\Stream as Stream;
use onservice\services\Stream\HTTPClient as HTTPClient;

$server = new CreateServer(	new Stream('wallrio.com',80,new HTTPClient) );

$server->stream->onReceiver(function($message,$scopeStream){
	print_r($message);
});

$server->stream->connect('decompress',array(
	'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
	'Accept-Encoding' => 'gzip, deflate',
	'Accept-Language' => 'en-US,en;q=0.9'
));
```