<?php

namespace rakesh\apartment;

class View_Flat extends \View{

	public $options = [];
	public $title = "Apartment Flats";
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
		$crud = $this->add('xepan\base\CRUD',['edit_page'=>$this->app->url('dashboard',['mode'=>'flatedit']),'action_page'=>$this->app->url('dashboard',['mode'=>'flatedit'])]);
		$crud->grid->js(true)->find('.main-box-body')->addClass('table-responsive');
		if($crud->form){
			$form = $crud->form;
			$form->add('xepan\base\Controller_FLC')
				->showLables(true)
				->addContentSpot()
				->makePanelsCoppalsible(true)
				->layout([
					'block_id~Block'=>'c1~4',
					'name~Flat Name'=>'c2~4',
					'size~Flat Size'=>'c3~4',
					'status~Flat Status'=>'c4~4',
					'member_id~Flat Member'=>'b2~4',
					'is_generate_bill~&nbsp;'=>'b3~4'
				]);
		}

		$crud->setModel($model,null,['name','size']);

		$crud->grid->addQuickSearch(['name','size']);
		$crud->grid->addPaginator(25);
		$crud->grid->addColumn('edit');
		$crud->grid->addColumn('delete');
	}
}