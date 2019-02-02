<?php

namespace rakesh\apartment;

class View_Profile extends \View{

	public $options = [];

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}

		$this->setModel($this->app->apartmentmember);

		$this->passwordForm();
		$this->updateProfileForm();
	}

	function passwordForm(){

		$user = $this->add('xepan\base\Model_User')
				->load($this->api->auth->model->id);
		$this->api->auth->addEncryptionHook($user);

		$change_pass_form = $this->add('Form',null,'change_password');
		$change_pass_form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->addContentSpot()
			->layout([
				'username'=>'Update Your Password~b1~12',
				'old_password'=>'c1~4',
				'new_password'=>'c2~4',
				'retype_password'=>'c3~4',
				'FormButtons~&nbsp;'=>'c4~12',
			]);
		$change_pass_form->layout->add('View',null,'username')->set($user['username']);
		$change_pass_form->addField('password','old_password')->validate('required');
		$change_pass_form->addField('password','new_password')->validate('required');
		$change_pass_form->addField('password','retype_password')->validate('required');
		$change_pass_form->addSubmit('Change Password')->addClass('btn btn-danger');

		if($change_pass_form->isSubmitted()){
			if( $change_pass_form['new_password'] != $change_pass_form['retype_password'])
				$change_pass_form->displayError('retype_password','Password not match');
			
			if(!$this->api->auth->verifyCredentials($user['username'],$change_pass_form['old_password']))
				$change_pass_form->displayError('old_password','Password not match');

			if($user->updatePassword($change_pass_form['new_password'])){
				$this->app->auth->logout();
				$change_pass_form->js(null,$this->app->redirect('login'))->univ()->successMessage('Password Changed Successfully')->execute();
			}
		}

	}

	function updateProfileForm(){
		$col = $this->add('Columns',null,'setting_form');
		$col1 = $col->addColumn('4');
		$col2 = $col->addColumn('8');
		if($this->app->apartmentmember['image_id']){
			$col1->add('View')->setElement('image')->setAttr('src',$this->app->apartmentmember['image'])->setStyle('width','200px;');
		}
		$form = $col2->add('Form');
		// $form->add('xepan\base\Controller_FLC')
		// 	->showLables(true)
		// 	->addContentSpot()
		// 	->layout([
		// 		'change_profile_image~&nbsp;'=>'Update Your Image~b1~12',
		// 		'FormButtons~&nbsp;'=>'c4~12',
		// 	]);
		$field_upload = $form->addField('xepan\base\Upload','change_profile_image')->validate('required');
		$field_upload->setModel('xepan/filestore/Image');
		$field_upload->setFormatFilesTemplate('view/fileupload');

		$form->addSubmit('Update Photo')->addClass('btn btn-success');
		
		if($form->isSubmitted()){			
			// $this->add('xepan\filestore\Model_Image')
			// 	->load($this->app->apartmentmember['image_id'])
			// 	->delete();

			$this->app->apartmentmember['image_id'] = $form['change_profile_image'];
			$this->app->apartmentmember->save()->reload();
			$this->app->redirect($this->app->url());
		}


	}

	function defaultTemplate(){
		return ['view/profile'];
	}
}