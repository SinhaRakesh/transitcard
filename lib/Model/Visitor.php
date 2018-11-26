<?php

namespace rakesh\apartment;

class Model_Visitor extends \xepan\base\Model_Table{
	public $table = 'r_visitor';
	public $title_field = 'name';
	public $status = ['Requested','Permitted','Denied'];
	public $actions = [
			'Requested'=>['View','Permitted','Denied'],
			'Permitted'=>['View'],
			'Denied'=>['View']
		];
	public $acl_type = "Apartment";

	function init(){
		parent::init();

		$this->hasOne('xepan\base\Model_Contact','created_by_id')->system(true); // must be employee or gatekipper
		$this->hasOne('rakesh\apartment\Model_Flat','flat_id');

		$this->addField('name');
		$this->addField('mobile_no');
		$this->addField('email_id');
		$this->addField('address')->type('text');
		
		$this->addField('title');
		$this->addField('message')->type('text');
		
		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now)->system(true);
		$this->addField('permitted_at')->type('datetime');
		$this->addField('denied_at')->type('datetime');

		$this->addField('status')->enum($this->status)->defaultValue('Requested');

	}
}