<?php

namespace rakesh\apartment;

class Model_GroupMemberAssociation extends \xepan\base\Model_Table{
	public $table = "r_group_member_association";

	function init(){
		parent::init();

		$this->hasOne('rakesh\apartment\Apartment','apartment_id')
			->defaultValue(@$this->app->apartment->id);
		$this->hasOne('rakesh\apartment\Group','group_id');
		$this->hasOne('rakesh\apartment\Member','member_id');

		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now);
		
		$this->add('dynamic_model\Controller_AutoCreator');
		
	}
}