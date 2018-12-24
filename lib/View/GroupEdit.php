<?php

namespace rakesh\apartment;

class View_GroupEdit extends \View{

	public $options = [];
	public $title = "";
	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update partment data');
			return;
		}


		$group_id = $this->app->stickyGET('contact_id');

		$model = $this->add('rakesh\apartment\Model_Group');
		$model->addCondition('apartment_id',@$this->app->apartment->id);
		if($group_id){
			$model->addCondition('id',$group_id);
			$model->tryLoadAny();
			if(!$model->loaded()) throw new \Exception("group not found");
			
			$this->app->template->trySet('page_title','Edit Group');
			$this->title = 'Edit Group';
		}else{
			$this->app->template->trySet('page_title','Add New Group');
			$this->title = 'Add New Group';
		}

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->addContentSpot()
			->layout([
				'first_name~Group Name'=>'c1~8',
				'FormButtons~&nbsp;'=>'c2~4',
				'members~'=>'c2~12'
			]);
		$form->setModel($model,['first_name','status']);
		$member_asso_field = $form->addField('hidden','members');

		if($model->loaded()){
			$member_asso_field->set(json_encode($model->getAssociatedMembers()));
		}

		$grid = $form->add('xepan\base\Grid');
		$grid->add('View',null,'Pannel')->setElement('h3')->set('Select group members');
		$grid->noSno();

		$member_model = $this->add('rakesh\apartment\Model_Member');
		$member_model->addCondition('apartment_id',$this->app->apartment->id)
			->addCondition('status','Active');
		$grid->setModel($member_model,['name']);
		$grid->addSelectable($member_asso_field);

		$form->addSubmit('save')->addClass('btn btn-primary');
		if($form->isSubmitted()){
			
			$form->save();

			$data = $this->add('rakesh\apartment\Model_GroupMemberAssociation')
					->addCondition('group_id',$form->model->id)
					->addCondition('apartment_id',$this->app->apartment->id)
					->_dsql()->del('fields')->field('id')->getAll();

			$saved_record_ids = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($data)),false);
			$saved_record_ids = array_combine($saved_record_ids,$saved_record_ids);
			
			$new_saved = [];
			$selected_members = array();
			$selected_members = json_decode($form['members'],true);
			foreach ($selected_members as $m_id) {
				$model_asso = $this->add('rakesh\apartment\Model_GroupMemberAssociation');
				$model_asso->addCondition('group_id',$form->model->id);
				$model_asso->addCondition('member_id',$m_id);
				$model_asso->addCondition('apartment_id',$this->app->apartment->id);
				$model_asso->tryLoadAny();
				$model_asso->save();

				$new_saved[$model_asso->id] = $model_asso->id;
			}

			$to_delete_ids = array_diff($saved_record_ids, $new_saved);

			if(count($to_delete_ids)){
				$this->add('rakesh\apartment\Model_GroupMemberAssociation')
					->addCondition('group_id',$form->model->id)
					->addCondition('apartment_id',$this->app->apartment->id)
					->addCondition('id',$to_delete_ids)
					->deleteAll();
			}

			$this->app->stickyForget('contact_id');
			$this->app->redirect($this->app->url('dashboard',['mode'=>'chat','active'=>'group']));
		}

	}
}