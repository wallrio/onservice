<?php

error_reporting(E_ALL);
ini_set("display_errors", true);
ini_set("display_startup_erros",true);



require "../../../../../../vendor/autoload.php";


use onservice\CreateServer as CreateServer;
use onservice\services\Http as Http;

$server = new CreateServer(	new Http() );

<<<<<<< HEAD
$server->http->routes(getcwd().DIRECTORY_SEPARATOR.'http'.DIRECTORY_SEPARATOR.'routes');
=======
$server->http->routes(__DIR__.DIRECTORY_SEPARATOR.'http'.DIRECTORY_SEPARATOR.'routes');
>>>>>>> 934e985ad4c70087d566eb8ad8c6ff64df99aa83
