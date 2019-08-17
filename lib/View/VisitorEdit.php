<?php

namespace rakesh\apartment;

class View_VisitorEdit extends \View{

	public $options = [];
	public $title = "Add New Visitor Request";

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}
		
		$action = $this->app->stickyGET('action');
		$visitor_id = $this->app->stickyGET('r_visitor_id');
		
		$model = $this->add('rakesh\apartment\Model_Visitor');
		$model->addCondition('apartment_id',$this->app->apartment->id);
		if($action == "edit"){
			$this->title = "Edit Visitor";
			$model->addCondition('id',$visitor_id);
			$model->tryLoadAny();
			if(!$model->loaded()){
				$this->add('View_Error')->set('Visitor record not loaded');
				return;
			}
		}else{
			$model->addCondition('created_by_id',$this->app->apartmentmember->id);
		}

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->addContentSpot()
			->layout([
					'name~Visitor Name'=>'Visitor Detail~c1~6',
					'image_id~Visitor Photo'=>'c7~6',
					'mobile_no'=>'c2~6',
					'email_id'=>'c3~6',
					'address'=>'c4~6',
					'visitor_narration'=>'c5~6',

					'vehical_type'=>'Vehical Details~e1~3~Bike, Car etc',
					'vehical_no'=>'e2~3',
					'vehical_model'=>'e3~3',
					'vehical_color'=>'e4~3',
					'person_count'=>'e5~3',
					'vehical_detail~Vehical Other Details'=>'e6~6',

					'title'=>'Meeting Purpose~d1~12',
					'message'=>'d2~12',

					'flat_id~Flat Member'=>'Meeting With~e1~6',
					'FormButtons~&nbsp;'=>'z1~12'
				]);

		$form->setModel($model,['name','image_id','mobile_no','email_id','address','title','message','flat_id','visitor_narration','vehical_type','vehical_no','vehical_model','vehical_color','vehical_detail','person_count']);
		$field_flat = $form->getElement('flat_id');
		$field_flat->setEmptyText('Please Select');
		$field_flat->getModel()
				->addCondition('apartment_id',$this->app->apartment->id)
				->title_field = "name_with_member";

		$form->getElement('name')->validate('required');
		$form->getElement('mobile_no')->validate('required');
		$form->getElement('title')->validate('required');

		// $field_member = $form->getElement('member_id');
		// $field_member->getModel()->addCondition('apartment_id',$this->app->apartment->id);
		// $field_member->validate('required');


		$form->addSubmit('Submit')->addClass('btn btn-primary');

		if($form->isSubmitted()){
			$form->save();
			$form->model->sendNotification();
			$this->app->redirect($this->app->url('dashboard',['mode'=>'visitor']));
		}

	}
}