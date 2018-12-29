<?php

namespace rakesh\apartment;

class View_NoticeBoard extends \View{

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update partment data');
			return;
		}
		
		$nb = $this->add('rakesh\apartment\Model_NoticeBoard');
		$nb->getElement('description')->display(['form'=>'xepan\base\RichText']);
		$nb->addCondition('related_id',$this->app->apartment->id);
		$nb->setOrder('created_at','desc');

		if($this->app->userIsApartmentAdmin){
			$crud = $this->add('xepan\base\CRUD',
					[
						'entity_name'=>'Notice',
						'edit_page'=>$this->app->url('dashboard',['mode'=>'noticeboard']),
						'action_page'=>$this->app->url('dashboard',['mode'=>'noticeboard'])
					],
					null,['view/noticeboard']
				);
			$grid = $crud->grid;
		}else{
			$grid = $crud = $this->add('xepan\base\Grid',null,null,['view/noticeboard']);
		}
		
		$grid->addHook('formatRow',function($g){
			$g->current_row_html['description'] = $g->model['description'];
			$g->current_row_html['display_date'] = date('d-M-Y',strtotime($g->model['flags']));
		});
		$crud->setModel($nb);
		$grid->addColumn('edit');
		$grid->addColumn('delete');
		$grid->addPaginator(5);

		
	}
}