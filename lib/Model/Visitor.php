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

		$this->hasOne('rakesh\apartment\Model_Apartment','apartment_id');
		$this->hasOne('xepan\base\Model_Contact','created_by_id')->system(true)->caption('Visitor Added By'); // must be employee or gatekipper
		$this->hasOne('rakesh\apartment\Model_Flat','flat_id');
		$this->hasOne('rakesh\apartment\Model_Member','member_id');

		$this->addField('name');
		$this->addField('mobile_no');
		$this->addField('email_id');
		$this->addField('address')->type('text');
		$this->addField('visitor_narration')->type('text');

		$this->addField('vehical_type')->hint('bike,car etc.');
		$this->addField('vehical_no');
		$this->addField('vehical_model');
		$this->addField('vehical_color');
		$this->addField('vehical_detail')->type('text');
		$this->addField('person_count');
		
		$this->addField('title');
		$this->addField('message')->type('text');
		
		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now)->system(true);
		$this->addField('permitted_at')->type('datetime');
		$this->addField('denied_at')->type('datetime');
		$this->hasOne('xepan\base\Model_Contact','permitted_by_id');
		$this->hasOne('xepan\base\Model_Contact','denied_by_id');

		$this->addField('status')->enum($this->status)->defaultValue('Requested');

		$this->add('dynamic_model/Controller_AutoCreator');
		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){
		if(!$this['created_by_id']){
			$contact = $this->add('xepan\base\Model_Contact');
			if($contact->loadLoggedIn()){
				$this['created_by_id']	= $contact->id;
			}
		}

	}
}