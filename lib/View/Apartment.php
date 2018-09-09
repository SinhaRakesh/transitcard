<?php

namespace rakesh\apartment;

class View_Apartment extends \View{
	public $options = [];

	function init(){
		parent::init();
		
		$this->app->template->set('page_title','Apartment Profile & Setting');

		$model = $this->app->apartment;	
		$model_id = @$this->app->apartment->id;	
		if(!$model_id){
			$model = $this->add('rakesh\apartment\Model_Apartment');
			$model->addCondition('created_by_id',$this->app->apartmentmember->id);
			$model->addCondition('status','Trial');
		}

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->makePanelsCoppalsible(true)
			->addContentSpot()
			->layout([
				'name~Your Apartment Name'=>'Apartment Information~c1~12',
				'country_id~Country'=>'c31~3',
				'state_id~State'=>'c32~3',
				'city'=>'c33~3',
				'address'=>'c34~3',
				'view~<hr/><h3>Billing Dates</h3>'=>'c41~12',
				'bill_generation_date'=>'c21~6',
				'last_submission_date'=>'c22~6',
				'view1~<hr/><h3>Builder Information</h3>'=>'c51~12',
				'builder_name'=>'c11~4',
				'builder_mobile_no'=>'c12~4',
				'builder_email_id'=>'c13~4'
			]);

		$form->setModel($model,['name','city','address','builder_name','builder_email_id','builder_mobile_no','state_id','country_id','bill_generation_date','last_submission_date']);
		$form->addSubmit('Update Informartion')->addClass('btn btn-primary text-center');

		if($form->isSubmitted()){
			$form->save();
			$apartment_model = $form->model;
			$this->app->apartmentmember['apartment_id'] = $apartment_model->id;
			$this->app->apartmentmember->save();

			$form->js(null,$form->js()->reload())->univ()->successMessage('Apartment Information Updated Successfully')->execute();
		}


	}
}