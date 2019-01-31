<?php

namespace rakesh\apartment;

class Model_PaymentTransaction extends \xepan\base\Model_Table{
	public $table = "r_payment_transaction";
 	public $status = ['Paid','Due'];
 	public $actions = [
 					'Due'=>['view','paid','edit','delete'],
 					'Paid'=>['view','edit','delete']
 				];

	function init(){
		parent::init();

		$this->hasOne('rakesh\apartment\Apartment','apartment_id')->defaultValue(@$this->app->apartment->id);
		$this->hasOne('rakesh\apartment\Model_Member','member_id');
		$this->hasOne('rakesh\apartment\Model_Flat','flat_id');
		$this->hasOne('rakesh\apartment\Model_Member','created_by_id')->defaultValue(@$this->app->apartmentmember->id);
		$this->hasOne('rakesh\apartment\Model_Member','paid_by_id');
		$this->hasOne('rakesh\apartment\Model_Affiliate','affiliate_id');
		
		$this->addField('name')->defaultValue($this->id);
		$this->addField('amount')->type('int')->defaultValue(0);
		$this->addField('panelty')->type('int')->defaultValue(0);

		$this->addField('created_at')->type('datetime');
		$this->addField('updated_at')->type('datetime');
		$this->addField('paid_at')->type('datetime');

		$this->addField('payment_type')->setValueList(['cash'=>'cash','cheque'=>'cheque','online'=>'online','other'=>'other']);
		$this->addField('payment_narration')->type('text');
		$this->addField('expenses_narration')->type('text');
		$this->addField('invoice_narration')->type('text');
		$this->addField('expenses_category');

		$this->addField('is_expences')->type('boolean')->defaultValue(false);
		$this->addField('is_invoice')->type('boolean')->defaultValue(false);

		$this->addExpression('net_amount')->set('amount + panelty');

		$this->addField('status')->setValueList(['Paid'=>'Paid','Due'=>'Due'])->defaultValue('Due');

		$this->addCondition('apartment_id',@$this->app->apartment->id);
		$this->addHook('beforeSave',$this);
		$this->add('dynamic_model\Controller_AutoCreator');
	}

	function beforeSave(){
		if(!$this['created_at']) $this['created_at'] = $this->app->now;
		$this['updated_at'] = $this->app->now;

		if($this['status'] == "Paid" AND !$this['paid_at']) $this['paid_at'] = $this->app->now;
	}

	function paid(){
		$this['status'] = 'Paid';
		$this['paid_by_id'] = $this->app->apartmentmember->id;
		$this['paid_at'] = $this->app->now;
		$this->save();
		
		// $this->sendNotification();
		// send push notification all apartment admin and to user;
	}

}