<?php

namespace rakesh\apartment;

class View_Expenses extends \View{

	public $options = [];

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}

		$model = $this->add('rakesh\apartment\Model_Expenses');
		$model->addCondition('apartment_id',@$this->app->apartment->id);

		$crud = $this->add('xepan\base\CRUD',
			['edit_page'=>$this->app->url('dashboard',['mode'=>'exedit']),'action_page'=>$this->app->url('dashboard',['mode'=>'exedit'])]);
		$crud->grid->js(true)->find('.main-box-body')->addClass('table-responsive');
		$crud->setModel($model);

		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(15);
		$crud->grid->addColumn('edit');
		$crud->grid->addColumn('delete');
		// $crud->add('rakesh\apartment\Controller_ACL');

	}
}