<?php

namespace rakesh\apartment;

class View_BlockEdit extends \View{

	public $options = [];
	public $title = "";
	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update partment data');
			return;
		}


		$block_id = $this->app->stickyGET('r_block_id');

		$model = $this->add('rakesh\apartment\Model_Block');
		$model->addCondition('apartment_id',@$this->app->apartment->id);
		if($block_id){
			$model->addCondition('id',$block_id);
			$model->tryLoadAny();
			$this->app->template->trySet('page_title','Edit Block');
			$this->title = 'Edit Block';
		}else{
			$this->app->template->trySet('page_title','Add New Block');
			$this->title = 'Add New Block';
		}

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->addContentSpot()
			->makePanelsCoppalsible(true)
			->layout([
				'name~Block Name'=>'c1~6',
				'status'=>'c3~3',
				'FormButtons~&nbsp;'=>'c4~3'
			]);
		$form->setModel($model,null,['name','status']);
		$form->addSubmit('update')->addClass('btn btn-primary');
		if($form->isSubmitted()){
			$form->save();
			$this->app->redirect($this->app->url('dashboard',['mode'=>'block']));
		}

	}
}