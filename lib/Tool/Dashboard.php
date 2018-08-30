<?php

namespace rakesh\apartment;

class Tool_Dashboard extends \xepan\cms\View_Tool{

	public $options = [];

	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController){
			$this->add('View_Info')->set('dashboard tool, reload');
			return;
		}

		if(!@$this->app->apartment->id){
			$this->add('View_Info')->set('First Update Your Apartment info');
			$this->add('rakesh\apartment\View_Apartment');
		}

		$this->add('rakesh\apartment\View_Flat');

	}
}