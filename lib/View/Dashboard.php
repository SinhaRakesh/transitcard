<?php

namespace rakesh\apartment;

class View_Dashboard extends \View{

	function init(){
		parent::init();
		
		$this->template->trySet('member_name',$this->app->apartmentmember['name']);
		$this->template->trySet('apartment_name',$this->app->apartment['name']);
		$this->template->trySet('profile_image',($this->app->apartmentmember['image']?:"websites/".$this->app->current_website_name."/www/dist/img/avatar04.png"));

		if($this->app->apartment['is_flat_owner']){
			// $this->template->trySet('relation_with_owner',"Family Head");
		}else{
			// $this->template->trySet('relation_with_owner',"Family Member");
		}

		// $this->template->trySet('page_title',$this->app->page_name);
		$this->template->trySet('website_name',$this->app->current_website_name);

	}

	function defaultTemplate(){
		return ['view/apdashboard'];
	}
}