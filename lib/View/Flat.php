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
		$this->app->template->trySet('page_title','Apartment Flats');

		$model = $this->add('rakesh\apartment\Model_Flat');
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
					'name~Flat Name'=>'c1~4',
					'size~Flat Size'=>'c2~4',
					'status'=>'c3~4',
					'member_id~Member'=>'b2~12',
					'is_generate_bill~&nbsp;'=>'b1~12'
				]);
		}

		$crud->setModel($model,null,['name','size','member','status','is_generate_bill']);

		$crud->grid->addQuickSearch(['name','size']);
		$crud->grid->addPaginator(25);
		
	}
}