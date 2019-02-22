<?php

header('Access-Control-Allow-Origin: *');  


require "../../../../../vendor/autoload.php";

use onservice\CreateServer as CreateServer;
use onservice\services\LongPolling as LongPolling;
use onservice\services\LongPolling\FilePersistence as FilePersistence;
use onservice\services\LongPolling\MysqlPersistence as MysqlPersistence;

$FilePersistence = new FilePersistence(__DIR__.'/work');
$MysqlPersistence = new MysqlPersistence(array(
	'host'=>'host',
	'basename' => 'basename',
	'username' => 'username',
	'password' => 'password'
));

// create the server
// $server = new CreateServer(	new LongPolling($MysqlPersistence) );
$server = new CreateServer(	new LongPolling($FilePersistence) );

$server->longpolling->config( array(	
	'startinfo' => 'Seja Bem-vindo(a)...',
	'updatetime'=>5
));




$server->longpolling->received(function($from,$data,$service){


	$data = json_decode($data);
	if($data->signal == 'users'){
		$users = $service->users();
		$service->recordMessage('server',$from,json_encode(array('command'=>'listusers','data'=>$users)));
	}
});


// run the server
$server->longpolling->start();