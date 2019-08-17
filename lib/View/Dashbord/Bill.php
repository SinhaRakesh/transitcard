<?php

namespace rakesh\apartment;

class View_Dashbord_Bill extends \View{

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update partment data');
			return;
		}
		
		$model =  $this->add('rakesh\apartment\Model_Invoice');
		$model->addCondition('member_id',$this->app->apartmentmember->id);
		$model->addCondition('apartment_id',$this->app->apartment->id);
		$model->setOrder('id','desc');

		if(!$model->count()->getOne()) return;

		$model->getElement('created_at')->caption('month');
		$grid = $this->add('xepan\base\Grid');
		$grid->addClass('box');
		$grid->addHook('formatRow',function($g){
			$g->current_row_html['created_at']  = date('M-Y',strtotime($g->model['created_at']));
			if($g->model['status'] == "Paid"){
				$g->current_row_html['status'] = '<span class="badge bg-green">Paid<span/>';
			}else{
				$g->current_row_html['status'] = '<span class="badge bg-green">Due<span/>';
			}
		});
		$grid->template->trySetHtml('grid_heading_left','<h4 class="box-header">Maintenance Bill</h4>');
		$grid->setModel($model,['name','created_at','amount','status']);
		// $grid->js(true)->find('.main-box-body')->addClass('table-responsive');
		$grid->addPaginator(5);
	}
}