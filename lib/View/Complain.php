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
		$model->addExpression('flat_name',function($m,$q){
			$model =  $m->add('rakesh\apartment\Model_MemberAbstract')
				->addCondition('id',$m->getElement('created_by_id'));
			return $q->expr('IFNULL([0],"")',[$model->fieldQuery('flat_name')]);
		});
		$model->addExpression('image',function($m,$q){
			$model =  $m->add('rakesh\apartment\Model_MemberAbstract')
				->addCondition('id',$m->getElement('created_by_id'));
			return $q->expr('IFNULL([0],"")',[$model->fieldQuery('image')]);
		});
		if(!$this->app->userIsApartmentAdmin){
			$model->addCondition('created_by_id',$this->app->apartmentmember->id);
			$model->actions = [
					'Pending'=>['view','edit','delete'],
					'Closed'=>['view'],
					'Rejected'=>['view']
				];
		}else{
			$model->actions = [
					'Pending'=>['view','close','reject'],
					'Closed'=>['view'],
					'Rejected'=>['view']
				];
		}

		$crud = $this->add('xepan\hr\CRUD',[
				'entity_name'=>'',
				'actionsWithoutACL'=>true,
				'edit_page'=>$this->app->url('dashboard',['mode'=>'complainedit']),
				'action_page'=>$this->app->url('dashboard',['mode'=>'complainedit'])
				],
				null,
				['view/complaingrid']
			);
		$crud->grid->js(true)->find('.main-box-body')->addClass('table-responsive');
		$crud->setModel($model);
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


		$qf = $crud->grid->addQuickSearch(['category','description','created_by','flat_name']);
		$crud->grid->addPaginator(10);
		$crud->grid->removeColumn('edit');
		$crud->grid->removeColumn('delete');

		$crud->grid->removeAttachment();
		$crud->add('rakesh\apartment\Controller_ACL');
		// $crud->grid->add('misc\Controller_AutoPaginator')->setLimit(5);
	}
}