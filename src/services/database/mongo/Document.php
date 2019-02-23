<?php

namespace onservice\services\database\mongo;

class Document{
	private $db = null;
	private $name = null;
	private $collection = null;
	private $suffix = '_jdoc';

	function __construct($db,$name,$collection){
		$this->db = $db;
		$this->name = $name;
		$this->collection = $collection;
		$this->collectionName = explode(DIRECTORY_SEPARATOR, $collection);
		$this->collectionName = end($this->collectionName);
	}


	public function create($fields = null,$hash = null){
		return $this->collection->insert( $fields );
	}

	public function update($id,$fields = null){

		if(gettype($id) == 'string'){
			$filesArray = [$id];
		}else if(gettype($id) == 'array'){
			$filesArray = $id;
		}else if(gettype($id) == 'boolean'){
			return false;
		}

		if(count($filesArray)<1) return false;

		foreach ($filesArray as $key => $value) {
			if( gettype($value) == 'string' )
				$idItem = $value;
			else if( gettype($value) == 'object' )
				$idItem = $key;

			$filter = new \MongoId($idItem);

			$this->collection->update(
				['_id'=> $filter],
				['$set' =>$fields], 
				['multi' => false, 'upsert' => false]
			);


		}


		return true;

	}

	public function delete($id){
		
		if(gettype($id) == 'string'){
			$filesArray = [$id];
		}else if(gettype($id) == 'array'){
			$filesArray = $id;
		}else if(gettype($id) == 'boolean'){
			return false;
		}

		if(count($filesArray)<1) return false;

		foreach ($filesArray as $key => $value) {
			if( gettype($value) == 'string' )
				$idItem = $value;
			else if( gettype($value) == 'object' )
				$idItem = $key;

			$this->collection->remove(array('_id' => new \MongoId($idItem)));
		}

		return true;
	}

	public function select(array $where = array()){

		$foundOrOperator = false;		
		$whereNew = [];
		foreach ($where as $key => $value) {
			
			if( substr($key, 0,3) == '||.'){
				$key = str_replace('||.', '', $key) ;
				$foundOrOperator = true;
			}
			$whereNew[] = array($key=>$value);
		}

		if($foundOrOperator === false){
			$whereNew = [];

			foreach ($where as $key => $value) {
				
				if( substr($key, 0,3) == '&&.'){
					$key = str_replace('&&.', '', $key) ;
					$foundOrOperator = false;
				}
				$whereNew[$key] = $value;
			}
		}else{
			$whereNew = array('$or'=>$whereNew);	
		}
		
		$cursor = $this->collection->find($whereNew);

		$array = array();
		foreach ( $cursor as $id => $value ){		    
		   	$array[$id] = (object) $value;
		   	unset($array[$id]->_id);
		}

		if(count($array)>0)
			return $array;
		else
			return false;
		
	}

}