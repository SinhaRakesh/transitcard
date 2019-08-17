<?php

namespace rakesh\apartment;

class Model_SalesInvoice extends \xepan\commerce\Model_SalesInvoice{
	function init(){
		parent::init();

		$this->addExpression('customer_apartment_id')->set(function($m,$q){
			$x = $m->add('rakesh\apartment\Model_Member',['table_alias'=>'flat_member']);
			return $x->addCondition('id',$m->getElement('contact_id'))
					->_dsql()
					->del('fields')
					->field('apartment_id');
		})->allowHTML(true);
	}
}