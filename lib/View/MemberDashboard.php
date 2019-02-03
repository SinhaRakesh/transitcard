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

		// $this->app->template->trySet('page_title',$this->app->apartment['name'].' Member Dashboard');
				
		$this->add('rakesh\apartment\View_QuickLink');
		// $this->add('rakesh\apartment\View_PayNow');
		$col = $this->add('Columns')->addClass("row");
		$col1 =	$col->addColumn(6)->addClass('col-md-6');
		$col2 = $col->addColumn(6)->addClass('col-md-6');
		$col1->add('rakesh\apartment\View_Dashbord_Bill');
		$col2->add('rakesh\apartment\View_NoticeBoard');
	}
}