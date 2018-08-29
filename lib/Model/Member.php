<?php

namespace rakesh\apartment;

class Model_Member extends \xepan\base\Model_Contact{

 	public $status = ['Active','InActive'];
	public $actions = [
					'Active'=>['view','edit','delete','deactivate','communication'],
					'InActive'=>['view','edit','delete','activate','communication']
				];

	public $contact_type = "Customer";

	function init(){
		parent::init();

		$this->getElement('created_by_id');//->defaultValue($this->app->employee->id);

		$model_j = $this->join('r_member.contact_id');

		$model_j->hasOne('rakesh\apartment\Apartment','apartment_id');

		$model_j->addField('relation_with_head')->enum(['Father','Mother']);
		$model_j->addField('dob')->type('date');
		$model_j->addField('marriage_date')->type('date');
		$model_j->addField('mobile_no');
		$model_j->addField('email_id');
		$model_j->addField('email_id');

	}
}