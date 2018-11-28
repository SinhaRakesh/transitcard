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
		
		$dashboard = $this->add('rakesh\apartment\View_Dashboard');
		$title = "Dashboard";
		if(!@$this->app->apartment->id){
			$dashboard->add('View_Info')->set('Your Account is created successfully, First Update Your Apartment info');
			$dashboard->add('rakesh\apartment\View_Apartment');
			return;
		}


		switch ($_GET['mode']){
			case 'apartment':
				$title = "Apartment Information";
				$dashboard->add('rakesh\apartment\View_Apartment')->addClass('card');
				break;
			case 'flat':
				$title = "Flat Management";
				$dashboard->add('rakesh\apartment\View_Flat')->addClass('card');
				break;
			case 'member':
				$title = "Member Management";
				$dashboard->add('rakesh\apartment\View_Member')->addClass('card');
				break;
			case 'invoices':
				$title = "Maintenance Amount Management";
				$dashboard->add('rakesh\apartment\View_Invoice')->addClass('card');
				break;
			default:
				if($this->app->userIsApartmentAdmin){
					$title = "Apartment Admin Dashboard";
					$dashboard->add('rakesh\apartment\View_AdminDashboard');
				}else{
					$title = "Apartment Member Dashboard";
					$dashboard->add('rakesh\apartment\View_MemberDashboard');
				}
			break;
		}

		$dashboard->template->trySet('page_title',$title);

	}
}