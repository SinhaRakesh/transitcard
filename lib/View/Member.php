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
		$model->addCondition([['is_flat_owner',true],['is_apartment_admin',true]]);
		// if($this->app->userIsApartmentAdmin){
		// }
		$model->setOrder('name','asc');
		// $frame_options = [
		// 					'show'=> ['effect'=> 'fade','duration'=> 50],
		// 					'hide'=> ['effect'=> 'fade', 'duration'=> 50]
		// 				];
		$crud = $this->add('xepan\base\CRUD',['entity_name'=>"",'edit_page'=>$this->app->url('dashboard',['mode'=>'memberedit']),'action_page'=>$this->app->url('dashboard',['mode'=>'memberedit'])]);

		$crud->grid->addHook('formatRow',function($g){
			$admin_html = ($g->model['is_apartment_admin']?'<span class="label bg-blue">Apartment Admin</span>':"");
							
			if($g->model['status'] == "Active")
				$status_html = '<span class="label bg-green">Active</span>';
			else
				$status_html = '<span class="label bg-red">InActive</span>';

			$g->current_row_html['name'] = $g->model['name']." - ".$g->model['flat_name']."<br/>".$admin_html."<br/>".$status_html;
		});

		$crud->add('xepan\base\Controller_Avatar',['name_field'=>'name']);
		$crud->grid->addColumn('avatar');
		// $crud->grid->noSno();
		$crud->setModel($model,
			['first_name','last_name','address','city','state_id','country_id','organization','post','dob','relation_with_head','marriage_date','login_password','flat'],
			['name','flat_name','image','status']
		);
		
		$crud->grid->removeColumn('image');
		$crud->grid->removeColumn('flat_name');
		$crud->grid->removeColumn('status');
		$crud->grid->addFormatter('name','Wrap');
		$crud->grid->js(true)->find('.main-box-body')->addClass('table-responsive');
		// if($crud->isEditing()){
		// 	$form = $crud->form;

		// 	if($form->isSubmitted()){

		// 		if($form['login_user_name'] && !$form['password']) $form->displayError('password','login password must not be empty');

		// 		if($form['login_user_name']){
		// 			$user = $this->add('xepan\base\Model_User');
		// 			$user->addCondition('username',$form['login_user_name']);
		// 			$this->add('BasicAuth')
		// 			->usePasswordEncryption('md5')
		// 			->addEncryptionHook($user);
		// 			$user->tryLoadAny();
		// 			if($user->loaded()){
		// 				$old_contact_model = $this->add('xepan\base\Model_Contact')
		// 									->addCondition('user_id',$user->id)
		// 									->tryLoadAny();
		// 				if($old_contact_model->loaded() && $old_contact_model['id'] != $form->model->id)
		// 					$form->displayError('login_user_name','User Name is already associated with other member');
		// 			}

		// 			$user['password'] = $form['password'];
		// 			$user->save();

		// 			$form->model['user_id'] = $user->id;
		// 		}else{
		// 			$form->model['user_id'] = 0;
		// 		}
				
		// 		$form->model->save();

		// 		if($form['flat']){
					
		// 			$flat_model = $this->add('rakesh\apartment\Model_Flat');
		// 			$result = $flat_model->checkMemberAssociation($form['flat'],$form->model->id);

		// 			if(!$result['result'])
		// 				$form->displayError('flat',"Flat Associated with other Member are: ".trim($result['message'],", ") );

		// 			$flat_model->associateWith($form['flat'],$form->model->id);
		// 		}


		// 	}else{
		// 		$form->getElement('login_user_name')->set($form->model['user']);
		// 		$form->getElement('password')->set($form->model['login_password']);
		// 		$form->getElement('flat')->set(explode(",",$form->model['flat']));
		// 	}
		// }

		$crud->grid->addColumn('edit');
		$crud->grid->addColumn('delete');
		$crud->grid->addQuickSearch(['name'],['cancel_icon'=>'fa fa-remove']);
		$crud->grid->addPaginator(25);

		$acl = $crud->add('rakesh\apartment\Controller_ACL');
	}
}