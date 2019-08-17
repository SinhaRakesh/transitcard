<?php

namespace rakesh\apartment;

class page_flat extends \xepan\base\Page{

	public $title = "Flat";
	function init(){
		parent::init();

		$model = $this->add('rakesh\apartment\Model_Flat');
		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($model);
	}
}