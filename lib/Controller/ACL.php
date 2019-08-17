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
			}elseif($this->app->userIsStaff){
				$this->acl_array = $acl_array['staff'];
			}else
				$this->acl_array = $acl_array['member'];

			if($this->hasAction()){
				$this->ACLObject->getModel()->actions = $this->acl_array['actions'];
			}

			if(!$this->canAdd()) $this->ACLObject->add_button->destroy();
			if(!$this->canEdit()){
				$this->ACLObject->grid->row_edit = false;
				$this->ACLObject->grid->removeColumn('edit');
				if(isset($this->ACLObject->custom_template)){
					$this->ACLObject->grid->addColumn('template','edit')->setTemplate(' ');
				}
			}
			if(!$this->canDelete()){
				$this->ACLObject->grid->row_delete = false;
				$this->ACLObject->grid->removeColumn('delete');
				if(isset($this->ACLObject->custom_template)){
					$this->ACLObject->grid->addColumn('template','delete')->setTemplate(' ');
				}
			} 
		}else{
			if($this->app->userIsApartmentAdmin) return;
			
			if($this->hasAction()){
				$this->ACLObject->getModel()->actions = $this->acl_array['actions'];
			}

			if(!$this->canAdd()) $this->ACLObject->add_button->destroy();
			if(!$this->canEdit()){
				$this->ACLObject->grid->row_edit = false;
				$this->ACLObject->grid->removeColumn('edit');
				if(isset($this->ACLObject->custom_template)){
					$this->ACLObject->grid->addColumn('template','edit')->setTemplate(' ');
				}
			}
			if(!$this->canDelete()){
				$this->ACLObject->grid->row_delete = false;
				$this->ACLObject->grid->removeColumn('delete');
				if(isset($this->ACLObject->custom_template)){
					$this->ACLObject->grid->addColumn('template','delete')->setTemplate(' ');
				}
			} 	
		}
	}

	function hasAction(){
		if(isset($this->acl_array['actions'])) return true;
		return false;
	}

	function canAdd(){
		if(isset($this->acl_array['add'])) return $this->acl_array['add'];
		return false;
	}

	function canEdit(){
		if(isset($this->acl_array['edit'])) return $this->acl_array['edit'];
		return false;
	}

	function canDelete(){
		if(isset($this->acl_array['delete'])) return $this->acl_array['delete'];
		return false;
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
									'admin'=>['edit'=>true,'delete'=>true,'view'=>true,'add'=>true],
									'staff'=>['edit'=>false,'delete'=>false,'view'=>true,'add'=>false]
								],
					'rakesh\apartment\Model_Flat'=>[
									'member'=>['edit'=>false,'delete'=>false,'view'=>false,'add'=>false],
									'admin'=>['edit'=>true,'delete'=>true,'view'=>true,'add'=>true],
									'staff'=>['edit'=>false,'delete'=>false,'view'=>true,'add'=>false]
								],
					'rakesh\apartment\Model_Member'=>[
									'member'=>['edit'=>false,'delete'=>false,'view'=>true,'add'=>false],
									'admin'=>['edit'=>true,'delete'=>true,'view'=>true,'add'=>true],
									'staff'=>['edit'=>false,'delete'=>false,'view'=>true,'add'=>false]
								],
					'rakesh\apartment\Model_Visitor'=>[
									'member'=>['edit'=>true,'delete'=>false,'view'=>true,'add'=>false,
										'actions'=>[
												'Requested'=>['view','permitted','denied'],
												'Permitted'=>['view'],
												'Denied'=>['view']
											]
										],
									'admin'=>['edit'=>true,'delete'=>false,'view'=>true,'add'=>true,
										'actions'=>[
													'Requested'=>['view','edit'],
													'Permitted'=>['view'],
													'Denied'=>['view','delete']
												]
										],
									'staff'=>['edit'=>true,'delete'=>false,'view'=>true,'add'=>true,
											'actions'=>[
													'Requested'=>['view','edit'],
													'Permitted'=>['view'],
													'Denied'=>['view']
												]
										],
								],
					'rakesh\apartment\Model_Category'=>[
									'member'=>['edit'=>false,'delete'=>false,'view'=>true,'add'=>false],
									'admin'=>['edit'=>true,'delete'=>true,'view'=>true,'add'=>true],
									'admin'=>['edit'=>false,'delete'=>false,'view'=>false,'add'=>false]
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
								],
					'rakesh\apartment\Model_Invoice'=>[
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
								],


				];
	}
}