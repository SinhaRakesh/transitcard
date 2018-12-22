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

		$crud->grid->addColumn('edit');
		$crud->grid->addColumn('delete');
		$crud->grid->addQuickSearch(['name','status'],['cancel_icon'=>'fa fa-remove']);
		$crud->grid->addPaginator(25);

		// $crud->grid->addHook('formatRow',function($g){
		// 	$g->current_row_html['edit'] = '<a class="table-link ap_edit" href="#" data-id="'.$g->model->id.'"><span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-pencil fa-stack-1x fa-inverse"></i></span></a>';
		// });

		// $crud->grid->on('click','.ap_edit')->univ()->location(
		// 		[
		// 			$this->api->url($this->edit_page?:$this->action_page),
		// 			[
		// 				'action'=>'edit',
		// 				$this->model->table.'_id'=>$this->js()->_selectorThis()->closest('[data-id]')->data('id')
		// 			]
		// 		]
		// 	);
		
	}
}