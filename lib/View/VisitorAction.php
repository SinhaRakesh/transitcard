<?php

namespace rakesh\apartment;

class View_VisitorAction extends \View{

	public $options = [];
	public $title = "Visitor Action";

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}
		
		$id = $this->app->stickyGET('vrecord');
		$visitor_model = $this->add('rakesh\apartment\Model_Visitor');
		$visitor_model->load($id);

		if( !($visitor_model->loaded() && 
			$visitor_model['apartment_id'] == $this->app->apartment->id && 
			$visitor_model['member_id'] == $this->app->apartmentmember->id && 
			$visitor_model['status'] == "Requested") ){
			$this->add('View')->addClass('alert alert-danger')->set('you are not permitted');
			return;
		}

		$view = $this->add('View',null,null,['view/visitoraction']);
		$view->template->trySet('profile_image',($visitor_model['image']?:'websites/'.$this->app->current_website_name.'/www/dist/img/avatar04.png'));
		$view->setModel($visitor_model);

		$form = $this->add('Form');
		$col = $form->add('Columns')->addClass('row');
		$col1 = $col->addColumn(6)->addClass('col-md-6 col-sm-6 col-xs-6 col-lg- 6');
		$col2 = $col->addColumn(6)->addClass('col-md-6 col-sm-6 col-xs-6 col-lg- 6');
		$permitted_btn = $col1->addSubmit('Permitted')->addClass('btn btn-success btn-block');
		$denied_btn = $col2->addSubmit('Denied')->addClass('btn btn-danger btn-block');

		if($form->isSubmitted()){
			if($form->isClicked($permitted_btn)){
				$visitor_model->permitted();
			}
			if($form->isClicked($denied_btn)){
				$visitor_model->denied();
			}
			$form->js()->univ()->redirect($this->app->url('dashboard',['mode'=>'visitor']))->execute();
		}


	}
}