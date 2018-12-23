<?php

namespace rakesh\apartment;

class View_ChatMember extends \View{

	public $options = [];
	public $member_lister;
	public $title="Apartment Member/Groups";
	function init(){
		parent::init();
		
		// if(!@$this->app->apartment->id){
		// 	$this->add('View_Error')->set('first update partment data');
		// 	return;
		// }

		$this->tab = $this->add('Tabs');
		// $group_tab = $tab->addTab('Groups');

		$this->addMemberLister();
		$this->addGroupLister();
	}

	function addGroupLister(){

		$group_tab = $this->tab->addTab('Groups');

		$this->group_lister = $lister = $group_tab->add('xepan\base\Grid',null,null,['view/chatmember']);
		$lister->addHook('formatRow',function($l){
			if($l->model['image_id']){
				$l->current_row_html['profile_image'] = $l->model['image'];
			}else{
				$l->current_row_html['profile_image'] = 'websites/apartment/www/dist/img/avatar04.png';
			}

			$l->current_row_html['uuid'] = $this->app->normalizeName($this->app->apartment['name']).'_'.$this->app->apartment->id.'_'. $l->model->id;
			$l->current_row_html['chaturl'] = $this->app->url('dashboard',['mode'=>'chatpanel','chatid'=>$l->model->id]);
		});

		$group_model = $this->add('rakesh\apartment\Model_Member');
		$group_model->addCondition('apartment_id',$this->app->apartment->id)
					->addCondition('status','Active')
					->addCondition('id','<>',$this->app->apartmentmember->id);

		$this->group_lister->setModel($group_model);
		$this->group_lister->addQuickSearch(['name']);
		$this->group_lister->addPaginator(25);

	}

	function addMemberLister(){

		$member_tab = $this->tab->addTab('Members');

		$this->member_lister = $lister = $member_tab->add('xepan\base\Grid',null,null,['view/chatmember']);
		$lister->addHook('formatRow',function($l){
			if($l->model['image_id']){
				$l->current_row_html['profile_image'] = $l->model['image'];
			}else{
				$l->current_row_html['profile_image'] = 'websites/apartment/www/dist/img/avatar04.png';
			}

			$l->current_row_html['uuid'] = $this->app->normalizeName($this->app->apartment['name']).'_'.$this->app->apartment->id.'_'. $l->model->id;
			$l->current_row_html['chaturl'] = $this->app->url('dashboard',['mode'=>'chatpanel','chatid'=>$l->model->id]);
		});

		$member_model = $this->add('rakesh\apartment\Model_Member');
		$member_model->addCondition('apartment_id',$this->app->apartment->id)
					->addCondition('status','Active')
					->addCondition('id','<>',$this->app->apartmentmember->id);

		$this->member_lister->setModel($member_model);
		$this->member_lister->addQuickSearch(['name']);

		$this->member_lister->addPaginator(25);

	}

	function recursiveRender(){


		// // reload member chat
		// $js_reload = [
		// 	$this->chat_history_lister->js()->reload([
		// 		'contact_to_id'=>$this->js()->_selectorThis()->data('memberid'),
		// 		'contact_to_name'=>$this->js()->_selectorThis()->data('name'),
		// 		'contact_to_image'=>$this->js()->_selectorThis()->data('profileimage')
		// 	]),
		// 	$this->js()->removeClass('active')->_selector('#'.$this->member_lister->name.' li.active'),
		// 	$this->js()->_selectorThis()->addClass('active'),
		// ];
		// // // regiter login customer for live chat
		// // $host = "ws://127.0.0.1:8890/";
		// // $uu_id = $this->app->normalizeName($this->app->apartment['name']).'_'.$this->app->apartment->id.'_'. $this->app->apartmentmember->id;
		// // $this->app->js(true)->_load('wsclient')->univ()->runWebSocketClient($host,$uu_id);

		parent::recursiveRender();

	}
}