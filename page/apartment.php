<?php

namespace rakesh\apartment;

class page_apartment extends \xepan\base\Page{

	public $title = "Apartment";

	function init(){
		parent::init();

		$apartment = $this->add('rakesh\apartment\Model_Apartment');

		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($apartment);
	}
}