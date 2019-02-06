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
		$this->app->stickyGET('type');
		$this->app->stickyGET('r_category_id');
		$this->app->stickyGET('r_affiliate_id');
		$this->app->stickyGET('action');

		if($_GET['type'] == "category"){
			$this->addCategoryForm();
		}elseif ($_GET['type'] == "affiliate") {
			$this->addAffiliateForm();
		}elseif($this->catid){
			$this->showRecords();
		}else{
			$this->showCategory();
		}

		$this->js(true)->find('.main-box-body')->addClass('table-responsive');

	}

	function addCategoryForm(){
		$model = $this->add('rakesh\apartment\Model_Category');
		$model->addCondition('apartment_id',$this->app->apartment->id);

		if($_GET['action'] == "edit"){
			$model->addCondition('id',$_GET['r_category_id']);
			$model->tryLoadAny();
			if(!$model->loaded()){
				$this->add('View_Error')->set('Record not loaded');
				return;
			}
		}

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->addContentSpot()
			->layout([
					'name'=>'c1~4',
					'status'=>'c2~4',
					'FormButtons~&nbsp;'=>'c3~4',
				]);
		$form->setModel($model);

		$action = $_GET['action'];
		if($action == "add"){
			$this->title = "Add New Help Category";
		}elseif($action == "edit"){
			$this->title = "Edit Help Category";
		}
		$form->addSubmit('Save')->addClass('btn btn-primary');

		if($form->isSubmitted()){			
			$form->save();
			$this->app->stickyForget('type');
			$this->app->stickyForget('action');
			$this->app->stickyForget('r_category_id');

			$form->js()->univ()->redirect($this->app->url('dashboard',['mode'=>'helpdesk']))->execute();
		}

		
	}

	function addAffiliateForm(){
		$cat_model = $this->add('rakesh\apartment\Model_Category');
		$cat_model->addCondition('apartment_id',$this->app->apartment->id);
		$cat_model->addCondition('id',$_GET['helpid']);
		$cat_model->tryLoadAny();
		if(!$cat_model->loaded()) throw new \Exception("category record not found");

		$model = $this->add('rakesh\apartment\Model_Affiliate');
		$model->addCondition('apartment_id',$this->app->apartment->id);
		$model->addCondition('category_id',$cat_model->id);

		if($_GET['action'] == "edit"){
			$model->addCondition('id',$_GET['r_affiliate_id']);
			$model->tryLoadAny();
			if(!$model->loaded()){
				$this->add('View_Error')->set('Record not loaded');
				return;
			}
		}

			
		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->addContentSpot()
			->layout([
					'name'=>'c1~4',
					'organization'=>'c2~4',
					'image_id~Photo'=>'c3~4',
					'contact_no'=>'c7~4',
					'intercom_number'=>'c8~4',
					'email_id'=>'c9~4',
					'status'=>'c11~4',
					'address'=>'c12~4',
					'narration'=>'c13~4',
					'FormButtons~&nbsp;'=>'c15~12',
				]);
		// $form->layout->add('View',null,'image_id')->setElement('img')->setAttr('src',$model['image'])->setStyle('width','50px');
		$form->setModel($model);
		$form->getElement('image_id')
			->setFormatFilesTemplate('view/fileupload');

		$action = $_GET['action'];
		if($action == "add"){
			$this->title = "Add New Record of ( ".$cat_model['name']." )";
		}elseif($action == "edit"){
			$this->title = "Edit Record";
		}

		$form->addSubmit('Save')->addClass('btn btn-primary');

		if($form->isSubmitted()){			
			$form->save();

			$this->app->stickyForget('type');
			$this->app->stickyForget('action');
			$this->app->stickyForget('r_category_id');
			$this->app->stickyForget('r_affiliate_id');
			// $this->app->stickyForget('helpid');
			$form->js()->univ()->redirect($this->app->url('dashboard',['mode'=>'helpdesk']))->execute();
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

		$crud = $this->add('xepan\base\CRUD',['edit_page'=>$this->app->url('dashboard',['mode'=>'helpdesk','type'=>'category']),'action_page'=>$this->app->url('dashboard',['mode'=>'helpdesk','type'=>'category'])]);
		// if(!$this->app->userIsApartmentAdmin){
		// 	$crud->allow_add = false;
		// 	$crud->allow_edit = false;
		// 	$crud->allow_del = false;
		// }
		$crud->grid->addHook('formatRow',function($g){
			$g->setTDParam('name','class','helpcategory');
			$g->setTDParam('records','class','helpcategory');
		});
		$crud->setModel($model,['name','records']);
		$crud->grid->addColumn('edit');
		$crud->grid->addColumn('delete');
		$crud->add('rakesh\apartment\Controller_ACL');
		// if($this->app->userIsApartmentAdmin){
		// }
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(25);
		$crud->grid->js('click',$this->js()->reload(['helpid'=>$this->js()->_selectorThis()->closest('tr')->attr('data-id')]))->univ()->_selector('tbody tr .helpcategory');
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
		$model->setOrder('name','asc');

		$lister = $this->add('xepan\base\CRUD',
				[
					'edit_page'=>$this->app->url('dashboard',['mode'=>'helpdesk','type'=>'affiliate']),
					'action_page'=>$this->app->url('dashboard',['mode'=>'helpdesk','type'=>'affiliate'])
				],null,['view/helplister']);

		$btn = $lister->addButton('Back')->addClass('btn btn-warning');
		$btn->js('click',$this->js()->reload(['helpid'=>0]));

		$lister->grid->addHook('formatRow',function($g){
			if($g->model['image_id'])
				$g->current_row_html['profile_image'] = $g->model['image'];
			else
				$g->current_row_html['profile_image'] = 'websites/'.$this->app->current_website_name.'/www/dist/img/avatar04.png';

			if($g->model['status'] == "Active"){
				$g->current_row_html['status_class'] = 'label-success';
			}else{
				$g->current_row_html['status_class'] = 'label-danger';
			}

			$color = ['bg-yellow','bg-blue','bg-info','bg-success','bg-green','bg-red','bg-gray','bg-orange','bg-warning'];
			$g->current_row['bg_color'] = $color[rand(0,8)];
		});

		$lister->setModel($model);
		$lister->grid->addQuickSearch(['name','contact_no','organization','email_id','address']);
		$lister->grid->addColumn('edit');
		$lister->grid->addColumn('delete');
		$lister->add('rakesh\apartment\Controller_ACL');

	}
}