<?php

namespace rakesh\apartment;

class View_StaffEdit extends \View{

	public $options = [];
	public $title = "Staff";
	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}
		
		$action = $this->app->stickyGET("action");
		$contact_id = $this->app->stickyGET("contact_id");

		$model = $this->add('rakesh\apartment\Model_Staff');
		$model->addCondition('apartment_id',@$this->app->apartment->id);

		if($action == "edit" && $contact_id){
			$this->title = "Edit Staff Details";
			$model->addCondition('id',$contact_id);
			$model->tryLoadAny();

			if(!$model->loaded()){
				$this->add('View_Error')->set('you are not authorize, 1008');
				return;
			}
		}else{
			$this->title = "Add Staff";
		}

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->addContentSpot()
			->layout([
					'staff_type'=>'Staff Section~c1~3',
					'first_name'=>'c2~3',
					'last_name'=>'c3~3',
					'dob'=>'c4~3',
					'country_id~Country'=>'c11~3',
					'state_id~State'=>'c12~3',
					'city'=>'c13~3',
					'address'=>'c14~3',
					'mobile_no_1'=>'c21~3',
					'mobile_no_2'=>'c22~3',
					'email_id_1'=>'c23~3',
					'email_id_2'=>'c24~3',
					'organization~Organization\ Business'=>'c31~3',
					'status'=>'c32~3',

					'login_user_name'=>'Login Credential~b1~6~Please enter valid Email Id or Mobile No',
					'password'=>'b2~6',
					'image_id~Profile Photo'=>'Documents~d1~3',
					'aadhar_card_no'=>'d2~3',
					'aadhar_card_photo_id'=>'d2~3',
					'pan_card_number'=>'d3~3',
					'pan_card_photo_id'=>'d3~3',
					'police_verification_number'=>'d4~3',
					'police_verification_photo_id'=>'d4~3',
					'FormButtons~&nbsp;'=>'e1~12',
				]);

		$form->addField('login_user_name');
		$form->addField('password','password');
		$form->addField('mobile_no_1')->set($model['mobile_no_1']);
		$form->addField('mobile_no_2')->set($model['mobile_no_2']);
		$form->addField('email_id_1')->set($model['email_id_1']);
		$form->addField('email_id_2')->set($model['email_id_2']);

		// aadhar card
		$aadhar_model = $this->add('xepan\filestore\Model_File',['policy_add_new_type'=>true]);
		if($model->loaded() && $model['aadhar_card_photo_id'] > 0){
			$aadhar_model->tryLoad($model['aadhar_card_photo_id']);
		}
		$aadhar_card_photo = $model->getElement('aadhar_card_photo_id')->display(['form'=>'xepan\base\Upload']);
		$aadhar_card_photo->setModel($aadhar_model);

		// pancard card
		$pan_model = $this->add('xepan\filestore\Model_File',['policy_add_new_type'=>true]);
		if($model->loaded() && $model['pan_card_photo_id'] > 0){
			$pan_model->tryLoad($model['pan_card_photo_id']);
		}
		$pan_card_photo = $model->getElement('pan_card_photo_id')->display(['form'=>'xepan\base\Upload']);
		$pan_card_photo->setModel($pan_model);

		// policecard card
		$police_model = $this->add('xepan\filestore\Model_File',['policy_add_new_type'=>true]);
		if($model->loaded() && $model['police_verification_photo_id'] > 0){
			$police_model->tryLoad($model['police_verification_photo_id']);
		}
		$police_card_photo = $model->getElement('police_verification_photo_id')->display(['form'=>'xepan\base\Upload']);
		$police_card_photo->setModel($police_model);


		$form->setModel($model,['staff_type','image_id','first_name','last_name','dob','organization','country_id','state_id','city','address','aadhar_card_no','aadhar_card_photo','aadhar_card_photo_id','pan_card_number','pan_card_photo_id','police_verification_number','police_verification_photo_id','status']);
		
		$status_field = $form->addField('DropDown','status')->setValueList(array_combine($model->status,$model->status));
		if($model->loaded()){
			$status_field->set($model['status']);
		}

		$form->getElement('aadhar_card_photo_id')->setFormatFilesTemplate('view/fileupload');
		$form->getElement('pan_card_photo_id')->setFormatFilesTemplate('view/fileupload');
		$form->getElement('police_verification_photo_id')->setFormatFilesTemplate('view/fileupload');
		$img_field = $form->getElement('image_id');
		$img_field->getModel()->check_db_inTransaction_at_delete = false;
		$img_field->setFormatFilesTemplate('view/fileupload');

		// validation required
		$form->getElement('staff_type')->validate('required');
		$form->getElement('first_name')->validate('required');
		$form->getElement('last_name')->validate('required');
		// $form->getElement('login_user_name')->validate('required');
		// $form->getElement('password')->validate('required');

		if($model->loaded()){
			$form->getElement('login_user_name')->set($form->model['user']);
			$form->getElement('password')->set($form->model['login_password']);
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
				$user->addCondition('username',$form['login_user_name']);
				$user->tryLoadAny();

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


			try{
				$this->api->db->beginTransaction();

				if($form['login_user_name']){
					$user['password'] = $form['password'];
					$user['username'] = $form['login_user_name'];
					$user['status'] = $form['status'];
					$user->save();

					$form->model['user_id'] = $user->id;
				}
				
				$form->model['is_flat_owner'] = false;
				$form->model['status'] = $form['status'];
				$form->model['relation_with_head'] = "none";
				$form->model['aadhar_card_no'] = $form['aadhar_card_no'];
				$form->model['police_verification_number'] = $form['police_verification_number'];
				$form->model['pan_card_number'] = $form['pan_card_number'];

				// $form->model['image_id'] = $form['image_id']?:0;
				$form->save();
				
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

				if($form['aadhar_card_photo_id'] > 0){
					$model_attach = $this->add('xepan\base\Model_Attachment')
						->addCondition('contact_id',$member_model->id)
						->addCondition('title','aadhar_card_photo');
					$model_attach->tryLoadAny();
					$model_attach['file_id'] = $form['aadhar_card_photo_id'];
					$model_attach->save();
				}
				if($form['pan_card_photo_id'] > 0){
					$model_attach = $this->add('xepan\base\Model_Attachment')
						->addCondition('contact_id',$member_model->id)
						->addCondition('title','pan_card_photo');
					$model_attach->tryLoadAny();
					$model_attach['file_id'] = $form['pan_card_photo_id'];
					$model_attach->save();
				}
				if($form['police_verification_photo_id'] > 0){
					$model_attach = $this->add('xepan\base\Model_Attachment')
						->addCondition('contact_id',$member_model->id)
						->addCondition('title','police_verification_photo');
					$model_attach->tryLoadAny();
					$model_attach['file_id'] = $form['police_verification_photo_id'];
					$model_attach->save();
				}

				// contact no association
				$this->api->db->commit();
			}catch(\Exception_StopInit $e){

			}catch(\Exception $e){
				$this->api->db->rollback();
				throw $e;
			}

			$this->app->redirect($this->app->url('dashboard',['mode'=>'staff']));

		}

	}
}