<?php

namespace onservice\services\longpolling;

interface InterfaceLongPollingPersistence{

	public function updateStatus($id);
	public function cleanMessage($id,$list);
	public function checkMessage($id,&$notify);
	public function recordMessage($from,$to,$message);
	public function createUser($id,$options);
	
}