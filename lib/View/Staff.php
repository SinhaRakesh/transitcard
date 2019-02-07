<?php

namespace rakesh\apartment;

class View_Staff extends \View{

	public $options = [];

	function init(){
		parent::init();
		
		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}

		$model = $this->add('rakesh\apartment\Model_Staff');
		$crud = $this->add('xepan\base\CRUD',
				[
					'edit_page'=>$this->app->url('dashboard',['mode'=>'staffedit']),
					'action_page'=>$this->app->url('dashboard',['mode'=>'staffedit']),
					'custom_template'=>true
				],
				null,
				['view/stafflister']
			);

		$crud->grid->addHook('formatRow',function($g){

			if($g->model['image_id'])
				$g->current_row_html['profile_image'] = $g->model['image'];
			else
				$g->current_row_html['profile_image'] = 'websites/'.$this->app->current_website_name.'/www/dist/img/avatar04.png';

			if($g->model['status'] == "Active"){
				$g->current_row_html['status_class'] = 'label-success';
			}else{
				$g->current_row_html['status_class'] = 'label-danger';
			}

			$color = ['bg-yellow','bg-blue','bg-info','bg-success','bg-green','bg-red','bg-gray','bg-orange','bg-warning'];
			$g->current_row['bg_color'] = $color[rand(0,8)];
		});

		$crud->setModel($model);
		$crud->grid->addQuickSearch(['name','email_id_1','email_id_2','mobile_no_1','mobile_no_2','aadhar_card_no','pan_card_number','police_verification_number']);
		$crud->grid->addColumn('edit');
		$crud->grid->addColumn('delete');
		$crud->grid->addPaginator(15);
		$crud->add('rakesh\apartment\Controller_ACL');

	}
}