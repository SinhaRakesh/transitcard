<?php

namespace rakesh\apartment;

class View_Feedback extends \View{

	public $options = [];

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}
		
		$v = $this->add('View_Info')->addClass('callout callout-info');
		$v->add('H4')->set('Todo Feedback, we are working on it');

	}
}