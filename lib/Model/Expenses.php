<?php

namespace rakesh\apartment;

class Model_Expenses extends \rakesh\apartment\Model_PaymentTransaction{

	function init(){
		parent::init();

		$this->addCondition('is_expences',true);
		$this->addCondition('is_invoice',false);

		$this->getElement('name')->caption('Expenses Title');
		$this->getElement('status')->defaultValue('Paid');

		// flat master data
		$config_model = $this->add('rakesh\apartment\Model_Config_Master')->tryLoadAny();
		if($config_model['expenses_category']){
			$temp = [];
			foreach (explode(",", $config_model['expenses_category']) as $key => $value) {
				$temp[trim($value)] = trim($value);
			}
			$this->getElement('expenses_category')->setValueList($temp);
		}
		$this->is([
			'name|required',
			'payment_type|required',
			'amount|required',
		]);
	}
}