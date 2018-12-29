<?php

namespace rakesh\apartment;

class View_NoticeBoardEdit extends \View{

	public $options = [];
	public $title = "Add New Notice";

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update partment data');
			return;
		}
		
		if(!$this->app->userIsApartmentAdmin){
			throw new \Exception("you are not the right person");
		}

		$action = $this->app->stickyGET('action');
		$noticeid = $this->app->stickyGET('communication_id');

		$model = $this->add('rakesh\apartment\Model_NoticeBoard');
		$model->addCondition('related_id',@$this->app->apartment->id);

		if($action == "edit"){
			$this->app->template->trySet('page_title','Edit Notice');
			$this->title = "Edit Notice";
			if(!$noticeid){
				$this->add('View_Error')->set('notice record is not defined, 1009');
				return;
			}

			$model->addCondition('id',$noticeid);
			$model->tryLoadAny();
			if(!$model->loaded()){
				$this->add('View_Error')->set('notice record is not defined, 1009');
				return;
			}
		}else{
			$model->addCondition('from_id',$this->app->apartmentmember->id);
			$model->addCondition('created_by_id',$this->app->apartmentmember->id);
			$model->getElement('created_at')->defaultValue($this->app->now);

			$this->title = "Add New Notice";
			$this->app->template->trySet('page_title','Add New Notice');
		}

		$model->getElement('flags')->type('datetime')->defaultValue($this->app->now);
		$model->getElement('tags')->type('datetime')->defaultValue($this->app->now);
		$model->getElement('description')->display(['form'=>'xepan\base\RichText']);
		
		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->addContentSpot()
			->layout([
				'title~Notice Title'=>'c1~12',
				'description~Notice Description'=>'c2~12',
				'flags~Display From Date'=>'c3~6',
				'tags~Valid Till Date'=>'c4~6',
				'FormButtons~&nbsp;'=>'c12~6',
			]);
		$form->setModel($model,['title','description','flags','tags']);
		$form->addSubmit('Save')->addClass('btn bg-purple');
		if($form->isSubmitted()){
			$form->save();
			$this->app->stickyForget('communication_id');
			$this->app->redirect($this->app->url('dashboard',['mode'=>'master']));
		}
	}
}