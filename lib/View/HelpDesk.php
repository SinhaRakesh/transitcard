<?php

namespace rakesh\apartment;

class View_HelpDesk extends \View{

	public $options = [];

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}

		$col = $this->add('Columns');
		$col1 = $col->addColumn('4');
		$col2 = $col->addColumn('8');

		$model = $this->add('rakesh\apartment\Model_Category');
		$model->addCondition('apartment_id',$this->app->apartment->id);

		$view = $col1->add('CRUD');
		$view->setModel($model,['name']);

		$affiliate = $this->add('rakesh\apartment\Model_Affiliate');
		$aff = $col2->add('CRUD');
		$aff->setModel($affiliate,
				['category_id','name','contact_no','email_id','address','narration','status'],
				['category','name','contact_no','email_id','address','narration','status']
			);


	}
}