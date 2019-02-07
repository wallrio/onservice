# Stream Using HTTPServer Driver
To create a server HTTP (Web Server)

## Instance 

```php
use onservice\CreateServer as CreateServer;
use onservice\services\Stream as Stream;
use onservice\services\Stream\HTTPServer as HTTPServer;

$server = new CreateServer(	new Stream(IP,ADDRESS,new HTTPServer) );
```


### inicia o servidor
Insira em DIRECTORY_ROOT_PAGES, o caminho do diretório onde estão os arquivos a serem exibidos.

	$server->stream->start(DIRECTORY_ROOT_PAGES);

#### Example

	$server->stream->start("/home/user/www/");

### injeta código HTML junto com as páginas exibidas
$server->stream->includeHTML('');

### Envio personalizado
para alterar a exibição das páginas utilize o método onReceiver

```php
$server->stream->onReceiver(function($url,$stream){

	return array(
		'code' => 200,
		'message' => 'Ok',
		'body' => 'response...'
	);

});
```


## Example complete

```php
use onservice\CreateServer as CreateServer;
use onservice\services\Stream as Stream;
use onservice\services\Stream\HTTPServer as HTTPServer;

$server = new CreateServer(	new Stream('0.0.0.0','8080',new HTTPServer) );

$server->stream->start("/home/user/www/");

```
