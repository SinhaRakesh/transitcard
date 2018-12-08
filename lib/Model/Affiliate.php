<?php

namespace rakesh\apartment;

class Model_Affiliate extends \xepan\base\Model_Table{
	public $table = "r_affiliate";
 	public $status = ['Active','InActive'];
	public $actions = [
					'Active'=>['view','edit','delete','deactivate','communication'],
					'InActive'=>['view','edit','delete','activate','communication']
				];

	function init(){
		parent::init();

		$this->hasOne('rakesh\apartment\Apartment','apartment_id');
		$this->hasOne('rakesh\apartment\Category','category_id');

		$this->addField('name')->caption('Full Name');
		$this->addField('contact_no');
		$this->addField('email_id');
		$this->addField('address')->type('text');
		$this->addField('narration')->type('text');
		$this->addField('status')->enum($this->status)->defaultValue('Active');
		$this->add('dynamic_model\Controller_AutoCreator');
		
	}
}