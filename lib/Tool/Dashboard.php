<?php

namespace rakesh\apartment;

class Tool_Dashboard extends \xepan\cms\View_Tool{

	public $options = [];

	function init(){
		parent::init();

		$this->app->stickyGET('mode');
		// $menu = $this->add('Menu');
		// $menu->addMenuItem($this->app->url(null,['mode'=>'apartment']),'Apartment');
		// $menu->addMenuItem($this->app->url(null,['mode'=>'flat']),'Flat');

		if($this->owner instanceof \AbstractController){
			$this->add('View_Info')->set('dashboard tool, reload');
			return;
		}
		
		if(!@$this->app->apartment->id){
			$this->add('View_Info')->set('First Update Your Apartment info');
			$this->add('rakesh\apartment\View_Apartment');
		}

		switch ($_GET['mode']){
			case 'apartment':
				$this->add('rakesh\apartment\View_Apartment')->addClass('card');
				break;
			case 'flat':
				$this->add('rakesh\apartment\View_Flat')->addClass('card');
				break;
			case 'member':
				$this->add('rakesh\apartment\View_Member')->addClass('card');
				break;
			case 'invoices':
				$this->add('rakesh\apartment\View_Invoice')->addClass('card');
				break;
			default:
				if($this->app->userIsApartmentAdmin){
					$this->add('rakesh\apartment\View_AdminDashboard');
				}else{
					$this->add('rakesh\apartment\View_MemberDashboard');
				}
			break;
		}

	}
}