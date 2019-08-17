<?php

namespace rakesh\apartment;

class Model_Feedback extends \rakesh\apartment\Model_Communication{
	
	function init(){
		parent::init();

		$this->addCondition('communication_type','Feedback');
		
	}
}