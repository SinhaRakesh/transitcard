<?php

namespace rakesh\apartment;

class page_cron_billgenerator extends \Page{

	public $title = "Bill Generator";

	function init(){
		parent::init();

		ini_set("memory_limit", "-1");
   		set_time_limit(0);
   		
		$this->add('rakesh\apartment\Controller_APTBillGeneration')->run();
	}
}