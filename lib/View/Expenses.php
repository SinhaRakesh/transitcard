<?php

namespace rakesh\apartment;

class View_Expenses extends \View{

	public $options = [];

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}

		$status = $this->app->stickyGET('status');

		$model_ex = $this->add('rakesh\apartment\Model_Expenses');
		$model_ex->addCondition('apartment_id',$this->app->apartment->id);
		$counts = $model_ex->_dsql()->del('fields')->field('status')->field('sum(amount) counts')->group('Status')->get();
		$counts_redefined = [];
		foreach ($counts as $cnt) {
			$counts_redefined[$cnt['status']] = $cnt['counts'];
			$counts_redefined['All'] += $cnt['counts'];
		}

		$model = $this->add('rakesh\apartment\Model_Expenses');
		$model->addCondition('apartment_id',@$this->app->apartment->id);
		if($status == "Paid" OR $status == "Due"){
			$model->addCondition('status',$status);
		}

		$crud = $this->add('xepan\hr\CRUD',
			[
				'actionsWithoutACL'=>true,
				'edit_page'=>$this->app->url('dashboard',['mode'=>'exedit']),
				'action_page'=>$this->app->url('dashboard',['mode'=>'exedit'])
			]);
		$crud->grid->js(true)->find('.main-box-body')->addClass('table-responsive');

		$crud->grid->addHook('formatRow',function($g){
			$g->current_row_html['name'] = $g->model['name'].
				"<br/>".$g->model['created_at'].
				"<br/>".$g->model['expenses_narration'];
			
			$g->current_row_html['payment_type'] = $g->model['payment_type']."<br/> On Date: ".$g->model['paid_at']."<br/>".$g->model['payment_narration'];
		});
		$crud->setModel($model,['name','created_at','updated_at','paid_at','status','payment_type','payment_narration','expenses_narration','net_amount']);
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(15);
		// $crud->grid->addColumn('action');
		$crud->grid->removeColumn('edit');
		$crud->grid->removeColumn('delete');
		$crud->grid->removeColumn('expenses_narration');
		$crud->grid->removeColumn('payment_narration');
		$crud->grid->removeColumn('updated_at');
		$crud->grid->removeColumn('paid_at');
		$crud->grid->removeColumn('status');
		$crud->grid->removeColumn('created_at');
		$crud->grid->removeAttachment();


		// $crud->add('rakesh\apartment\Controller_ACL');
		$btn_set = $crud->grid->add('ButtonSet',null,'grid_heading_left')->addClass('btn-group');
		$all_btn = $btn_set->addButton('All')->addClass('btn btn-primary')->set('Total Amount: '.$counts_redefined['All']);
		$paid_btn = $btn_set->addButton('Paid')->addClass('btn btn-success')->set('Paid: '.$counts_redefined['Paid']);
		$due_btn = $btn_set->addButton('Due')->addClass('btn btn-danger')->set('Due: '.$counts_redefined['Due']);

		$paid_btn->js('click',$crud->js()->univ()->reload(['status'=>'Paid']));
		$all_btn->js('click',$crud->js()->univ()->reload(['status'=>'All']));
		$due_btn->js('click',$crud->js()->univ()->reload(['status'=>'Due']));
		
	}
}