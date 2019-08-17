<?php

namespace rakesh\apartment;

class Model_Category extends \xepan\base\Model_Table{

	public $table = 'r_category';
	public $status = ['Active','Inactive'];

	public $actions = [
			'Active'=>['View','deactive','edit','delete'],
			'Inactive'=>['View','active','edit','delete']
		];
	public $acl_type = "Flat";

	function init(){
		parent::init();

		$this->hasOne('rakesh\apartment\Apartment','apartment_id');
		$this->addField('name')->sortable(true);
		$this->addField('status')->enum($this->status)->defaultValue('Active');

		$this->hasMany('rakesh\apartment\Model_Affiliate','category_id',null,'Affiliates');

		$this->is([
			'apartment_id|to_trim|required',
			'name|to_trim|required'
		]);
		
		$this->add('dynamic_model\Controller_AutoCreator');
	}

}