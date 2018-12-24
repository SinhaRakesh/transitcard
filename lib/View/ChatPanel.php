<?php

namespace rakesh\apartment;

class View_ChatPanel extends \View{

	public $options = [];
	private $member_lister;
	private $form;
	private $chat_history_lister;

	private $contact_to_id = 0;
	private $contact_to_name = "";
	private $contact_to_image = "";

	private $my_uuid = "";
	public $title = "";
	public $usertyping = false;
	public $hasError = false;

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}

		$chat_id = $this->app->stickyGET('chatid');
		$this->chat_type = $chat_type = $this->app->stickyGET('chattype');
		if($chat_id){
			$this->contact_to_id = $chat_id;

			if($chat_type == "group")
				$member_model = $this->add('rakesh\apartment\Model_Group');
			else
				$member_model = $this->add('rakesh\apartment\Model_Member');

			$member_model->addCondition('apartment_id',$this->app->apartment->id)
					->addCondition('status','Active')
					->addCondition('id',$chat_id);
			$member_model->tryLoadAny();
			if(!$member_model->loaded()){
				$this->add('View_Error')->set('record not found');
				$this->hasError = true;
				return;
			} 

			if($chat_type == "group" AND !$member_model->isPermitted($this->app->apartmentmember->id)){
				$this->add('View_Error')->set('You are not found permitted');
				$this->hasError = true;
				return;	
			}
			
			$this->contact_to_image = $member_model['image']?:"websites/apartment/www/dist/img/avatar04.png";
			$this->contact_to_name = $member_model['name'];
		}else{
			$this->contact_to_id = $this->app->stickyGET('contact_to_id')?:0;
			$this->contact_to_name = $this->app->stickyGET('contact_to_name')?:"";
			$this->contact_to_image = $this->app->stickyGET('contact_to_image')?:"websites/apartment/www/dist/img/avatar04.png";
		}

		if(!$this->hasError){
			$this->addChatHistory();		
		}
	}

	function addForm(){

		$this->form = $form = $this->add('Form',null,'message_form');
		$msg_field = $form->addField('Line','message');
		$msg_field->setAttr('autofocus','autofocus')
				->setAttr('PlaceHolder','Type a message')
				->addClass('msginputbox')
				->validate('required');

		$this->send_button = $this->add('Button',null,'send_button')->set(' ')->setIcon('fa fa fa-send')->addClass('btn btn-primary');

		$msg_field->on('focus',function(){
			if($this->usertyping) return;

			$msg = [
					'message'=>"",
					'sticky'=>false,
					'desktop'=>false,
					'js'=>(string)$this->app->js()->show()->_selector('.chat-user-typing')
				];
			$ml = $this->add('rakesh\apartment\Model_MessageSent');
			$ml->pushToWebSocket([$this->contact_to_id],$msg);

			$this->usertyping = true;
		});
		$msg_field->on('focusout',function(){
			$msg = [
					'message'=>"",
					'sticky'=>false,
					'desktop'=>false,
					'js'=>(string)$this->app->js()->hide()->_selector('.chat-user-typing')
				];
			$ml = $this->add('rakesh\apartment\Model_MessageSent');
			$ml->pushToWebSocket([$this->contact_to_id],$msg);

			$this->usertyping = false;
		});

	}

	// function addMemberLister(){

	// 	$this->member_lister = $lister = $this->add('CompleteLister',null,'ap_member_list',['view\chat','ap_member_list']);
	// 	$lister->addHook('formatRow',function($l){
	// 		if($l->model['image_id']){
	// 			$l->current_row_html['profile_image'] = $l->model['image'];
	// 		}else{
	// 			$l->current_row_html['profile_image'] = 'websites/apartment/www/dist/img/avatar04.png';
	// 		}

	// 		$l->current_row_html['uuid'] = $this->app->normalizeName($this->app->apartment['name']).'_'.$this->app->apartment->id.'_'. $l->model->id;
	// 	});

	// }

	function addChatHistory(){
		// sent
		// replies
		$this->chat_history_lister = $lister = $this->add('CompleteLister',null,'ap_chat_lister',['view\chatpanel','ap_chat_lister']);

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
			$this->addForm();
			// $this->form = $form = $lister->add('Form');
			// $form->addField('Line','message');
			// $form->addSubmit('save');
		}
	}

	function recursiveRender(){
		if($this->hasError){
			$this->js('click')->univ()->redirect($this->app->url('dashboard',['mode'=>'chat']))->_selector('.backtochatmember');
			parent::recursiveRender();
			return;
		}

		// member lister
		// $member_model = $this->add('rakesh\apartment\Model_Member');
		// $member_model->addCondition('apartment_id',$this->app->apartment->id)
		// 			->addCondition('status','Active')
		// 			->addCondition('id','<>',$this->app->apartmentmember->id);
		// $this->member_lister->setModel($member_model);

		// chat history
		$chat_history_model = $this->add('rakesh\apartment\Model_MessageSent');
		$chat_history_model->addCondition('related_id',$this->app->apartment->id);

		if($this->chat_type == "group"){
			$chat_history_model->addCondition('to_id',$this->contact_to_id);
		}else{
			$chat_history_model->addCondition([['from_id',$this->app->apartmentmember->id],['to_id',$this->app->apartmentmember->id]]);
			$chat_history_model->addCondition([['from_id',$this->contact_to_id],['to_id',$this->contact_to_id]]);
		}


		$chat_history_model->setLimit(10);
		$chat_history_model->setorder('id','desc');

		$this->chat_history_lister->setModel($chat_history_model);

		// if contact is selected then updated name
		$this->chat_history_lister->template->trySet('selected_name',$this->contact_to_name?:'Chat History');
		$this->chat_history_lister->template->trySet('selected_member_img',$this->contact_to_image?:'websites/apartment/www/dist/img/avatar5.png');

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
				$this->form->js()->_selector('.msginputbox')->val(""),
				$this->chat_history_lister->js()->append($send_html)->_selector('.messages > ul'),
			];
			$this->form->js(null,$js_array)->univ()->execute();
		}

		// reload member chat

		// $js_reload = [
		// 	$this->chat_history_lister->js()->reload([
		// 		'contact_to_id'=>$this->js()->_selectorThis()->data('memberid'),
		// 		'contact_to_name'=>$this->js()->_selectorThis()->data('name'),
		// 		'contact_to_image'=>$this->js()->_selectorThis()->data('profileimage')
		// 	]),
		// 	$this->js()->removeClass('active')->_selector('#'.$this->member_lister->name.' li.active'),
		// 	$this->js()->_selectorThis()->addClass('active'),
		// ];
		// $this->member_lister->js('click',$js_reload)->_selector('li.contact');

		$this->send_button->js('click',[$this->form->js()->submit()]);
		
		$this->app->stickyForget('chatid');
		$this->js('click')->univ()->redirect($this->app->url('dashboard',['mode'=>'chat']))->_selector('.backtochatmember');
		parent::recursiveRender();
	}
	
	function defaultTemplate(){
		return ['view\chatpanel'];
	}

}