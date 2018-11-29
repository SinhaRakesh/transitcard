<?php

namespace rakesh\apartment;

class View_Visitor extends \View{

	public $options = [];

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}

		$crud = $this->add('CRUD');
		$model = $this->add('rakesh\apartment\Model_Visitor');
		$crud->setModel($model);
		$crud->grid->addQuickSearch(['name']);

	}
}