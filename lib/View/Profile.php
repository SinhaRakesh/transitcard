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

		if(!$this->app->apartmentmember['image']){
			$this->app->apartmentmember['image'] = 'websites/'.$this->app->current_website_name.'/www/dist/img/avatar04.png';
		}

		$this->setModel($this->app->apartmentmember);

		if($this->app->apartmentmember['skills']){
			$skil = explode(",", $this->app->apartmentmember['skills']);
			$values = "";
			$label = ['label-danger','label-success','label-info','label-danger','label-warning'];
			foreach ($skil as $key => $value) {
				$rand = rand(0,4);
				$values .= '<span class="label '.$label[$rand].'">'. $value .'</span>';
			}
			$this->template->trySetHtml('skills_value',$values);
		}

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
			$col1->add('View')->setElement('image')->setAttr('src',$this->app->apartmentmember['image'])->setStyle('width','100px;');
		}

		$form = $col2->add('Form');
		$field_upload = $form->addField('xepan\base\Upload','change_profile_image')->validate('required');
		$field_upload->setModel('xepan/filestore/Image');
		$field_upload->setFormatFilesTemplate('view/fileupload');

		$form->addSubmit('Update Photo')->addClass('btn btn-primary')->setStyle('margin-top','10px');
		
		if($form->isSubmitted()){			
			if(!$form['change_profile_image']) $form->displayError('change_profile_image','profile image must not be empty');
			$this->app->apartmentmember['image_id'] = $form['change_profile_image'];
			$this->app->apartmentmember->save()->reload();
			$this->app->redirect($this->app->url());
		}

		$this->add('View',null,'setting_form')->setElement('hr');
		$form_contact = $this->add('Form',null,'setting_form');
		$form_contact->add('xepan\base\Controller_FLC')
				->showLables(true)
				->makePanelsCoppalsible(true)
				->layout([
					'first_name'=>'Update Your Profile~c1~3',
					'last_name'=>'c2~3',
					'organization'=>'c3~3',
					'post'=>'c4~3',
					'country_id~Country'=>'c5~3',
					'state_id~State'=>'c6~3',
					'city'=>'c7~3',
					'address'=>'c8~3',
					'dob'=>'c9~4',
					'marriage_date'=>'c10~4',
					'relation_with_head'=>'c11~4',
					'education'=>'c12~4~ie. B.Tech in Computer science',
					'skills'=>'c13~4~comma(,) seperated multiple values ie. ui design, reading, dancing',
					'remark~About You'=>'c14~4',
					'FormSubmit~&nbsp;'=>'c15~12'
				]);

		$form_contact->setModel($this->app->apartmentmember,['first_name','last_name','country_id','state_id','city','address','organization','post','dob','relation_with_head','marriage_date','education','skills','remark']);
		$form_contact->addSubmit('Update')->addClass('btn btn-primary');
		if($form_contact->isSubmitted()){
			$form_contact->update();
			$form_contact->js(null,$form_contact->js()->univ()->successMessage('Profile Updated Successfully'))->univ()->redirect($this->app->url())->execute();
		}
	}

	function defaultTemplate(){
		return ['view/profile'];
	}
}