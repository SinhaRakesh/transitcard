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
			$view = $dashboard->add('rakesh\apartment\View_Apartment');
			return;
		}

		$menu_active = 'active_dashboard_class';
		switch ($_GET['mode']){
			case 'apartment':
				$menu_active = 'active_apartment_class';
				$title = "Apartment Settings";
				$view = $dashboard->add('rakesh\apartment\View_Apartment');
				break;
			case 'block':
				$menu_active = 'active_block_class';
				$title = "Apartment Block";
				$view = $dashboard->add('rakesh\apartment\View_Block');
				break;
			case 'blockedit':
				$menu_active = 'active_block_class';
				$title = "Edit Block";
				$view = $dashboard->add('rakesh\apartment\View_BlockEdit');
				break;

			case 'flat':
				$menu_active = 'active_flat_class';
				$title = "Flat Management";
				$view = $dashboard->add('rakesh\apartment\View_Flat');
				break;
			case 'flatedit':
				$menu_active = 'active_flat_class';
				$title = "Edit Flat";
				$view = $dashboard->add('rakesh\apartment\View_FlatEdit');
				break;

			case 'member':
				$menu_active = 'active_member_class';
				$title = "Flat Members";
				$view = $dashboard->add('rakesh\apartment\View_Member');
				break;
			case 'memberedit':
				$menu_active = 'active_member_class';
				$title = "Flat Members";
				$view = $dashboard->add('rakesh\apartment\View_MemberEdit');
				break;

			case 'invoices':
				$menu_active = 'active_invoice_class';
				$title = "Maintenance Amount Management";
				$view = $dashboard->add('rakesh\apartment\View_Invoice');
				break;

			case 'visitor':
				$menu_active = 'active_visitor_class';
				$title = "Visitor Management";
				$view = $dashboard->add('rakesh\apartment\View_Visitor');
				break;
			case 'visitoredit':
				$menu_active = 'active_visitor_class';
				$title = "Edit Visitor";
				$view = $dashboard->add('rakesh\apartment\View_VisitorEdit');
				break;
			case 'suggestion':
				$menu_active = 'active_suggestion_class';
				$title = "Suggestions Management";
				$view = $dashboard->add('rakesh\apartment\View_Suggestion');
				break;
			case 'feedback':
				$menu_active = 'active_feedback_class';
				$title = "Feedback Management";
				$view = $dashboard->add('rakesh\apartment\View_Feedback');
				break;
			case 'staff':
				$menu_active = 'active_staff_class';
				$title = "staff Management";
				$view = $dashboard->add('rakesh\apartment\View_Staff');
				break;
			case 'helpdesk':
				$menu_active = 'active_helpdesk_class';
				$title = "Help Desk";
				$view = $dashboard->add('rakesh\apartment\View_HelpDesk');
				break;
			case 'chat':
				$menu_active = 'active_chat_class';
				$title = "Chat Pannel";
				$view = $dashboard->add('rakesh\apartment\View_ChatMember');
				break;
			case 'chatpanel':
				$menu_active = 'active_chat_class';
				$title = " ";
				$view = $dashboard->add('rakesh\apartment\View_ChatPanel');
				break;

			case 'groupedit':
				$menu_active = 'active_chat_class';
				$title = "Edit Group";
				$view = $dashboard->add('rakesh\apartment\View_GroupEdit');
				break;
			
			default:
				$menu_active = 'active_dashboard_class';
				if($this->app->userIsApartmentAdmin){
					$title = "Apartment Admin Dashboard";
					$view = $dashboard->add('rakesh\apartment\View_AdminDashboard');
				}else{
					$title = "Apartment Member Dashboard";
					$view = $dashboard->add('rakesh\apartment\View_MemberDashboard');
				}
			break;
		}
		
		if(isset($view->title) && $view->title)  $title = $view->title;

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
	            <i class="fa fa-th text-green"></i> <span>Apartment Block</span>
	          </a>
	        </li>
	        <li class="{$active_member_class}">
	          <a href="?page=dashboard&mode=member">
	            <i class="fa fa-users text-green"></i> <span>Flat Members</span>
	          </a>
	        </li>
	        <li class="{$active_flat_class}">
	          <a href="?page=dashboard&mode=flat">
	            <i class="fa fa-home text-yellow"></i> <span>Flat</span>
	          </a>
	        </li>

	        <li class="{$active_visitor_class}">
	          <a href="?page=dashboard&mode=visitor">
	            <i class="fa fa-cog text-red"></i> <span>Visitors</span>
	          </a>
	        </li>
	        <li class="{$active_chat_class}">
	          <a href="?page=dashboard&mode=chat">
	            <i class="fa fa-comment text-blue"></i> <span>Chat</span>
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
	        <li class="{$active_helpdesk_class}">
	          <a href="?page=dashboard&mode=helpdesk">
	            <i class="fa fa-support text-red"></i> <span>Help Desk</span>
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


	function recursiveRender(){
		
		// regiter login customer for live chat
		$host = "ws://127.0.0.1:8890/";
		$uu_id = $this->app->normalizeName($this->app->apartment['name']).'_'.$this->app->apartment->id.'_'. $this->app->apartmentmember->id;
		$this->app->js(true)->_load('apwsclient')->univ()->runWebSocketClient($host,$uu_id);
		parent::recursiveRender();
	}
}