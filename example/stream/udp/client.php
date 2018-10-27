<?php

require "../../../../../../vendor/autoload.php";

use onservice\CreateServer as CreateServer;
use onservice\services\Stream as Stream;

$client = new CreateServer(	new Stream('255.255.255.255',3333,'udp') );

$client->stream->send('test from client to broadcast address');

