<?php

namespace rakesh\apartment;

class Model_ComplainDepartment extends \xepan\base\Model_Table{
	public $table = "r_complain_department";
 	public $status = ['Active','InActive'];

	function init(){
		parent::init();

		$this->hasOne('rakesh\apartment\Apartment','apartment_id')->defaultValue($this->app->apartment->id);
		$this->hasOne('rakesh\apartment\Model_Member','created_by_id')->defaultValue($this->app->apartmentmember->id)->system(true);
				
		$this->addField('name')->defaultValue($this->id);
		$this->add('dynamic_model\Controller_AutoCreator');

		$this->addCondition('apartment_id',@$this->app->apartment->id);
	}
}