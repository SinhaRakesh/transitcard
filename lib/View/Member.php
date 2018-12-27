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
		$model->addCondition('is_flat_owner',true);
		// if($this->app->userIsApartmentAdmin){
		// }
		$model->setOrder('name','asc');

		// $frame_options = [
		// 					'show'=> ['effect'=> 'fade','duration'=> 50],
		// 					'hide'=> ['effect'=> 'fade', 'duration'=> 50]
		// 				];
		$crud = $this->add('xepan\base\CRUD',['edit_page'=>$this->app->url('dashboard',['mode'=>'memberedit']),'action_page'=>$this->app->url('dashboard',['mode'=>'memberedit'])]);
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
						'login_user_name'=>'Login Credential~c6~6',
						'password'=>'c7~6',
						'flat'=>'Flat Association~c7~12',
					]);

			$form->addField('login_user_name');
			$form->addField('password','password');
			$flat_model = $this->add('rakesh\apartment\Model_Flat')->addCondition('apartment_id',@$this->app->apartment->id);
			$flat_field = $form->addField('xepan\base\Multiselect','flat');
			$flat_field->setModel($flat_model);
			$flat_field->setEmptyText('Please Select Associated Flat');
		}

		$crud->setModel($model,
			['first_name','last_name','address','city','state_id','country_id','organization','post','dob','relation_with_head','marriage_date','login_password','flat'],
			['name','flat_name']
		);
		
		if($crud->isEditing()){
			$form = $crud->form;

			if($form->isSubmitted()){

				if($form['login_user_name'] && !$form['password']) $form->displayError('password','login password must not be empty');

				if($form['login_user_name']){
					$user = $this->add('xepan\base\Model_User');
					$user->addCondition('username',$form['login_user_name']);
					$this->add('BasicAuth')
					->usePasswordEncryption('md5')
					->addEncryptionHook($user);
					$user->tryLoadAny();
					if($user->loaded()){
						$old_contact_model = $this->add('xepan\base\Model_Contact')
											->addCondition('user_id',$user->id)
											->tryLoadAny();
						if($old_contact_model->loaded() && $old_contact_model['id'] != $form->model->id)
							$form->displayError('login_user_name','User Name is already associated with other member');
					}

					$user['password'] = $form['password'];
					$user->save();

					$form->model['user_id'] = $user->id;
				}else{
					$form->model['user_id'] = 0;
				}
				
				$form->model->save();

				if($form['flat']){
					
					$flat_model = $this->add('rakesh\apartment\Model_Flat');
					$result = $flat_model->checkMemberAssociation($form['flat'],$form->model->id);

					if(!$result['result'])
						$form->displayError('flat',"Flat Associated with other Member are: ".trim($result['message'],", ") );

					$flat_model->associateWith($form['flat'],$form->model->id);
				}


			}else{
				$form->getElement('login_user_name')->set($form->model['user']);
				$form->getElement('password')->set($form->model['login_password']);
				$form->getElement('flat')->set(explode(",",$form->model['flat']));
			}
		}

		$crud->grid->addColumn('edit');
		$crud->grid->addColumn('delete');
		$crud->grid->addQuickSearch(['name'],['cancel_icon'=>'fa fa-remove']);
		$crud->grid->addPaginator(25);

		// $crud->grid->add('QuickSearch',null,'quick_search');
		$acl = $crud->add('rakesh\apartment\Controller_ACL');
	}
}