<?php

namespace rakesh\apartment;

class Model_Invoice extends \rakesh\apartment\Model_PaymentTransaction{
	function init(){
		parent::init();

		$this->addCondition('is_expences',false);
		$this->addCondition('is_invoice',true);
		$this->getElement('name')->caption('Bill No');
		$this->getElement('status')->defaultValue('Due');
		
	}
}