<?php

namespace rakesh\apartment;

class View_Invoice extends \View{

	public $options = [];

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}

		$this->app->template->trySet('page_title',"Invoice Management");

		$inv = $this->add('rakesh\apartment\Model_Invoice');
		$inv->addCondition('customer_apartment_id',$this->app->apartment->id);

		$grid = $this->add('xepan\base\Grid');
		$grid->setModel($inv,['contact','due_date','created_at','net_amount','status']);

	}
}