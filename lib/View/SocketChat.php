<?php

namespace rakesh\apartment;

class View_SocketChat extends \View{
	public $options = [];
	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}

		$this->default_img_url = 'websites/'.$this->app->current_website_name.'/www/dist/img/avatar04.png';

	}
	
	function defaultTemplate(){
		return ['view\socket'];
	}

}