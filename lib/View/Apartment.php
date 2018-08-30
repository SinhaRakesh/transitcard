<?php

namespace rakesh\apartment;

class View_Apartment extends \View{
	public $options = [];

	function init(){
		parent::init();
		
		$model = $this->app->apartment;	
		$model_id = @$this->app->apartment->id;	
		if(!$model_id){
			$model = $this->add('rakesh\apartment\Model_Apartment');
			$model->addCondition('created_by_id',$this->app->apartmentmember->id);
			$model->addCondition('status','Trial');
		}

		$form = $this->add('Form');
		$form->setModel($model);
		$form->addSubmit('Submit');

		if($form->isSubmitted()){
			$form->save();
			$apartment_model = $form->model;
			$this->app->apartmentmember['apartment_id'] = $apartment_model->id;
			$this->app->apartmentmember->save();

			$form->js(null,$form->js()->reload())->univ()->successMessage('Your Apartment Added Successfully')->execute();
		}


	}
}