<?php

namespace rakesh\apartment;

class View_Flat extends \View{

	public $options = [];

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update partment data');
			return;
		}

		$model = $this->add('rakesh\apartment\Model_Flat');
		$model->addCondition('apartment_id',@$this->app->apartment->id);
		$model->setOrder('name','asc');
		$crud = $this->add('xepan\base\CRUD');
		$crud->setModel($model);

		$crud->grid->addQuickSearch(['name','size']);
		$crud->grid->addPaginator(10);

	}
}