<?php

require "../../../../../../vendor/autoload.php";

use onservice\CreateServer as CreateServer;
use onservice\services\Stream as Stream;

$server = new CreateServer(	new Stream('127.0.0.1',3332,'tcp') );

$server->stream->listen(function($data){
	
	echo $data;

});
