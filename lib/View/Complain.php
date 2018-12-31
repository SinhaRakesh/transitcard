<?php

namespace rakesh\apartment;

class View_Complain extends \View{

	public $options = [];
	public $title = "Complain";

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}
		
		$model = $this->add('rakesh\apartment\Model_Complain');
		$model->addCondition('apartment_id',@$this->app->apartment->id);
		$model->setOrder('created_at','desc');
		$crud = $this->add('xepan\hr\CRUD',['actionsWithoutACL'=>true,'edit_page'=>$this->app->url('dashboard',['mode'=>'complainedit']),'action_page'=>$this->app->url('dashboard',['mode'=>'complainedit'])]);
		// $crud->grid->js(true)->find('.main-box-body')->addClass('table-responsive');

		$crud->setModel($model,null,['complain_to_department','created_by','category','description','is_urgent','status']);

		$crud->grid->addQuickSearch(['category','description']);
		$crud->grid->addPaginator(25);
		$crud->grid->removeColumn('edit');
		$crud->grid->removeColumn('delete');
		$crud->grid->removeColumn('status');
		$crud->grid->removeAttachment();
		$crud->add('rakesh\apartment\Controller_ACL');
	}
}