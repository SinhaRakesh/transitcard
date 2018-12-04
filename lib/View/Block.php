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
		$crud = $this->add('xepan\base\CRUD');

		if($crud->form){
			$form = $crud->form;
			$form->add('xepan\base\Controller_FLC')
				->showLables(true)
				->addContentSpot()
				->makePanelsCoppalsible(true)
				->layout([
					'name~Block Name'=>'c1~6',
					'status'=>'c3~3',
					'FormButtons~&nbsp;'=>'c4~3'
				]);
		}

		$crud->setModel($model,null,['name','status']);

		$crud->grid->addQuickSearch(['name','status']);
		$crud->grid->addPaginator(25);
		
	}
}