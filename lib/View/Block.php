<?php

namespace rakesh\apartment;

class View_Block extends \View{

	public $options = [];

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update partment data');
			return;
		}
		$this->app->template->trySet('page_title','Apartment Blocks');

		$model = $this->add('rakesh\apartment\Model_Block');
		$model->addCondition('apartment_id',@$this->app->apartment->id);
		$model->setOrder('name','asc');
		$crud = $this->add('xepan\base\CRUD',['edit_page'=>$this->app->url('dashboard',['mode'=>'blockedit']),'action_page'=>$this->app->url('dashboard',['mode'=>'blockedit'])]);

		$crud->setModel($model,['name','status']);

		$crud->grid->addColumn('edit');
		$crud->grid->addColumn('delete');
		$crud->grid->addQuickSearch(['name','status'],['cancel_icon'=>'fa fa-remove']);
		$crud->grid->addPaginator(25);
		$acl = $crud->add('rakesh\apartment\Controller_ACL');
		
	}
}