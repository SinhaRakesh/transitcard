<?php

namespace rakesh\apartment;

class View_AdminDashboard extends \View{

	public $options = [];

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}

		$this->app->template->trySet('page_title',$this->app->apartment['name'].' Admin Dashboard');

		$pay_now = $this->add('rakesh\apartment\View_PayNow');

		$view_admin_amount = $this->add('View');
		$col = $view_admin_amount->add('Columns');
		$col1 = $col->addColumn('3');
		$col2 = $col->addColumn('3');
		$col3 = $col->addColumn('3');
		$col4 = $col->addColumn('3');
		$col1->add('View')->setHtml('<h3 class="card-title">Received<br/><small>17,000</small></h3><br/>5-Flats')->addClass('card card-stats');
		$col2->add('View')->setHtml('<h3 class="card-title">Due<br/><small>17,000</small></h3><br/>5-Flats')->addClass('card card-stats');
		$col3->add('View')->setHtml('<h3 class="card-title">Expences<br/><small>17,000</small></h3><br/>5-Flats')->addClass('card card-stats');
		$col4->add('View')->setHtml('<h3 class="card-title">Balance Amount<br/><small>17,000</small></h3><br/>5-Flats')->addClass('card card-stats');

	}
}