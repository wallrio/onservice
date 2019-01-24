<?php


require "../../../../../../vendor/autoload.php";

use onservice\Debug as Debug;  (new Debug());
use onservice\CreateServer as CreateServer;
use onservice\services\Database as Database;
use onservice\services\Database\JSON as JSON;

$server = new CreateServer( new Database( new JSON() ) );


$server->database->config(array(
	'dir' => __DIR__.DIRECTORY_SEPARATOR.'bases'
));

$base = $server->database->createBase('example1');
$collection = $base->createCollection('users');

$collection->document->create(array(
	'username' => 'wallrio',
	'name' => 'Wallace Rio'
));

$result = $collection->document->select();

print_r($result);