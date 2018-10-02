<?php

namespace rakesh\apartment;

class View_MemberDashboard extends \View{

	public $options = [];

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}

		$this->app->template->trySet('page_title',$this->app->apartment['name'].' Member Dashboard');

	}
}