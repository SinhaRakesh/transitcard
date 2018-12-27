<?php

namespace rakesh\apartment;

class View_Visitor extends \View{

	public $options = [];

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}

		$crud = $this->add('xepan\base\CRUD',['edit_page'=>$this->app->url('dashboard',['mode'=>'visitoredit']),'action_page'=>$this->app->url('dashboard',['mode'=>'visitoredit'])]);
		$model = $this->add('rakesh\apartment\Model_Visitor');
		$model->addCondition('apartment_id',$this->app->apartment->id);
		if($this->app->apartmentmember['flat']){
			$model->addCondition([
					['created_by_id',$this->app->apartmentmember->id],
					['member_id',$this->app->apartmentmember->id],
					['flat_id',explode(",",$this->app->apartmentmember['flat'])]
				]);
		}else{
			$model->addCondition([
					['created_by_id',$this->app->apartmentmember->id],
					['member_id',$this->app->apartmentmember->id]
				]);
		}
		
		$model->setOrder('id','desc');

		$crud->grid->addColumn('visitor_detail');
		$crud->grid->addColumn('purpose');
		$crud->grid->addColumn('meeting_with');
		$crud->setModel($model);
		$crud->grid->addQuickSearch(['name']);

		$crud->grid->addColumn('edit');
		$crud->grid->addColumn('delete');

		$crud->grid->addHook('formatRow',function($g){
			$v_detail = '<div class="box-comment">
							<div class="comment-text">
								<span class="username"> '.$g->model['name'].'</span>
							</div>';
							$v_detail .= '<i class="fa fa-clock-o">&nbsp;</i><span class="text-muted">'.($g->model['created_at']?:'------').'</span>';
						// if($g->model['mobile_no'])
							$v_detail .= '<br/><i class="fa fa-mobile">&nbsp;</i><span class="text-muted">'.($g->model['mobile_no']?:'----------').'</span>';
						// if($g->model['email_id'])
							$v_detail .= '<br/><i class="fa fa-envelope">&nbsp;</i><span class="text-muted">'.($g->model['email_id']?:'----------').'</span>';
						// if($g->model['address'])
							$v_detail .= '<br/><i class="fa fa-map-marker">&nbsp;</i><span class="text-muted">'.($g->model['address']?:'----------').'</span>';

			$v_detail .= '</div>';

			$g->current_row_html['visitor_detail'] = $v_detail;
			$g->current_row_html['purpose'] = $g->model['title'].'<div class="text-muted">'.$g->model['message']."</div>";
			$g->current_row_html['meeting_with'] = $g->model['member'].'<div class="text-muted">'.$g->model['flat']."</div>";
		});

		$crud->grid->js(true)->find('.main-box-body')->addClass('table-responsive');
		$fields = $model->getActualFields();
		foreach ($fields as $key => $value) {
			if($value == "status") continue;
			$crud->grid->removeColumn($value);
		}

		$crud->grid->addPaginator(25);
		
		$acl = $crud->add('rakesh\apartment\Controller_ACL');
	}
}