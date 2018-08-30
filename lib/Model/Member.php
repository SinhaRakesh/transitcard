<?php

namespace rakesh\apartment;

class Model_Member extends \xepan\commerce\Model_Customer{

 	public $status = ['Active','InActive'];
	public $actions = [
					'Active'=>['view','edit','delete','deactivate','communication'],
					'InActive'=>['view','edit','delete','activate','communication']
				];

	public $contact_type = "Customer";

	function init(){
		parent::init();

		$this->getElement('created_by_id');//->defaultValue($this->app->employee->id);

		$model_j = $this->join('r_member.customer_id');

		$model_j->hasOne('rakesh\apartment\Apartment','apartment_id');

		$model_j->addField('customer_id');
		$model_j->addField('relation_with_head')->enum(['Father','Mother']);
		$model_j->addField('dob')->type('date');
		$model_j->addField('marriage_date')->type('date');
		// $model_j->addField('mobile_no');
		// $model_j->addField('email_id');
	}


	function createNewMember($app,$contact_detail=[],$user){

		$contact = $this->add('xepan\base\Model_Contact')->tryLoadBy('user_id',$user->id);
		if(!$contact->loaded()) throw new \Exception("contact not created, application bug");

		if(!$this->add('rakesh\apartment\Model_Member')->tryLoad($contact->id)->loaded()){
			$this->app->db->dsql()->table('r_member')
				->set('customer_id',$contact->id)
				->set('apartment_id',0)
				->insert();
		}
		
		$contact['first_name'] = $contact_detail['first_name'];
		$contact['last_name'] = $contact_detail['last_name'];
		$contact['type'] = 'Customer';
		$contact['user_id'] = $user->id;
		$contact->save();
	}
}