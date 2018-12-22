<?php

namespace rakesh\apartment;

class View_HelpDesk extends \View{

	public $options = [];
	public $catid = 0;
	public $title;
	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}

		$this->catid = $this->app->stickyGET('helpid')?:0;

		if($this->catid){
			$this->showRecords();
		}else{
			$this->showCategory();
		}

	}

	function showCategory(){
		$this->js(true)->_selector('h1.page-title')->html("Help Desk");

		$model = $this->add('rakesh\apartment\Model_Category');
		$model->addExpression('records')->set(function($m,$q){
			return $q->expr('[0]',[$m->refSQL('Affiliates')->count()]);
		})->caption('Related Contacts');

		$model->addCondition('apartment_id',$this->app->apartment->id);
		$model->setOrder('name','asc');

		$crud = $this->add('xepan\base\CRUD');
		if(!$this->app->userIsApartmentAdmin){
			$crud->allow_add = false;
			$crud->allow_edit = false;
			$crud->allow_del = false;
		}
		$crud->setModel($model,['name','records']);
		if($this->app->userIsApartmentAdmin){
			$crud->grid->addColumn('edit');
			$crud->grid->addColumn('delete');
		}
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(25);
		$url = $this->app->url(null);
		$crud->grid->js('click',$this->js()->reload(['helpid'=>$this->js()->_selectorThis()->attr('data-id')]))->univ()->_selector('tbody tr[data-id]');
	}

	function showRecords(){
		$cat_model = $this->add('rakesh\apartment\Model_Category')->load($this->catid);

		$title = $cat_model['name']." Help Desk Details";
		$this->js(true)->_selector('h1.page-title')->html($title);
		// $heading_view = $this->add('View')->setElement('h3')->setHtml($title);

		$model = $this->add('rakesh\apartment\Model_Affiliate');
		$model->addCondition('category_id',$this->catid);
		if(!$this->app->userIsApartmentAdmin){
			$model->addCondition('status','Active');
		}
		$model->setOrder('name','desc');

		if($this->app->userIsApartmentAdmin){
			$lister = $this->add('xepan\base\CRUD');
		}else{
			$lister = $this->add('xepan\base\Grid');
		}
		
		$btn = $lister->addButton('Back')->addClass('btn btn-warning');
		$btn->js('click',$this->js()->reload(['helpid'=>0]));
		$lister->setModel($model,['name','contact_no','email_id','address','narration']);
	}

}