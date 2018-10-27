<?php

require "../../../../../../vendor/autoload.php";

use onservice\CreateServer as CreateServer;
use onservice\services\Stream as Stream;

$server = new CreateServer(	new Stream('255.255.255.255',3333,'udp') );

$server->stream->listen(function($data){
	
	echo $data;

});
