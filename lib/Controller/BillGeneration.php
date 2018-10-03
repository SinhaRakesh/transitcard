<?php

namespace rakesh\apartment;

class Controller_BillGeneration extends \AbstractController {
	public $for_apartment = null;
	public $debug = false;
	function setApartment($apartment_id){
		if($apartment_id) $this->for_apartment = $apartment_id;
	}

	function run(){

		$apartment = $this->add('rakesh\apartment\Model_Apartment');
		if($this->for_apartment)
			$apartment->addCondition('id',$this->for_apartment);

		$today_date = date('d',strtotime($this->app->today));
		$current_month_year = date('m-Y',strtotime($this->app->today));
		
		$apartment->addCondition('bill_generation_date',$today_date);

		if($this->debug)
			$this->owner->add('Grid')->setModel($apartment);

		$item_model = $this->add('xepan\commerce\Model_Item');
		$item_model->addCondition('sku','APT-MAINTENANCE');
		$item_model->tryLoadAny();
		if(!$item_model->loaded()){
			throw new \Exception("First Add an item of sku (APT-MAINTENANCE)");
		}

		$default_nominal_id = $this->add('xepan\accounts\Model_Ledger')->load('Sales Account')->get('id');
		$default_currency_id = $this->add('xepan\accounts\Model_Currency')->addCondition('status','Active')->tryLoadAny()->id;
				
		$qsp_array = [];
		foreach($apartment as $ap_model){
			$flats = $this->add('rakesh\apartment\Model_Flat');
			$flats->addExpression('last_invoice_month')->set(function($m,$q){
				$saleinv = $m->add('xepan\commerce\Model_SalesInvoice');
				$saleinv->addCondition('contact_id',$m->getElement('member_id'));
				$saleinv->setOrder('id','desc');
				$saleinv->setLimit(1);
				return $q->expr('concat(MONTH([0]),"-",year([0]))',[$saleinv->fieldQuery('created_at')]);
			});

			$flats->addExpression('b_country_id')->set($flats->refSQL('member_id')->fieldQuery('country_id'));
			$flats->addExpression('b_state_id')->set($flats->refSQL('member_id')->fieldQuery('state_id'));
			$flats->addExpression('b_address')->set($flats->refSQL('member_id')->fieldQuery('address'));
			$flats->addExpression('b_city')->set($flats->refSQL('member_id')->fieldQuery('city'));
			$flats->addExpression('b_pincode')->set($flats->refSQL('member_id')->fieldQuery('pin_code'));

			$flats->addCondition('apartment_id',$ap_model->id)
				->addCondition('is_generate_bill',true)
				->addCondition('status','<>','Deactive')
				->addCondition('member_id','>',0)
				->addCondition('last_invoice_month','<>',$current_month_year);

			if($this->debug)
				$this->owner->add('Grid')->setModel($flats);

			$master = [];
			foreach ($flats as $flat) {
				// $master['qsp_no'] = ;
				$master['contact_id'] = $flat['member_id'];
				// $master['serial'] = "";
				$master['currency_id'] = $default_currency_id;
				$master['nominal_id'] = $default_nominal_id;
				$master['billing_country_id'] = $flat['b_country_id'];
				$master['billing_state_id'] = $flat['b_state_id'];
				$master['billing_name'] = $flat['member'];
				$master['billing_address'] = $flat['b_address'];
				$master['billing_city'] = $flat['b_city'];
				$master['billing_pincode'] = $flat['b_pincode'];
				$master['created_at'] = $this->app->now;
				$master['due_date'] = date('Y-m-'.($ap_model['last_submission_date']?$ap_model['last_submission_date']:$ap_model['bill_generation_date']),strtotime($this->app->now));
				$master['narration'] = "Maintenance for Flat: ".$flat['name']." of apartment ".$ap_model['name']." for ".$this->app->today;
				$master['round_amount'] = 0;
				$master['discount_amount'] = 0; 
				$master['exchange_rate'] = 1;
				$master['status'] = "Due";

				// item detail
				$detail = [];
				$detail[] = [
							'item_id'=> $item_model->id,
							'price'=> $ap_model['maintenance_amount'],
							'quantity'=>1,
							'taxation_id'=>0,
							'tax_percentage'=>0,
							'narration'=> "Maintenance fees for Flat: ".$flat['name']." of apartment ".$ap_model['name']." date ".$this->app->today,
							'extra_info'=> "",
							'qty_unit_id'=> $item_model['qty_unit_id'],
							'discount'=>0,
							'treat_sale_price_as_amount'=>1
						];

				$this->app->qsp_saving_from_pos = true;
				$sale_invoice = $this->add('xepan\commerce\Model_SalesInvoice');
				$master_model = $sale_invoice->createQSPMaster($master,'SalesInvoice');
				$old_new_ids_array = $sale_invoice->addQSPDetail($detail,$master_model);

				$master['detail'] = $detail;
				$qsp_array[] = $master;
			}
			
		}

		if($this->debug)
			$this->app->print_r($qsp_array);

	}

}