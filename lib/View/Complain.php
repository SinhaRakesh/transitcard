<?php

namespace rakesh\apartment;

class View_Complain extends \View{

	public $options = [];
	public $title = "Complaint";

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}
		
		$model = $this->add('rakesh\apartment\Model_Complain');
		$model->addCondition('apartment_id',@$this->app->apartment->id);
		$model->setOrder('created_at','desc');
		$crud = $this->add('xepan\hr\CRUD',
				[
					'entity_name'=>'',
					'actionsWithoutACL'=>true,
					'edit_page'=>$this->app->url('dashboard',['mode'=>'complainedit']),'action_page'=>$this->app->url('dashboard',['mode'=>'complainedit'])
				],
				null,
				['view/complaingrid']
			);
		// $crud->grid->js(true)->find('.main-box-body')->addClass('table-responsive');
		$crud->setModel($model,null,['complain_to_department','created_by','category','description','is_urgent','status','created_at']);
		$crud->grid->addHook('formatRow',function($g){
			if($g->model['image']){
				$g->current_row_html['profile_image'] = $g->model['image'];
			}else{
				$g->current_row_html['profile_image'] = 'websites/'.$this->app->current_website_name.'/www/dist/img/default-50x50.gif';
			}
			$g->current_row_html['created_at'] = date('M d, Y h:i a',strtotime($g->model['created_at']));

			if($g->model['is_urgent']){
				$g->current_row_html['urget_wrapper'] = '<span class="label label-danger">Urget</span>';
			}else{
				$g->current_row_html['urget_wrapper'] = " ";
			}
		});


		$crud->grid->addQuickSearch(['category','description']);
		$crud->grid->addPaginator(5);
		$crud->grid->removeColumn('edit');
		$crud->grid->removeColumn('delete');
		$crud->grid->removeColumn('status');
		$crud->grid->removeAttachment();
		// $crud->add('rakesh\apartment\Controller_ACL');
	}
}