<?php

namespace rakesh\apartment;

class Model_Block extends \xepan\base\Model_Table{

	public $table = 'r_block';
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
		
		$this->is([
			'apartment_id|to_trim|required',
			'name|to_trim|required'
		]);

		// $this->add('dynamic_model\Controller_AutoCreator');

		$this->addHook('beforeSave',[$this,'checkDuplicate']);
	}

	function checkDuplicate(){
		$block = $this->add('rakesh\apartment\Model_Block');
		$block->addCondition('apartment_id',$this->app->apartment->id);
		$block->addCondition('name',$this['name']);
		$block->addCondition('id','<>',$this->id);
		$block->tryLoadAny();

		if($block->loaded()) throw $this->exception('name is already taken','ValidityCheck')->setField('name');			

	}
}