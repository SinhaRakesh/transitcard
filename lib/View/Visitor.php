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

		$model = $this->add('rakesh\apartment\Model_Visitor');
		$model->addCondition('apartment_id',$this->app->apartment->id);

		$crud = $this->add('xepan\hr\CRUD',
				[
				'actionsWithoutACL'=>true,
				'status_color'=>$model->status_color,
				'edit_page'=>$this->app->url('dashboard',['mode'=>'visitoredit']),
				'action_page'=>$this->app->url('dashboard',['mode'=>'visitoredit'])]
			);

		if(!$this->app->userIsApartmentAdmin){
			$model->addCondition([
					['flat_id',$this->app->apartmentmember['flat_id']],
					['member_id',$this->app->apartmentmember['id']],
				]);
		}
		
		$model->setOrder('id','desc');

		// $crud->grid->addColumn('avatar');
		$crud->grid->addColumn('visitor_detail');
		// $crud->add('xepan\base\Controller_Avatar',['name_field'=>'name']);
		
		$crud->grid->addHook('formatRow',function($g){
			$date = $this->add('xepan\base\xDate');
			$diff = $date->diff($this->app->now,$g->model['created_at']);
			$vehical_detail = "vehical ".$g->model['vehical_type']."<strong> No:</strong> ".$g->model['vehical_no']." ".$g->model['vehical_model']." ".$g->model['vehical_color']." <i class='fa fa-users'></i> Persons ".$g->model['person_count'];

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
						$v_detail .="<br/>Meeting with: <span class='text-muted'>".$g->model['member']." ".$g->model['flat']."</span>";
			$v_detail .= '</div>';

			$name = $g->model['name']." <span class='fa fa-clock-o'> ".$diff."</span>".
					"<br/> ".$g->model['title'].
					"<br/><p class='text-muted'>".$g->model['message']."</p>".$vehical_detail;
					;
			// ------------------- expander html
			$exhtml = '<div class="box collapsed-box" style="border:0px;box-shadow:none;">
					  <div class="">
						<div type="button" class="" data-widget="collapse" style="background-color:white;padding:0px;margin:0px;text-align:left;">';
			$exhtml .= '<img class="pull-left" style="width:50px;height:50px;" src="'.$g->model['image'].'" />';
			$exhtml .= '<div class="box-title pull-left" style="margin-left:5px;">'.$name.'</div> </div></div>
						<div class="box-body" style="display: none;clear:both;">
					    '.$v_detail.'
					  </div>
					</div>';

			$g->current_row_html['visitor_detail'] = $exhtml;
		});
		$crud->setModel($model);
		$crud->grid->addQuickSearch(['name','title','message','']);


		$crud->grid->js(true)->find('.main-box-body')->addClass('table-responsive');
		$fields = $model->getActualFields();
		foreach ($fields as $key => $value) {
			$crud->grid->removeColumn($value);
		}
		$crud->grid->removeColumn('edit');
		$crud->grid->removeColumn('delete');
		$crud->grid->removeAttachment();
		$crud->grid->addPaginator(10);
		$crud->grid->addFormatter('visitor_detail','wrap');
		$crud->add('rakesh\apartment\Controller_ACL');
		
	}
}