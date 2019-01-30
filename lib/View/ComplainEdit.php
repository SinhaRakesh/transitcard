<?php

namespace rakesh\apartment;

class View_ComplainEdit extends \View{

	public $options = [];
	public $title = "Edit Complain";

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}
		
		$action = $this->app->stickyGET('action');
		$data_id = $this->app->stickyGET('r_complain_id');

		$model = $this->add('rakesh\apartment\Model_Complain');
		$model->addCondition('apartment_id',@$this->app->apartment->id);

		$dept_model = $model->getElement("complain_to_department_id")
				->getModel();
		$dept_count = $dept_model->addCondition('apartment_id',$this->app->apartment->id)
			->count()->getOne();
		if(!$dept_count){
			$dept_model->addDefault();
		}

		if($action == "edit"){
			$this->title = "Edit Complain";
			if(!$data_id){
				$this->add('View_Error')->set('record is not defined, 1009');
				return;
			}
			$model->addCondition('id',$data_id);
			$model->tryLoadAny();
			if(!$model->loaded()){
				$this->add('View_Error')->set('record is not defined, 1009');
				return;
			}
		}else{
			$this->title = "New Complain";
			$model->addCondition('created_by_id',$this->app->apartmentmember->id);
			$model->addCondition('pending_by_id',$this->app->apartmentmember->id);
			$model->addCondition('status','Pending');
		}
		
		$form_field = ['complain_to_department_id','category','description','is_urgent'];
		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->addContentSpot()
			->layout([
				'complain_to_department_id~Complain To Department'=>'c1~12',
				'category'=>'c2~12',
				'description'=>'c10~12',
				'is_urgent~'=>'c11~12',
				'FormButtons~&nbsp;'=>'c12~4',
			]);
		$form->setModel($model,$form_field);

		$form->getElement('complain_to_department_id')->validate('required');
		$form->getElement('category')->validate('required');
		$form->getElement('description')->validate('required');
		$form->addSubmit('Submit')->addClass('btn btn-primary');

		if($form->isSubmitted()){
			$form->save();
			$form->model->sendNotification();
			$this->app->redirect($this->app->url('dashboard',['mode'=>'complain']));
		}

	}
}