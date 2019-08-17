<?php

namespace rakesh\apartment;

class View_ChatComplete extends \View{

	public $options = [];
	private $member_lister;
	private $form;
	private $chat_history_lister;

	private $contact_to_id = 0;
	private $contact_to_name = "";
	private $contact_to_mage = "";

	private $my_uuid = "";

	function init(){
		parent::init();
		
		// if(!@$this->app->apartment->id){
		// 	$this->add('View_Error')->set('first update partment data');
		// 	return;
		// }
		$this->contact_to_id = $this->app->stickyGET('contact_to_id')?:0;
		$this->contact_to_name = $this->app->stickyGET('contact_to_name')?:"";
		$this->contact_to_image = $this->app->stickyGET('contact_to_image')?:"";

		// $this->addForm();
		$this->addChatHistory();
		$this->addMemberLister();
		
	}

	function addForm(){

		$this->form = $form = $this->add('Form',null,'message_form',['form/horizontal']);
		$form->addField('Line','message','')->validate('required');
		
		$form->addSubmit('send');
	}

	function addMemberLister(){

		$this->member_lister = $lister = $this->add('CompleteLister',null,'ap_member_list',['view\chat','ap_member_list']);
		$lister->addHook('formatRow',function($l){
			if($l->model['image_id']){
				$l->current_row_html['profile_image'] = $l->model['image'];
			}else{
				$l->current_row_html['profile_image'] = 'websites/apartment/www/dist/img/avatar04.png';
			}

			$l->current_row_html['uuid'] = $this->app->normalizeName($this->app->apartment['name']).'_'.$this->app->apartment->id.'_'. $l->model->id;
		});

	}

	function addChatHistory(){
		// sent
		// replies
		$this->chat_history_lister = $lister = $this->add('CompleteLister',null,'ap_chat_lister',['view\chat','ap_chat_lister']);

		$lister->addHook('formatRow',function($l){
			if($l->model['from_id'] == $this->app->apartmentmember->id){
				$l->current_row_html['chat_direction'] = "sent";
			}else{
				$l->current_row_html['chat_direction'] = "replies";
			}

			if($l->model['image_id']){
				$l->current_row_html['profile_image'] = $l->model['image'];
			}else{
				$l->current_row_html['profile_image'] = 'websites/apartment/www/dist/img/avatar04.png';
			}

			if($l->model['created_at']){
				// $date = $this->add('xepan\base\xDate');
				// $diff = $date->diff($this->app->now,$l->model['created_at']);
				// $l->current_row_html['send_date'] = $diff;
				$l->current_row_html['send_date'] = date('M d H:i a',strtotime($l->model['created_at']));
			}else
				$l->current_row_html['send_date'] = "";

		});

		// $lister->addClass('ap-chat-message-trigger-reload');
		$lister->js('reload')->reload();

		if($this->contact_to_id){
			$this->form = $form = $lister->add('Form');
			$form->addField('Line','message');
			$form->addSubmit('save');
		}
	}

	function recursiveRender(){

		// member lister
		$member_model = $this->add('rakesh\apartment\Model_Member');
		$member_model->addCondition('apartment_id',$this->app->apartment->id)
					->addCondition('status','Active')
					->addCondition('id','<>',$this->app->apartmentmember->id);
		$this->member_lister->setModel($member_model);

		// chat history
		$chat_history_model = $this->add('rakesh\apartment\Model_MessageSent');
		$chat_history_model->addCondition('related_id',$this->app->apartment->id);
		$chat_history_model->addCondition([['from_id',$this->app->apartmentmember->id],['to_id',$this->app->apartmentmember->id]]);
		if($this->contact_to_id)
			$chat_history_model->addCondition([['from_id',$this->contact_to_id],['to_id',$this->contact_to_id]]);

		$chat_history_model->setLimit(4);
		$chat_history_model->setorder('id','desc');

		$this->chat_history_lister->setModel($chat_history_model);

		// if contact is selected then updated name
		$this->chat_history_lister->template->trySet('selected_name',$this->contact_to_name?:'Chat History');
		$this->chat_history_lister->template->trySet('selected_member_img',$this->contact_to_image);

		// form submission
		if($this->contact_to_id && $this->form->isSubmitted()){
			
			if(!$this->contact_to_id) $this->form->js()->univ()->errorMessage('please select member first from your member list')->execute();
			
			$send_msg = $this->add('rakesh\apartment\Model_MessageSent');

			$send_msg['from_id'] = $this->app->apartmentmember->id;
			$send_msg['from_raw'] = ['name'=>$this->app->apartmentmember['name'],'id'=>$this->app->apartmentmember->id];
			$send_msg['to_id'] = $this->contact_to_id;
			$send_msg['to_raw'] = json_encode([['name'=>$this->contact_to_name,'id'=>$this->contact_to_id]]);
			$send_msg['related_contact_id'] = $this->contact_to_id; // if communication is around some contact like group because group is contact
			$send_msg['mailbox'] = "InternalMessage";
			$send_msg['created_by_id'] = $this->contact_to_id;
			$send_msg['related_id'] = $this->app->apartment->id;
			// $send_msg['title'] = $f['subject'];
			$send_msg['description'] = $message = $this->form['message'];
			$send_msg->save();
			
			$send_date = $this->app->now;
			// $this->chat_history_lister->js()->reload()->execute();
			$send_html = '<li class="sent">
                  <div class="message-wrapper">
                    <img src="'.$this->contact_to_image.'" alt="" style="" />
                    <div class="message-content">'.$message.'</div>
                  </div>
                  <div class="message-otherinfo-wrapper">
                    <div class="chat-otherinfo">
                      <span class="">'.$send_date.'</span>
                    </div>
                  </div>
                </li>';
			$js_array = [
					$this->chat_history_lister->js()->append($send_html)->_selector('.messages > ul'),
					$this->form->js()->reload()
				];
			$this->form->js(null,$js_array)->univ()->execute();
		}

		// reload member chat

		$js_reload = [
			$this->chat_history_lister->js()->reload([
				'contact_to_id'=>$this->js()->_selectorThis()->data('memberid'),
				'contact_to_name'=>$this->js()->_selectorThis()->data('name'),
				'contact_to_image'=>$this->js()->_selectorThis()->data('profileimage')
			]),
			$this->js()->removeClass('active')->_selector('#'.$this->member_lister->name.' li.active'),
			$this->js()->_selectorThis()->addClass('active'),
		];
		$this->member_lister->js('click',$js_reload)->_selector('li.contact');
		

		// // regiter login customer for live chat
		// $host = "ws://127.0.0.1:8890/";
		// $uu_id = $this->app->normalizeName($this->app->apartment['name']).'_'.$this->app->apartment->id.'_'. $this->app->apartmentmember->id;
		// $this->app->js(true)->_load('wsclient')->univ()->runWebSocketClient($host,$uu_id);

		parent::recursiveRender();

	}
	
	function defaultTemplate(){
		return ['view\chat'];
	}

}