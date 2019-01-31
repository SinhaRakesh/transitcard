<?php

namespace rakesh\apartment;

class Controller_ACL extends \AbstractController {
	public $acl_value = [];

	public $ACLObject;
	public $model;
	public $model_class_name;
	public $model_ns;
	public $acl_array;

	function init(){
		parent::init();

		$this->setACLValue();
		if(!$this->ACLObject) $this->ACLObject = $this->owner;

		$this->model = $model = $this->ACLObject->getModel();
		$this->model_class = new \ReflectionClass($this->model);
		$this->model_class_name = $this->model_class->name;
		$this->model_ns = $this->model->namespace?:$this->model_class->getNamespaceName();


		// apply ACL here

		if(isset($this->acl_value[$this->model_class_name])){
			$acl_array = $this->acl_value[$this->model_class_name];

			if($this->app->userIsApartmentAdmin){
				$this->acl_array = $acl_array['admin'];
			}else
				$this->acl_array = $acl_array['member'];

			if($this->hasAction()){
				$this->ACLObject->getModel()->actions = $this->acl_array['actions'];
			}

			if(!$this->canAdd()) $this->ACLObject->add_button->destroy();
			if(!$this->canEdit()) $this->ACLObject->grid->removeColumn('edit');
			if(!$this->canDelete()) $this->ACLObject->grid->removeColumn('delete');
		}
	}

	function hasAction(){
		if(isset($this->acl_array['actions'])) return true;
		return false;
	}

	function canAdd(){
		if(isset($this->acl_array['add'])) return $this->acl_array['add'];
	}

	function canEdit(){
		if(isset($this->acl_array['edit'])) return $this->acl_array['edit'];
	}

	function canDelete(){
		if(isset($this->acl_array['delete'])) return $this->acl_array['delete'];
	}

	/*
	member = [
			'member'=>['edit'=>yes,'delete'=>yes,'view'=>yes],
			'apartment-admin'=>['edit'=>yes,'delete'=>yes,'view'=>yes]
		]
	*/
	function setACLValue(){
		$this->acl_value = [
					'rakesh\apartment\Model_Block'=>[
									'member'=>['edit'=>false,'delete'=>false,'view'=>false,'add'=>false],
									'admin'=>['edit'=>true,'delete'=>true,'view'=>true,'add'=>true]
								],
					'rakesh\apartment\Model_Flat'=>[
									'member'=>['edit'=>false,'delete'=>false,'view'=>false,'add'=>false],
									'admin'=>['edit'=>true,'delete'=>true,'view'=>true,'add'=>true]
								],
					'rakesh\apartment\Model_Member'=>[
									'member'=>['edit'=>false,'delete'=>false,'view'=>false,'add'=>false],
									'admin'=>['edit'=>true,'delete'=>true,'view'=>true,'add'=>true]	
								],
					'rakesh\apartment\Model_Visitor'=>[
									'member'=>['edit'=>true,'delete'=>false,'view'=>true,'add'=>true],
									'admin'=>['edit'=>true,'delete'=>false,'view'=>true,'add'=>true]
								],
					'rakesh\apartment\Model_Category'=>[
									'member'=>['edit'=>false,'delete'=>false,'view'=>true,'add'=>false],
									'admin'=>['edit'=>true,'delete'=>true,'view'=>true,'add'=>true]
								],
					'rakesh\apartment\Model_Affiliate'=>[
									'member'=>['edit'=>false,'delete'=>false,'view'=>true,'add'=>false],
									'admin'=>['edit'=>true,'delete'=>true,'view'=>true,'add'=>true]
								],
					'rakesh\apartment\Model_NoticeBoard'=>[
									'member'=>['edit'=>false,'delete'=>false,'view'=>true,'add'=>false],
									'admin'=>['edit'=>true,'delete'=>true,'view'=>true,'add'=>true]
								],
					'rakesh\apartment\Model_Expenses'=>[
									'member'=>[
											'edit'=>false,'delete'=>false,'view'=>true,'add'=>false,
											'actions'=>[
													'Paid'=>['view'],
													'Due'=>['view']
												],
										],
									'admin'=>[
										'edit'=>true,'delete'=>true,'view'=>true,'add'=>true,
										'actions'=>[
													'Paid'=>['view','edit','delete'],
													'Due'=>['view','paid','edit','delete']
												],
										]
								]

				];
	}
}