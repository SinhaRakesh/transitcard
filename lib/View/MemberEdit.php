<?php

namespace rakesh\apartment;

class View_MemberEdit extends \View{

	public $options = [];
	public $title = "Flat Members";
	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}
		$action = $this->app->stickyGET("action");
		$contact_id = $this->app->stickyGET("contact_id");

		$model = $this->add('rakesh\apartment\Model_Member');
		$model->addCondition('apartment_id',@$this->app->apartment->id);
		if($this->app->userIsApartmentAdmin){
			$model->addCondition('is_flat_owner',true);
		}

		if($action == "edit" && $contact_id){
			$this->title = "Edit Member Details";
			$model->addCondition('id',$contact_id);
			$model->tryLoadAny();

			if(!$model->loaded()){
				$this->add('View_Error')->set('you are not authorize, 1008');
				return;
			}

		}else{
			$this->title = "Add New Member";
		}

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->addContentSpot()
			->layout([
					'first_name'=>'Member Section~c1~6',
					'last_name'=>'c2~6',
					'dob'=>'c11~4',
					'relation_with_head'=>'c12~4',
					'marriage_date'=>'c13~4',
					'organization~Organization\Business'=>'c14~6',
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

		$form->setModel($model,['first_name','last_name','dob','relation_with_head','marriage_date','organization','post','country_id','state_id','city','address']);
		$form->getElement('first_name')->validate('required');
		$form->getElement('last_name')->validate('required');
		$form->getElement('login_user_name')->validate('required');
		$form->getElement('password')->validate('required');

		if($model->loaded()){
			$form->getElement('login_user_name')->set($form->model['user']);
			$form->getElement('password')->set($form->model['login_password']);
			$form->getElement('flat')->set(explode(",",$form->model['flat']));
		}

		$form->addSubmit('Save')->addClass('btn btn-primary');

		if($form->isSubmitted()){

			if($form['login_user_name'] && !$form['password']) $form->displayError('password','login password must not be empty');

			// condition 1 : check login user name is already exit or not
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
						if($old_contact_model->loaded() && $old_contact_model['id'] != $model->id)
							$form->displayError('login_user_name','User Name is already associated with other member');
					}
			}

			// condition 2 : check flat is already associated with other or not
			if($form['flat']){
				$flat_model = $this->add('rakesh\apartment\Model_Flat');
				$result = $flat_model->checkMemberAssociation($form['flat'],$model->id);

				if(!$result['result'])
					$form->displayError('flat',"Flat Already Associated with other Member are: ".trim($result['message'],", ") );
			}

			try{
				$this->api->db->beginTransaction();

				$user['password'] = $form['password'];
				$user->save();
				$form->model['user_id'] = $user->id;
				$form->save();
				if($form['flat']){
					$flat_model->associateWith($form['flat'],$form->model->id);
				}
				
				$this->api->db->commit();
			}catch(\Exception_StopInit $e){

			}catch(\Exception $e){
				$this->api->db->rollback();
				throw $e;
			}

			$this->app->redirect($this->app->url('dashboard',['mode'=>'member']));

		}

			
	}
}