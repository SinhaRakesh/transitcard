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
		// if($this->app->userIsApartmentAdmin){
		// 	$model->addCondition([['is_flat_owner',true],['is_apartment_admin',true]]);
		// }
		$model->addExpression('email_id_1')->set(function($m,$q){
			$email = $m->add('xepan\base\Model_Contact_Email',['table_alias'=>'email1']);
			$email->addCondition("contact_id",$m->getElement('customer_id'));
			$email->addCondition("head","Official");
			$email->setLimit(1);
			return $q->expr('[0]',[$email->fieldQuery('value')]);
		});
		$model->addExpression('email_id_2')->set(function($m,$q){
			$email = $m->add('xepan\base\Model_Contact_Email',['table_alias'=>'email2']);
			$email->addCondition("contact_id",$m->getElement('customer_id'));
			$email->addCondition("head","Personal");
			$email->setLimit(1);
			return $q->expr('[0]',[$email->fieldQuery('value')]);
		});
		$model->addExpression('mobile_no_1')->set(function($m,$q){
			$phone = $m->add('xepan\base\Model_Contact_Phone',['table_alias'=>'phone1']);
			$phone->addCondition("contact_id",$m->getElement('customer_id'));
			$phone->addCondition("head","Official");
			$phone->setLimit(1);
			return $q->expr('[0]',[$phone->fieldQuery('value')]);
		});
		$model->addExpression('mobile_no_2')->set(function($m,$q){
			$phone = $m->add('xepan\base\Model_Contact_Phone',['table_alias'=>'phone2']);
			$phone->addCondition("contact_id",$m->getElement('customer_id'));
			$phone->addCondition("head","Personal");
			$phone->setLimit(1);
			return $q->expr('[0]',[$phone->fieldQuery('value')]);
		});

		if($action == "edit" && $contact_id){
			$this->title = "Edit Member Details";
			$model->addCondition('id',$contact_id);
			$model->tryLoadAny();

			if(!$model->loaded()){
				$this->add('View_Error')->set('you are not authorize, 1008');
				return;
			}

		}else{
			$this->title = "Add Flat Owner";
		}

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->addContentSpot()
			->layout([
					'first_name'=>'Member Section~c1~6',
					'last_name'=>'c2~6',
					'country_id~Country'=>'c3~3',
					'state_id~State'=>'c4~3',
					'city'=>'c5~3',
					'address'=>'c6~3',
					'mobile_no_1'=>'c7~3',
					'mobile_no_2'=>'c8~3',
					'email_id_1'=>'c9~3',
					'email_id_2'=>'c10~3',
					'image_id~Profile Picture'=>'a1~12',
					'organization~Organization\Business'=>'c14~6',
					'post'=>'c15~6',
					'relation_with_head'=>'c12~4',
					'dob'=>'c11~4',
					'marriage_date'=>'c13~4',
					'login_user_name'=>'Login Credential~b1~6~Please enter member valid Email Id or Mobile No',
					'password'=>'b2~6',
					'flat'=>'Flat Association~b3~12',
				]);

		$form->addField('login_user_name');
		$form->addField('password','password');
		$form->addField('mobile_no_1')->set($model['mobile_no_1']);
		$form->addField('mobile_no_2')->set($model['mobile_no_2']);
		$form->addField('email_id_1')->set($model['email_id_1']);
		$form->addField('email_id_2')->set($model['email_id_2']);

		$flat_model = $this->add('rakesh\apartment\Model_Flat')->addCondition('apartment_id',@$this->app->apartment->id);
		$flat_field = $form->addField('xepan\base\Multiselect','flat');
		$flat_field->setModel($flat_model);
		$flat_field->setEmptyText('Please Select Associated Flat');
		$form->setModel($model,['first_name','last_name','dob','relation_with_head','marriage_date','organization','post','country_id','state_id','city','address','image_id','image']);
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
				$username = trim($form['login_user_name']);
				$username_is_mobile = false;
				$username_is_email = false;
				if(is_numeric($username) && strlen($username) == 10){
					$username_is_mobile = true;
				}elseif(filter_var($username,FILTER_VALIDATE_EMAIL)){
					$username_is_email = true;
				}else{
					$form->displayError($form->getElement('login_user_name'),'username must be either mobile no or email id');
				}

				$user = $this->add('xepan\base\Model_User');
				if($model->loaded()){
					$user->load($model['user_id']);
				}else
					$user->addCondition('username',$form['login_user_name']);

				$this->add('BasicAuth')
					->usePasswordEncryption('md5')
					->addEncryptionHook($user);

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
				$user['username'] = $form['login_user_name'];
				$user->save();
				
				$form->model['user_id'] = $user->id;
				$form->model['is_flat_owner'] = true;
				$form->save();
				if($form['flat']){
					$flat_model->associateWith($form['flat'],$form->model->id);
				}
				
				$member_model = $form->model;
				$member_model->reload();
				
				// email id association
				if($form['email_id_1']){
					$member_model->checkEmail($form['email_id_1'],$member_model,'email_id_1');
					$email = $this->add('xepan\base\Model_Contact_Email',['bypass_hook'=>true]);
					$email->addCondition("contact_id",$member_model->id);
					$email->addCondition("head","Official");
					$email->tryLoadAny();
					$email['value'] = $form['email_id_1'];
					$email->save();
				}
				if($form['email_id_2']){
					$member_model->checkEmail($form['email_id_2'],$member_model,'email_id_2');
					$email = $this->add('xepan\base\Model_Contact_Email',['bypass_hook'=>true]);
					$email->addCondition("contact_id",$member_model->id);
					$email->addCondition("head","Personal");
					$email->tryLoadAny();
					$email['value'] = $form['email_id_2'];
					$email->save();
				}

				if($form['mobile_no_1']){
					$member_model->checkPhone($form['mobile_no_1'],$member_model,'mobile_no_1');
					$phone = $this->add('xepan\base\Model_Contact_Phone',['bypass_hook'=>true]);
					$phone->addCondition("contact_id",$member_model->id);
					$phone->addCondition("head","Official");
					$phone->tryLoadAny();
					$phone['value'] = $form['mobile_no_1'];
					$phone->save();
				}
				if($form['mobile_no_2']){
					$member_model->checkPhone($form['mobile_no_2'],$member_model,'mobile_no_2');
					$phone = $this->add('xepan\base\Model_Contact_Phone',['bypass_hook'=>true]);
					$phone->addCondition("contact_id",$member_model->id);
					$phone->addCondition("head","Personal");
					$phone->tryLoadAny();
					$phone['value'] = $form['mobile_no_2'];
					$phone->save();
				}

				// contact no association
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