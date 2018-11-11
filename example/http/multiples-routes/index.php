<?php

error_reporting(E_ALL);
ini_set("display_errors", true);
ini_set("display_startup_erros",true);



require "../../../../../../vendor/autoload.php";


use onservice\CreateServer as CreateServer;
use onservice\services\Http as Http;

$server = new CreateServer(	new Http() );

$server->http->routesDir(__DIR__.DIRECTORY_SEPARATOR.'routes');

$server->http->routes(array('Index','Users','Companies'));
