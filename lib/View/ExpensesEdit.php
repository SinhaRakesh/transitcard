<?php

namespace rakesh\apartment;

class View_ExpensesEdit extends \View{

	public $options = [];
	public $title = "Add Expenses";

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update partment data');
			return;
		}

		$action = $this->app->stickyGET('action');
		$dataid = $this->app->stickyGET('r_payment_transaction_id');

		$model = $this->add('rakesh\apartment\Model_Expenses');
		$model->addCondition('apartment_id',@$this->app->apartment->id);

		if($action == "edit"){
			$this->title = "Edit Expenses";
			$this->app->template->trySet('page_title','Edit Expenses');
			if(!$dataid){
				$this->add('View_Error')->set('Expenses record is not defined, 1009');
				return;
			}
			$model->addCondition('id',$dataid);
			$model->tryLoadAny();
			if(!$model->loaded()){
				$this->add('View_Error')->set('Expenses record is not defined, 1009');
				return;
			}
		}else{
			$this->title = "Add New Expenses";
			$this->app->template->trySet('page_title','Add New Expenses');
		}

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->addContentSpot()
			->layout([
				'name~Expenses Title'=>'c1~4',
				'expenses_category~Expenses Category'=>'c2~4',
				'affiliate_id~Related Person'=>'c3~4',
				'expenses_narration'=>'c7~12',
				'payment_type'=>'b1~4',
				'amount'=>'b2~4',
				'status'=>'b3~4',
				'payment_narration'=>'b4~12',
				'FormButtons~&nbsp;'=>'b5~12',
			]);
		$form->setModel($model,['name','expenses_category','affiliate_id','expenses','expenses_narration','payment_type','amount','payment_narration','status']);
		$form->addSubmit('Save')->addClass('btn btn-primary');

		if($form->isSubmitted()){
			$form->save();
			$this->app->redirect($this->app->url('dashboard',['mode'=>'expenses']));
		}
	}
}