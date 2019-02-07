<?php

namespace rakesh\apartment;

class View_QuickLink extends \View{

	public $options = [];

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}
		
		$html = '<div class="box">
            <div class="box-body">
            	<div class="row no-margin no-padding">
            		<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6">
            			<a href="?page=dashboard&mode=complain" class="btn btn-app bg-yellow btn-block boxshadow">
                			<i class="fa fa-edit"></i> Complain
              			</a>
            		</div>
            		<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6">
		              <a href="?page=dashboard&mode=visitor" class="btn btn-app bg-green btn-block boxshadow" >
		                <i class="fa fa-users"></i> Invite Visitors
		              </a>
            		</div>
            		<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6" >
			            <a href="?page=dashboard&mode=helpdesk" class="btn btn-app bg-purple btn-block">
			            	<i class="glyphicon glyphicon-headphones"></i> Help Desk
			            </a>
            		</div>';
            		
		if(!$this->app->userIsStaff){
			$html .= '<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6">
		              <a href="?page=dashboard&mode=chat" class="btn btn-app bg-maroon btn-block" >
		                <i class="fa fa-comments-o"></i> Communication
		              </a>
            		</div>';
		}
		
        $html .= '</div>
            </div>
          </div>';

		$this->add('View')->setHtml($html);
		
	}
}