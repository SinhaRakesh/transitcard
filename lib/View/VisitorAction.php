<?php

namespace rakesh\apartment;

class View_VisitorAction extends \View{
	
	public $options = [];
	public $title = "Visitor Action";

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}
			
	}
}