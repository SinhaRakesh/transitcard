<?php

namespace rakesh\apartment;

class Model_NoticeBoard extends \xepan\communication\Model_Communication{

	public $acl_type = "ApartmentCommunication";

	function init(){
		parent::init();

		$this->addCondition('communication_type','NoticeBoard');
		$this->addCondition('direction','Out');
		$this->getElement('status')->defaultValue('Draft');

		$this->getElement('related_id')->defaultValue($this->app->apartment->id);

		$this->getElement('tags')->caption('Valid Till Date');
		$this->getElement('flags')->caption('Display From Date');
	}
}