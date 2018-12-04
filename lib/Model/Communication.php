<?php

namespace rakesh\apartment;

class Model_Communication extends \xepan\communication\Model_Communication{

	// public $table = 'r_communication';
	// public $status = ['Active','Inactive'];

	// public $actions = [
	// 		'Active'=>['View','deactive','edit','delete'],
	// 		'Inactive'=>['View','active','edit','delete']
	// 	];
	// used field 
		// from_id, to_id, related_contact_id, related_document_id, created_by_id, 
		// title, description, direction, communication_type, sent_on ,created_at, status

	public $acl_type = "ApartmentCommunication";

	function init(){
		parent::init();

	}

	function createNew($data_array=[]){

		if(!count($data_array)) throw new \Exception("data array is not defined, at communication");
		
		foreach ($data_array as $key => $value) {
			$this[$key] = $value;
		}
		$this->save();
		return $this;
	}

}