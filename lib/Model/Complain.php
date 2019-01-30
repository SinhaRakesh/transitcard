<?php

namespace rakesh\apartment;

class Model_Complain extends \xepan\base\Model_Table{
	public $table = "r_complain";
 	public $status = ['Draft','Pending','Closed','Rejected'];
	public $actions = [
					'Draft'=>['view','receive','reject','edit','delete'],
					'Pending'=>['view','close','reject','edit','delete'],
					'Closed'=>['view','edit','delete'],
					'Rejected'=>['view','edit','delete']
				];

	function init(){
		parent::init();

		$this->hasOne('rakesh\apartment\Apartment','apartment_id')->defaultValue($this->app->apartment->id);
		$this->hasOne('rakesh\apartment\Model_Member','created_by_id')->defaultValue($this->app->apartmentmember->id);
		$this->hasOne('rakesh\apartment\Model_Member','pending_by_id');
		$this->hasOne('rakesh\apartment\Model_Member','closed_by_id');
		$this->hasOne('rakesh\apartment\Model_Member','rejected_by_id');
		$this->hasOne('rakesh\apartment\Model_ComplainDepartment','complain_to_department_id');

		$this->addField('name')->defaultValue($this->id);
		$cat_field = $this->addField('category');
		$this->addField('description')->type('text');
		$this->addField('is_urgent')->type('boolean');
		$this->addField('status')->enum($this->status)->defaultValue('Pending');

		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now);
		$this->addField('pending_at')->type('datetime');
		$this->addField('closed_at')->type('datetime');
		$this->addField('rejected_at')->type('datetime');

		$this->addField('close_narration')->type('text');
		$this->addField('reject_narration')->type('text');
		
		$config_model = $this->add('rakesh\apartment\Model_Config_Master')
					->tryLoadAny();
		$this->cat_value = [];
		if($config_model['complain_category']){
			foreach (explode(",", $config_model['complain_category']) as $key => $value) {
				$this->cat_value[trim($value)] = trim($value);
			}
		}
		$cat_field->setValueList($this->cat_value);

		$this->add('dynamic_model\Controller_AutoCreator');
	}

	function receive(){
		$this['status'] = 'Pending';
		$this['name'] = $this->getNextID() + 1;
		$this['pending_at'] = $this->app->now;
		$this['pending_by_id'] = $this->app->apartmentmember->id;
		$this->save();
		// $this->sendNotification();
	}

	function getNextID(){
		return $this->add('rakesh\apartment\Model_Complain')
				->addCondition('status','<>','Draft')
				->count()->getOne();
	}
	
	function addDefault(){
		$m = $this->add('rakesh\apartment\Model_Complain');
		$m->addCondition('apartment_id',$this->app->apartment->id);
		$m->addCondition('name','Complain Department');
		$m->tryLoadAny();
		$m->save();
	}

	function close(){
		$this['status'] = 'Closed';
		$this['closed_by_id'] = $this->app->apartmentmember->id;
		$this['closed_at'] = $this->app->now;
		$this['close_narration'] = "";
		$this->save();
		$this->sendNotification();
		// send push notification all apartment admin and to user;
	}

	function reject(){
		$this['status'] = 'Rejected';
		$this['rejected_by_id'] = $this->app->apartmentmember->id;
		$this['rejected_at'] = $this->app->now;
		$this['reject_narration'] = "";
		$this->save();
		$this->sendNotification();
		// send push notification all apartment admin and to user;
	}

	function sendNotification(){

		if($this['status'] == 'Pending'){
			// send to apartment admin message
			$from_id = $this['created_by_id'];
			$from_row = ['id'=>$this['created_by_id'],'name'=>[$this['created_by']]];
			$to_id = "";
			$member_model = $this->add('rakesh\apartment\Model_Member');
			$member_model->addCondition('apartment_id',$this->app->apartment->id);
			$member_model->addCondition('is_apartment_admin',true);
			$to_row = [];
			foreach ($member_model as $model) {
				$temp = ['name'=>$model['name'],'id'=>$model['id']];
				$to_row[] =$temp; 
			}
			$sub_type = "ComplainSubmitted";
			$title = "New Complain of ".$this['category']." submitted by ".$this['created_by'];
		}else{
			// send apartment admin to complain created by user
			$sub_type = "ComplainAction";
			$from_id = $this[strtolower($this['status']).'_by_id'];
			$from_row = [['name'=>$this[strtolower($this['status']).'_by'],'id'=>$from_id]];
			$to_id = $this['created_by_id'];
			$to_row = [['name'=>$this['created_by'],'id'=>$this['created_by_id']]];
			$title = "Your Complain ".$this['category']." is ".$this['status']." by Admin ".$from_row['name'];			
		}

		$send_msg = $this->add('rakesh\apartment\Model_MessageSent');
		$send_msg['from_id'] = $from_id;
		$send_msg['from_raw'] = $from_row;
		$send_msg['to_id'] = $to_id;
		$send_msg['to_raw'] = json_encode($to_row);
		$send_msg['mailbox'] = "Complain";
		$send_msg['related_document_id'] = $this->app->apartment->id;
		$send_msg['created_by_id'] = $this->app->apartmentmember->id;
		$send_msg['related_id'] = $this->id;
		$send_msg['title'] = $title;
		$send_msg['description'] = $this['description'];
		$send_msg['type'] = 'notification';
		$send_msg['sub_type'] = $sub_type;
		$send_msg->save();
		$send_msg->sendNotification();
	}


}