<?php

namespace rakesh\apartment;
class Model_Config_Master extends \xepan\base\Model_ConfigJsonModel{
	public $fields =[
						'flat_size'=>'Text',
						'flat_status'=>"Text",
						'complain_category'=>"Text",
					];
	public $config_key = 'Apartment_Config';
	public $application='rakesh\apartment';

	function init(){
		parent::init();

		$this->getElement('flat_size')->hint("Comma(,) seperated values i.e. 1BHK,2BHK");
		$this->getElement('flat_status')->hint("Comma(,) seperated values i.e. Sold,Rent");
		$this->getElement('complain_category')->hint("Comma(,) seperated values i.e. Accounts,Lifts");
		
	}
}