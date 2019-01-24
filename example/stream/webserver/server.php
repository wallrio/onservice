<?php


require "../../../../../../vendor/autoload.php";

use onservice\CreateServer as CreateServer;
use onservice\services\Stream as Stream;

// create the server
$server = new CreateServer(	new Stream('127.0.0.1',8081,'tcp') );


$server->stream->listen(function($data){
	
	$in = 'HTTP/1.1 200 OK '."\r\n";
	$in .= 'Server: OnService'."\r\n\r\n ";
	$in .= 'Response from server...';

	return $in;
});

// Access on your browser the address localhost:8081