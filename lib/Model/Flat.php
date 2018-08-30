<?php

namespace rakesh\apartment;

class Model_Flat extends \xepan\base\Model_Table{

	public $table = 'r_flat';
	public $status = ['Sold','OnRent','Vacant','Deactive'];

	public $actions = [
			'Sold'=>['View','edit','delete'],
			'OnRent'=>['View','edit','delete'],
			'Vacant'=>['View','edit','delete'],
			'Deactive'=>['View','edit','delete']
		];
	public $acl_type = "Flat";

	function init(){
		parent::init();

		$this->hasOne('rakesh\apartment\Apartment','apartment_id');
		$this->hasOne('rakesh\apartment\Member','member_id');

		$this->addField('name')->sortable(true);
		$this->addField('size')->setValueList(['1BHK'=>'1 BHK','2BHK'=>'2 BHK','3BHK'=>'3 BHK','4BHK'=>'4 BHK','Other'=>'Other']);
		$this->addField('is_generate_bill')->type('boolean')->defaultValue(true);
		$this->addField('status')->enum($this->status);
	}
}