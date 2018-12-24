<?php

namespace rakesh\apartment;

class Model_Member extends \rakesh\apartment\Model_MemberAbstract{

	function init(){
		parent::init();

		$this->addCondition([['is_group',false],['is_group',null]]);
	}
}