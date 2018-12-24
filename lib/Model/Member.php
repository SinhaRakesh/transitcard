<?php

namespace rakesh\apartment;

class Model_Member extends \rakesh\apartment\Model_MemberAbstract{

	function init(){
		parent::init();

		$this->addCondition([['is_group',false],['is_group',null]]);
	}

	function getPermittedGroups(){
		$associated_group = $this->add('rakesh\apartment\Model_GroupMemberAssociation')
					->addCondition('member_id',$this->id)
					->addCondition('apartment_id',$this->app->apartment->id)
					->_dsql()->del('fields')->field('group_id')->getAll();

		$data = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($associated_group)),false);
		return array_combine($data, $data);
	}

	function isPermitted($group_id){
		return $this->add('rakesh\apartment\Model_GroupMemberAssociation')
					->addCondition('member_id',$this->id)
					->addCondition('apartment_id',$this->app->apartment->id)
					->addCondition('group_id',$group_id)
					->count()
					->getOne();


	}
}