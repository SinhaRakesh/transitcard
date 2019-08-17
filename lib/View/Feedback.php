<?php

namespace rakesh\apartment;

class View_Feedback extends \View{

	public $options = [];

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}
		

		$model = $this->add('rakesh\apartment\Model_Feedback');
		$model->addCondition('from_id',$this->app->apartmentmember->id);
		$model->addCondition('related_id',$this->app->apartment->id);
		$model->addCondition('created_by_id',$this->app->apartmentmember->id);
		$model->addCondition('direction','Out');

		$model->getElement('title');
		$model->getElement('description');

		$column = $this->add('Columns');
		$col1 = $column->addColumn(4);
		$col2 = $column->addColumn(8);

		$v = $col1->add('View_Info')->addClass('callout callout-warning');
		$v->add('H4')->set('Add Your Feedback');

		$form = $col1->add('Form');
		$form->setModel($model,['title','description']);
		$form->addSubmit('Submit Your Feedback')->addClass('btn btn-warning');
	
		$v = $col2->add('View_Info')->addClass('callout callout-info');
		$v->add('H4')->set('Your Feedback History');
		$grid = $col2->add('Grid');

		if($form->isSubmitted()){
			$form->save();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Thank you for your suggestion')->execute();
		}

		$model->setOrder('id','desc');
		$grid->setModel($model,['title','description','created_at']);
		$grid->template->tryDel('Pannel');
		$grid->addPaginator(10);

	}
}