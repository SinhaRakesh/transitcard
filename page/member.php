<?php

namespace rakesh\apartment;

class page_member extends \xepan\base\Page{

	public $title = "Member";

	function init(){
		parent::init();

		$model = $this->add('rakesh\apartment\Model_Member');
		
		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($model);
	}
}