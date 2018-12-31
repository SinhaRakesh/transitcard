<?php

namespace rakesh\apartment;

class View_Master extends \View{

	public $options = [];
	public $title = "Configuration";

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
							'flat_status'=>'Text',
							'complain_category'=>'Text'
							],
					'config_key'=>'Apartment_Config',
					'application'=>'rakesh\apartment'
			]);
		$master_model->tryLoadAny();
		
		$ft = $this->add('View')->addClass('flattabs');
		$tabs = $ft->add('Tabs');
		$flat_size_tab = $tabs->addTab('Flat Size');
		$flat_status_tab = $tabs->addTab('Flat Status');
		$notice_tab = $tabs->addTab('Notice Board');
		$complain_tab = $tabs->addTab('Complain Category');
		$complain_department_tab = $tabs->addTab('Complain Department');


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

		// notice board
		$notice_tab->add('rakesh\apartment\View_NoticeBoard');

		// complain category
		$complain_form = $complain_tab->add('Form');
		$complain_status_field = $complain_form->addField('Text','complain_category',"")
				->setFieldHint('Comma(,) seperated values i.e. Accounts,Lifts')
				->set($master_model['complain_category']);
		if(!$master_model['complain_category']){
			$complain_status_field->set('Accounts,Drains,Electrical,Garbage Disposal,Garden,Housekeeping,Lifts,Playarea,Security,Plumbing');
		}
		$complain_form->addSubmit('Save')->addClass('btn btn-primary');
		if($complain_form->isSubmitted()){
			$master_model['complain_category'] = $complain_form['complain_category'];
			$master_model->save();
			$complain_form->js(null,$complain_form->js()->reload())->univ()->successMessage('saved successfully ')->execute();
		}

		// complain Department
		$cmp_dept_model = $this->add('rakesh\apartment\Model_ComplainDepartment');
		$cmp_dept_model->addCondition('apartment_id',$this->app->apartment->id);

		$complain_dept_crud = $complain_department_tab->add('xepan\base\CRUD');
		$complain_dept_crud->setModel($cmp_dept_model,null,['name']);
		$complain_dept_crud->grid->addPaginator(5);
		$complain_dept_crud->grid->js(true)->find('.main-box-body')->addClass('table-responsive');
		$complain_dept_crud->grid->addColumn('edit');
		$complain_dept_crud->grid->addColumn('delete');


	}
}