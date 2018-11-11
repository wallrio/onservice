<?php


require "../../../../../../vendor/autoload.php";

use onservice\Debug as Debug;  (new Debug());
use onservice\CreateServer as CreateServer;
use onservice\services\Database as Database;
use onservice\services\Database\Mysql as Mysql;

$server = new CreateServer( new Database( new Mysql() ) );

$server->database->config(array(
	'host'=>'localhost',
	'username'=>'username',
	'password'=>'password',
));

$server->database->createBase('onservicedbmysql');

$resultCreateBase = $server->database->base('onservicedbmysql');

$server->database->createTable('users',array(
'id'=>'int NOT NULL AUTO_INCREMENT PRIMARY KEY',
'name'=> 'VARCHAR(30) NOT NULL',
'email'=> 'VARCHAR(150)',
'created'=> 'VARCHAR(20)'
));

$server->database->insert('users',array(
	'name'		=>	'Wallace Rio',
	'email'		=>	'wallrio@gmail.com',
	'created'	=>	time(),
));

$result = $server->database->select('users');

print_r($result);