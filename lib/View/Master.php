<?php

namespace rakesh\apartment;

class View_Master extends \View{

	public $options = [];
	// public $title = "Configuration";

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update partment data');
			return;
		}

		$master_model = $this->add('rakesh\apartment\Model_Config_Master',
			[
				'fields'=>[
							'flat_size'=>'Text',
							'flat_status'=>'Text'
							],
					'config_key'=>'Apartment_Config',
					'application'=>'rakesh\apartment'
			]);
		$master_model->tryLoadAny();

		$ft = $this->add('View')->addClass('flattabs');
		$tabs = $ft->add('Tabs');
		$flat_size_tab = $tabs->addTab('Flat Size');
		$flat_status_tab = $tabs->addTab('Flat Status');


		// flat size
		$form_size = $flat_size_tab->add('Form');
		$field_size = $form_size->addField('Text','flat_size',"")
				->setFieldHint('Comma(,) seperated values i.e. 1BHK,2BHK')
				->set($master_model['flat_size']);
		if(!$master_model['flat_size']){
			$field_size->set('1 BHK,2 BHK,3 BHK,4 BHK, Other');
		}
		$form_size->addSubmit('Save')->addClass('btn btn-primary');
		if($form_size->isSubmitted()){
			$master_model['flat_size'] = $form_size['flat_size'];
			$master_model->save();
			$form_size->js(null,$form_size->js()->reload())->univ()->successMessage('saved successfully ')->execute();
		}


		// flat status
		$form_status = $flat_status_tab->add('Form');
		$field_status = $form_status->addField('Text','flat_size',"")
				->setFieldHint('Comma(,) seperated values i.e. Sold,Rent')
				->set($master_model['flat_status']);
		if(!$master_model['flat_status']){
			$field_status->set('Sold,OnRent,Vacant');
		}
		$form_status->addSubmit('Save')->addClass('btn btn-primary');
		if($form_status->isSubmitted()){
			$master_model['flat_status'] = $form_status['flat_status'];
			$master_model->save();
			$form_size->js(null,$form_size->js()->reload())->univ()->successMessage('saved successfully ')->execute();
		}

	}
}