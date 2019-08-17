<?php

namespace rakesh\apartment;

class View_PayNow extends \View{

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update partment data');
			return;
		}
		
		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->makePanelsCoppalsible(true)
			->addContentSpot()
			->layout([
				'payment_due~Due Amount'=>'Pay Your Due 6,000~c1~8~closed',
				'FormButtons~&nbsp;'=>'c2~4',
			]);
		$form->addField('Line','payment_due')->setAttr('disabled',true)->validate('required')->set('6,000');
		$form->addSubmit('Pay Now')->addClass('btn btn-primary btn-block');
		
	}
}