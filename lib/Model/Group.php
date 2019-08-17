<?php

namespace rakesh\apartment;

class Model_Group extends \rakesh\apartment\Model_MemberAbstract{

	public $contact_type = "Group";

	function init(){
		parent::init();

		$this->addCondition('is_group',true);

		$this->hasMany('rakesh\apartment\GroupMemberAssociation','group_id',null,'GroupMemberAssociation');

	}

	function getAssociatedMembers(){

		$associated_member = $this->add('rakesh\apartment\Model_GroupMemberAssociation')
					->addCondition('group_id',$this->id)
					->addCondition('apartment_id',$this->app->apartment->id)
					->_dsql()->del('fields')->field('member_id')->getAll();

		return iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($associated_member)),false);
	}


	function isPermitted($member_id){
		return $this->add('rakesh\apartment\Model_GroupMemberAssociation')
					->addCondition('group_id',$this->id)
					->addCondition('member_id',$member_id)
					->addCondition('apartment_id',$this->app->apartment->id)
					->count()
					->getOne();


	}
}