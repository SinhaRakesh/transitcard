<?php

namespace rakesh\apartment;

class Model_Complain extends \xepan\base\Model_Table{
	public $table = "r_complain";
 	public $status = ['Draft','Pending','Closed','Rejected'];
	public $actions = [
					'Draft'=>['view','receive','reject','edit','delete'],
					'Pending'=>['view','close','reject','edit','delete'],
					'Closed'=>['view','edit','delete'],
					'Rejected'=>['view','edit','delete']
				];

	function init(){
		parent::init();

		$this->hasOne('rakesh\apartment\Apartment','apartment_id')->defaultValue($this->app->apartment->id);
		$this->hasOne('rakesh\apartment\Model_Member','created_by_id')->defaultValue($this->app->apartmentmember->id);
		$this->hasOne('rakesh\apartment\Model_Member','pending_by_id');
		$this->hasOne('rakesh\apartment\Model_Member','closed_by_id');
		$this->hasOne('rakesh\apartment\Model_Member','rejected_by_id');
		$this->hasOne('rakesh\apartment\Model_ComplainDepartment','complain_to_department_id');

		$this->addField('name')->defaultValue($this->id);
		$cat_field = $this->addField('category');
		$this->addField('description')->type('text');
		$this->addField('is_urgent')->type('boolean');
		$this->addField('status')->enum($this->status)->defaultValue('Draft');

		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now);
		$this->addField('pending_at')->type('datetime');
		$this->addField('closed_at')->type('datetime');
		$this->addField('rejected_at')->type('datetime');

		$this->addField('close_narration')->type('text');
		$this->addField('reject_narration')->type('text');
		
		$config_model = $this->add('rakesh\apartment\Model_Config_Master')
					->tryLoadAny();
		$this->cat_value = [];
		if($config_model['complain_category']){
			foreach (explode(",", $config_model['complain_category']) as $key => $value) {
				$this->cat_value[trim($value)] = trim($value);
			}
		}
		$cat_field->setValueList($this->cat_value);

		$this->add('dynamic_model\Controller_AutoCreator');

	}

	function receive(){
		$this['status'] = 'Pending';
		$this['name'] = $this->getNextID() + 1;
		$this['pending_at'] = $this->app->now;
		$this['pending_by_id'] = $this->app->apartmentmember->id;
		$this->save();
	}

	function getNextID(){
		return $this->add('rakesh\apartment\Model_Complain')
				->addCondition('status','<>','Draft')
				->count()->getOne();
	}
	
	function addDefault(){
		$m = $this->add('rakesh\apartment\Model_Complain');
		$m->addCondition('apartment_id',$this->app->apartment->id);
		$m->addCondition('name','Complain Department');
		$m->tryLoadAny();
		$m->save();
	}

}