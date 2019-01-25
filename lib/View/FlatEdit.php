<?php

namespace rakesh\apartment;

class View_FlatEdit extends \View{

	public $options = [];
	public $title = "Add New Flat";

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update partment data');
			return;
		}
		$action = $this->app->stickyGET('action');
		$flatid = $this->app->stickyGET('r_flat_id');

		$model = $this->add('rakesh\apartment\Model_Flat');
		$model->addCondition('apartment_id',@$this->app->apartment->id);

		if($action == "edit"){
			$this->app->template->trySet('page_title','Edit Flat');
			$this->title = "Edit Flat";
			if(!$flatid){
				$this->add('View_Error')->set('flat record is not defined, 1009');
				return;
			}

			$model->addCondition('id',$flatid);
			$model->tryLoadAny();
			if(!$model->loaded()){
				$this->add('View_Error')->set('flat record is not defined, 1009');
				return;
			}
		}else{
			$this->title = "Add New Flat";
			$this->app->template->trySet('page_title','Add New Flat');
		}

		$model->getElement('member_id')
			->getModel()
				->addCondition('is_flat_owner',true)
				->addCondition('apartment_id',@$this->app->apartment->id);
		
		$model->getElement('block_id')
			->getModel()
				->addCondition('apartment_id',@$this->app->apartment->id);
		

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->addContentSpot()
			->layout([
				'block_id~Block'=>'c1~4',
				'name~Flat Name'=>'c2~4',
				'size~Flat Size'=>'c3~4',
				'status~Flat Status'=>'c4~4',
				'member_id~Flat Member'=>'b2~4',
				'is_generate_bill~&nbsp;'=>'b3~4',
				'FormButtons~&nbsp;'=>'c12~4',
			]);
		$form->setModel($model,['name','size','member_id','block_id','status','is_generate_bill']);

		$form->getElement('name')->validate('required');
		$form->getElement('size')->validate('required');
		$form->getElement('status')->validate('required');
		$form->addSubmit('save')->addClass('btn btn-primary');

		if($form->isSubmitted()){
			$form->save();
			$this->app->redirect($this->app->url('dashboard',['mode'=>'flat']));
		}
	}
}