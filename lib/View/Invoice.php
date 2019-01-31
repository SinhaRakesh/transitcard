<?php

namespace rakesh\apartment;

class View_Invoice extends \View{

	public $options = [];

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}

		$inv = $this->add('rakesh\apartment\Model_Invoice');
		$inv->addCondition('apartment_id',$this->app->apartment->id);
		$inv->getElement('created_at')->caption('Bill Month');
		$inv->getElement('payment_narration')->caption('Payment Detail');

		$crud = $this->add('xepan\hr\CRUD',['actionsWithoutACL'=>true,'entity_name'=>' Maintenance Bill']);
		$crud->grid->js(true)->find('.main-box-body')->addClass('table-responsive');
		$crud->grid->addHook('formatRow',function($g){

			$payment_html = 'Payment Mode: '.$g->model['payment_type'].
							"<br/>Paid By: ".$g->model['paid_by'].
							"<br/>Paid On: ".$g->model['paid_at'].
							"<br/>".$g->model['payment_narration']
							;

			$name = $g->model['name'].
					"<br/>".$g->model['member']." ".$g->model['flat'].
					"<br/>Bill Month: ".date('M-Y',strtotime($g->model['created_at']))
					;

			$exhtml = '<div class="box collapsed-box" style="border:0px;box-shadow:none;">
					  <div class="">
					      <button type="button" class="btn" data-widget="collapse" style="background-color:white;padding:0px;margin:0px;">
					    	<p class="box-title">'.$name.'</p>
					      </button>
					  </div>
					  <div class="box-body" style="display: none;">
					    '.$payment_html.'
					  </div>
					</div>';


			$g->current_row_html['name'] = $exhtml;
		});

		$crud->setModel($inv,['member','name','flat','created_at','net_amount','status','paid_by','paid_at','payment_type','payment_narration']);

		$crud->grid->addQuickSearch(['name','member','flat','status']);
		$crud->grid->removeColumn('edit');
		$crud->grid->removeColumn('delete');
		$crud->grid->removeColumn('status');
		$crud->grid->removeColumn('member');
		$crud->grid->removeColumn('flat');
		$crud->grid->removeColumn('payment_type');
		$crud->grid->removeColumn('paid_at');
		$crud->grid->removeColumn('paid_by');
		$crud->grid->removeColumn('created_at');
		$crud->grid->removeColumn('payment_narration');
		$crud->grid->removeColumn('attachment_icon');
		$crud->grid->addFormatter('name','wrap');

	}
}