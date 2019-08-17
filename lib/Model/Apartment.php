<?php

namespace rakesh\apartment;

class Model_Apartment extends \xepan\base\Model_Table{
	public $table = 'r_apartment';

	public $status = ['Trial','Paid','Grace','Unpaid'];
	public $actions = [
			'Trial'=>['All'],
			'Paid'=>['All'],
			'Unpaid'=>['All'],
			'Grace'=>['All']
		];
	public $acl_type = "Apartment";

	function init(){
		parent::init();

		$this->hasOne('xepan\base\Country','country_id')->display(array('form' => 'xepan\base\Country'));
		$this->hasOne('xepan\base\State','state_id')->display(array('form' => 'xepan\base\State'));
		$this->hasOne('xepan\base\Model_Contact','created_by_id')->system(true);

		$this->addField('name');
		$this->addField('city');
		$this->addField('address')->type('text');

		// $this->addField('admin_name');
		// $this->addField('admin_mobile_no');
		// $this->addField('admin_email_id');
		
		$this->addField('builder_name');
		$this->addField('builder_mobile_no');
		$this->addField('builder_email_id');
		$this->addField('status')->enum($this->status)->defaultValue('Trial');

		$day_array = [
						1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,
						11=>11,12=>12,13=>13,14=>14,15=>15,16=>16,17=>17,18=>18,19=>19,20=>20,
						21=>21,22=>22,23=>23,24=>24,25=>25,26=>26,27=>27,28=>28
				];
		$this->addField('bill_generation_date')->setValueList($day_array);
		$this->addField('last_submission_date')->setValueList($day_array);

		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now)->system(true);

		$this->addField('maintenance_amount')->hint('maintenance amount per month')->defaultValue(0);
		$this->addField('penelty_amount')->hint('panelty amount applicable after last submission date')->defaultValue(0);

		// used when invoice is generated on
		$this->addField('bill_generated_on_date')->type('date')->system(true);
		$this->addField('bill_generated_on_time')->type('time')->system(true);
		// $this->addField('penelty_based_on')->setValueList(['']);

	}
}