<?php

namespace rakesh\apartment;

class Model_Staff extends \rakesh\apartment\Model_MemberAbstract{
	function init(){
		parent::init();
		
		$this->addCondition('is_staff',true);
		$this->addCondition([['is_group',false],['is_group',null]]);

		$config_model = $this->add('rakesh\apartment\Model_Config_Master')
					->tryLoadAny();
		$this->cat_value = [];
		if($config_model['staff_type']){
			foreach (explode(",", $config_model['staff_type']) as $key => $value) {
				$this->cat_value[trim($value)] = trim($value);
			}
		}
		
		$this->getElement('staff_type')->setValueList($this->cat_value);

		$this->addExpression('aadhar_card_photo')->set(function($m,$q){
			$attach = $this->add('xepan\base\Model_Attachment',['table_alias'=>'acp'])
					->addCondition('contact_id',$m->getElement('id'))
					->addCondition('title','aadhar_card_photo');
			return $q->expr('[0]',[$attach->fieldQuery('thumb_url')]);
		});

		$this->addExpression('pan_card_photo')->set(function($m,$q){
			$attach = $m->add('xepan\base\Model_Attachment',['table_alias'=>'pcp'])
					->addCondition('contact_id',$m->getElement('id'))
					->addCondition('title','pan_card_photo');
			return $q->expr('[0]',[$attach->fieldQuery('thumb_url')]);
		});


		$this->addExpression('police_verification_photo')->set(function($m,$q){
			$attach = $this->add('xepan\base\Model_Attachment',['table_alias'=>'pocp'])
					->addCondition('contact_id',$m->getElement('id'))
					->addCondition('title','police_verification_photo');
			return $q->expr('[0]',[$attach->fieldQuery('thumb_url')]);
		});


		$this->addExpression('email_id_1')->set(function($m,$q){
			$email = $m->add('xepan\base\Model_Contact_Email',['table_alias'=>'email1']);
			$email->addCondition("contact_id",$m->getElement('customer_id'));
			$email->addCondition("head","Official");
			$email->setLimit(1);
			return $q->expr('[0]',[$email->fieldQuery('value')]);
		});
		$this->addExpression('email_id_2')->set(function($m,$q){
			$email = $m->add('xepan\base\Model_Contact_Email',['table_alias'=>'email2']);
			$email->addCondition("contact_id",$m->getElement('customer_id'));
			$email->addCondition("head","Personal");
			$email->setLimit(1);
			return $q->expr('[0]',[$email->fieldQuery('value')]);
		});
		$this->addExpression('mobile_no_1')->set(function($m,$q){
			$phone = $m->add('xepan\base\Model_Contact_Phone',['table_alias'=>'phone1']);
			$phone->addCondition("contact_id",$m->getElement('customer_id'));
			$phone->addCondition("head","Official");
			$phone->setLimit(1);
			return $q->expr('[0]',[$phone->fieldQuery('value')]);
		});
		$this->addExpression('mobile_no_2')->set(function($m,$q){
			$phone = $m->add('xepan\base\Model_Contact_Phone',['table_alias'=>'phone2']);
			$phone->addCondition("contact_id",$m->getElement('customer_id'));
			$phone->addCondition("head","Personal");
			$phone->setLimit(1);
			return $q->expr('[0]',[$phone->fieldQuery('value')]);
		});
	}
}