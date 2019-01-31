<?php

namespace rakesh\apartment;

class View_InvoiceEdit extends \View{

	public $options = [];
	public $title = "Edit Invoice";

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update partment data');
			return;
		}

		$action = $this->app->stickyGET('action');
		$dataid = $this->app->stickyGET('r_payment_transaction_id');

		$model = $this->add('rakesh\apartment\Model_Invoice');
		$model->addCondition('apartment_id',@$this->app->apartment->id);

		if($action == "edit"){
			$this->title = "Edit Bill";
			$this->app->template->trySet('page_title','Edit Bill');
			if(!$dataid){
				$this->add('View_Error')->set('Bill record is not defined, 1009');
				return;
			}
			$model->addCondition('id',$dataid);
			$model->tryLoadAny();
			if(!$model->loaded()){
				$this->add('View_Error')->set('Bill record is not defined, 1009');
				return;
			}

			$form_fields = ['amount','panelty','payment_type','payment_narration','invoice_narration','status'];
			$layout = [
						'amount~Maintenance Amount'=>'c1~6',
						'panelty~Panelty Amount'=>'c2~6~Payment submission last date is <b>'.$this->app->apartment['last_submission_date']." ".date('M-Y',strtotime($model['created_at']))."</b> and Panelty Amount is: <b>".$this->app->apartment['penelty_amount']."</b>",
						'payment_type'=>'c3~6',
						'status'=>'c7~6',
						'payment_narration'=>'b1~6',
						'invoice_narration'=>'b2~6',
						'FormButtons~&nbsp;'=>'b5~12'
					];

			$name = "<h4> Bill No: ".$model['name']."</h4>".
					"<h5>Member: ".$model['member']." ".$model['flat']."</h5>".
					"<h5>Bill Month: ".date('M-Y',strtotime($model['created_at']))."</h5><br/>"
					;
			$this->add('View')->addClass('box box-body')->setHtml($name);

		}else{
			$this->title = "Add New Bill";
			$this->app->template->trySet('page_title','Add New Bill');
			$form_fields = ['flat_id','amount','panelty','payment_type','payment_narration','invoice_narration','status'];
			$layout = [

						'flat_id~For Member / Flat'=>'c1~12',
						'amount~Maintenance Amount'=>'c2~6',
						'panelty~Panelty Amount'=>'c3~6~Payment submission last date is <b>'.$this->app->apartment['last_submission_date']." ".date('M-Y',strtotime($model['created_at']))."</b> and Panelty Amount is: <b>".$this->app->apartment['penelty_amount']."</b>",
						'payment_type'=>'c4~6',
						'status'=>'c7~6',
						'payment_narration'=>'b1~6',
						'invoice_narration'=>'b2~6',
						'FormButtons~&nbsp;'=>'b5~12'
					];
			$model->getElement('flat_id')->getModel()
						->addCondition('apartment_id',$this->app->apartment->id)
						->addCondition('member_id','>',0)
						;
			$model->getElement('amount')->defaultValue($this->app->apartment['maintenance_amount']);
		}

		$today_date = date('d',strtotime($this->app->today));
		if($today_date > $this->app->apartment['last_submission_date']) $model->getElement('panelty')->defaultValue($this->app->apartment['penelty_amount']);

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->addContentSpot()
			->layout($layout);
		$form->setModel($model,$form_fields);

		// $form->getElement('payment_type')->validate('required');
		$form->getElement('amount')->validate('required');
		$form->getElement('status')->validate('required');

		$form->addSubmit('Save')->addClass('btn btn-primary');

		if($form->isSubmitted()){
			if($action == "add"){
				$flat = $this->add('rakesh\apartment\Model_Flat')->load($form['flat_id']);
				$form->model['member_id'] = $flat['member_id'];
				$form->model['created_at'] = $this->app->now;
				$form->model['created_by_id'] = $this->app->apartmentmember->id;
			}

			if($form['status'] == "Paid" && !$form['payment_type']){
				$form->displayError('payment_type','payment type must not be empty')->execute();
			}

			$form->model['updated_at'] = $this->app->now;
			$form->update();
			$this->app->redirect($this->app->url('dashboard',['mode'=>'invoices']));
		}
	}
}