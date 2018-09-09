<?php

namespace rakesh\apartment;

class View_Member extends \View{

	public $options = [];

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}

		$this->app->template->trySet('page_title','Apartment Member Management');

		$model = $this->add('rakesh\apartment\Model_Member');
		$model->addCondition('apartment_id',@$this->app->apartment->id);
		$model->setOrder('name','asc');
		$crud = $this->add('xepan\base\CRUD');
		if($crud->isEditing()){
			$form = $crud->form;
			$form->add('xepan\base\Controller_FLC')
				->addContentSpot()
				->makePanelsCoppalsible(true)
				->layout([
						'first_name'=>'Member Section~c1~6',
						'last_name'=>'c2~6',
						'dob'=>'c11~4',
						'relation_with_head'=>'c12~4',
						'marriage_date'=>'c13~4',
						'organization'=>'c14~6',
						'post'=>'c15~6',
						'country_id~Country'=>'c3~3',
						'state_id~State'=>'c4~3',
						'city'=>'c5~3',
						'address'=>'c6~3',
						'user_id~Username'=>'Login Credential~c6~3',
					]);
		}
		$crud->setModel($model,['first_name','last_name','address','city','state_id','country_id','organization','post','dob','relation_with_head','marriage_date','user_id'],['name','user','address','city','organization','dob','relation_with_head','marriage_date']);
		
		$crud->grid->addQuickSearch(['name','size']);
		$crud->grid->addPaginator(10);
		
	}
}