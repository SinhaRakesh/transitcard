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

		$menu_active = 'active_dashboard_class';
		switch ($_GET['mode']){
			case 'apartment':
				$menu_active = 'active_apartment_class';
				$title = "Apartment Settings";
				$dashboard->add('rakesh\apartment\View_Apartment');
				break;
			case 'block':
				$menu_active = 'active_block_class';
				$title = "Apartment Block Management";
				$dashboard->add('rakesh\apartment\View_Block');
				break;
			case 'flat':
				$menu_active = 'active_flat_class';
				$title = "Flat Management";
				$dashboard->add('rakesh\apartment\View_Flat');
				break;
			case 'member':
				$menu_active = 'active_member_class';
				$title = "Member Management";
				$dashboard->add('rakesh\apartment\View_Member');
				break;
			case 'invoices':
				$menu_active = 'active_invoice_class';
				$title = "Maintenance Amount Management";
				$dashboard->add('rakesh\apartment\View_Invoice');
				break;
			case 'visitor':
				$menu_active = 'active_visitor_class';
				$title = "Visitor Management";
				$dashboard->add('rakesh\apartment\View_Visitor');
				break;
			case 'suggestion':
				$menu_active = 'active_suggestion_class';
				$title = "Suggestions Management";
				$dashboard->add('rakesh\apartment\View_Suggestion');
				break;
			case 'feedback':
				$menu_active = 'active_feedback_class';
				$title = "Feedback Management";
				$dashboard->add('rakesh\apartment\View_Feedback');
				break;
			case 'staff':
				$menu_active = 'active_staff_class';
				$title = "staff Management";
				$dashboard->add('rakesh\apartment\View_Staff');
				break;
			default:
				$menu_active = 'active_dashboard_class';
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

		$menu_html = '
			<li class="{$active_dashboard_class}">
	          <a href="?page=dashboard">
	            <i class="fa fa-dashboard text-info"></i> <span>Dashboard</span>
	          </a>
	        </li>
	        <li class="{$active_apartment_class}">
	          <a href="?page=dashboard&mode=apartment">
	            <i class="fa fa-cog text-red"></i> <span>Apartment Settings</span>
	          </a>
	        </li>
	        <li class="{$active_block_class}">
	          <a href="?page=dashboard&mode=block">
	            <i class="fa fa-users text-green"></i> <span>Apartment Block</span>
	          </a>
	        </li>
	        <li class="{$active_member_class}">
	          <a href="?page=dashboard&mode=member">
	            <i class="fa fa-users text-green"></i> <span>Flat Members</span>
	          </a>
	        </li>
	        <li class="{$active_flat_class}">
	          <a href="?page=dashboard&mode=flat">
	            <i class="fa fa-th text-yellow"></i> <span>Flat</span>
	          </a>
	        </li>

	        <li class="{$active_visitor_class}">
	          <a href="?page=dashboard&mode=visitor">
	            <i class="fa fa-cog text-red"></i> <span>Visitors</span>
	          </a>
	        </li>
	        <li class="{$active_suggestion_class}">
	          <a href="?page=dashboard&mode=suggestion">
	            <i class="fa fa-cog text-red"></i> <span>Suggestions</span>
	            <span class="pull-right-container">
	              <small class="label pull-right bg-yellow">12</small>
	            </span>
	          </a>
	        </li>
	        <li class="{$active_feedback_class}">
	          <a href="?page=dashboard&mode=feedback">
	            <i class="fa fa-cog text-red"></i> <span>Feedback</span>
	            <span class="pull-right-container">
	              <small class="label pull-right bg-yellow" title="new Feedback">12</small>
	            </span>
	          </a>
	        </li>

	        <li class="{$active_staff_class}">
	          <a href="?page=dashboard&mode=staff">
	            <i class="fa fa-cog text-red"></i> <span>Staff</span>
	            <span class="pull-right-container">
	              <small class="label pull-right bg-yellow"></small>
	            </span>
	          </a>
	        </li>
	    ';

	    $menu_html = str_replace('{$'.$menu_active.'}', 'active', $menu_html);
		$dashboard->template->trySetHtml('sidebar_menu',$menu_html);
	}
}