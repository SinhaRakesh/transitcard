<?php

namespace rakesh\apartment;

class Model_Visitor extends \xepan\base\Model_Table{
	public $table = 'r_visitor';
	public $title_field = 'name';
	public $status = ['Requested','Permitted','Denied'];
	public $actions = [
			'Requested'=>['view','permitted','denied','edit'],
			'Permitted'=>['view'],
			'Denied'=>['view']
		];

	public $status_color = ['Requested'=>'warning','Permitted'=>'success','Denied'=>'danger'];
	public $acl_type = "Apartment";

	function init(){
		parent::init();

		$this->hasOne('rakesh\apartment\Model_Apartment','apartment_id');
		$this->hasOne('xepan\base\Model_Contact','created_by_id')->system(true)->caption('Visitor Added By'); // must be employee or gatekipper
		$this->hasOne('rakesh\apartment\Model_Flat','flat_id');
		$this->hasOne('rakesh\apartment\Model_Member','member_id');
		
		$this->addField('name');
		$this->add('xepan/filestore/Field_File',['name'=>'image_id'])->allowHTML(true);

		$this->addField('mobile_no');
		$this->addField('email_id');
		$this->addField('address')->type('text');
		$this->addField('visitor_narration')->type('text');

		$this->addField('vehical_type')->hint('bike,car etc.');
		$this->addField('vehical_no');
		$this->addField('vehical_model');
		$this->addField('vehical_color');
		$this->addField('vehical_detail')->type('text');
		$this->addField('person_count');
		
		$this->addField('title');
		$this->addField('message')->type('text');
		
		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now)->system(true);
		$this->addField('permitted_at')->type('datetime');
		$this->addField('denied_at')->type('datetime');
		$this->hasOne('xepan\base\Model_Contact','permitted_by_id');
		$this->hasOne('xepan\base\Model_Contact','denied_by_id');

		$this->addField('status')->enum($this->status)->defaultValue('Requested');

		// $this->add('dynamic_model/Controller_AutoCreator');
		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){
		if(!$this['created_by_id']){
			$contact = $this->add('xepan\base\Model_Contact');
			if($contact->loadLoggedIn()){
				$this['created_by_id']	= $contact->id;
			}
		}

		if(!$this['member_id'] && $this['flat_id']){
			$f_model = $this->add('rakesh\apartment\Model_Flat')->load($this['flat_id']);
			$this['member_id'] = $f_model['member_id'];
		}
	}

	function permitted(){
		$this['permitted_at'] = $this->app->now;
		$this['permitted_by_id'] = $this->app->apartmentmember->id;
		$this['status'] = "Permitted";
		$this->save();
		$this->sendNotification();
	}

	function denied(){
		$this['denied_at'] = $this->app->now;
		$this['denied_by_id'] = $this->app->apartmentmember->id;
		$this['status'] = "Denied";
		$this->save();
		$this->sendNotification();
	}


	function sendNotification(){

		if($this['status'] == 'Requested'){
			// send to apartment admin message
			$from_id = $this['created_by_id'];
			$from_row = ['id'=>$this['created_by_id'],'name'=>[$this['created_by']]];
			$to_id = "";
			$to_row = [['name'=>$this['member'],'id'=>$this['member_id']]];
			$sub_type = "VisitorRequest";
			$title = "Visitor Request ";
			$detail = "Name: ".$this['name']." <br/>Purpose: ".$this['title']." <br/>".$this['message'];
			$js = $this->app->js()->univ()->newWindow($this->app->url("dashboard",['mode'=>'visitoraction','vrecord'=>$this['id']]));
		}else{
			$sub_type = "Visitor".$this['status'];
			$from_id = $this[strtolower($this['status']).'_by_id'];
			$from_row = [['name'=>$this[strtolower($this['status']).'_by'],'id'=>$from_id]];
			$to_id = $this['created_by_id'];
			$to_row = [['name'=>$this['created_by'],'id'=>$this['created_by_id']]];

			$title = "Visitor Request :: ".$this['status'];
			$detail = "request ".$this['status']." by ".$this[strtolower($this['status']).'_by'];
			$js = "";
		}

		$send_msg = $this->add('rakesh\apartment\Model_MessageSent');
		// $send_msg->addCondition('to_id',$to_id);
		// $send_msg->addCondition('related_document_id',$this->app->apartment->id);
		// $send_msg->addCondition('created_by_id',$this->app->apartmentmember->id);
		// $send_msg->addCondition('related_id',$this->id);
		// $send_msg->addCondition('sub_type',$sub_type);
		// $send_msg->tryLoadAny();
		// if($send_msg->loaded()){
			$send_msg['from_id'] = $from_id;
			$send_msg['from_raw'] = $from_row;
			$send_msg['to_id'] = $to_id;
			$send_msg['to_raw'] = json_encode($to_row);
			$send_msg['mailbox'] = "Visitor";
			$send_msg['related_document_id'] = $this->app->apartment->id;
			$send_msg['created_by_id'] = $this->app->apartmentmember->id;
			$send_msg['related_id'] = $this->id;
			$send_msg['title'] = $title;
			$send_msg['description'] = $detail;
			$send_msg['type'] = 'notification';
			$send_msg->save();
		// }
		$send_msg->sendNotification(['js'=>$js]);
	}

}