<?php

namespace rakesh\apartment;

class Controller_APTBillGeneration extends \AbstractController {

	public $for_apartment = null;
	public $debug = false;

	function setApartment($apartment_id){
		if($apartment_id) $this->for_apartment = $apartment_id;
	}

	function run(){
		$today_date = date('d',strtotime($this->app->today));
		$today_date = "03";
		$current_month_year = date('n-Y',strtotime($this->app->today));

		$apartment = $this->add('rakesh\apartment\Model_Apartment');
		$apartment->addExpression('bill_generated_on_month_year')->set(function($m,$q){
			return $q->expr('concat(MONTH([0]),"-",year([0]))',[$m->getElement('bill_generated_on_date')]);
		});

		$apartment->addExpression('active_flats')->set(function($m,$q){
			$f_model = $m->add('rakesh\apartment\Model_Flat');
			$f_model->addCondition('apartment_id',$m->getElement('id'))
					->addCondition('member_id','>',0)
					->addCondition('is_generate_bill',true)
					->addCondition('status','<>','Deactive')
					;
			return $q->expr('[0]',[$f_model->count()]);
		});
		$apartment->addCondition('status',['Trial','Paid','Grace']);
		$apartment->addCondition('active_flats','>',0);

		if($this->for_apartment)
			$apartment->addCondition('id',$this->for_apartment);

		$apartment->addCondition('bill_generation_date',$today_date);
		$apartment->addCondition('bill_generated_on_month_year','<>',$current_month_year);

		if($this->debug){
			$this->owner->add('View')->set('Current Month: '.$current_month_year." Today Date: ".$today_date);
			$grid = $this->owner->add('Grid')->setModel($apartment);
		}

		foreach($apartment as $ap_model){
			$flats = $this->add('rakesh\apartment\Model_Flat');
			$flats->addExpression('last_invoice_month')->set(function($m,$q){
				$saleinv = $m->add('rakesh\apartment\Model_Invoice');
				$saleinv->addCondition('flat_id',$m->getElement('id'));
				$saleinv->setOrder('id','desc');
				$saleinv->setLimit(1);
				return $q->expr('concat(MONTH([0]),"-",year([0]))',[$saleinv->fieldQuery('created_at')]);
			});
			$flats->addCondition('apartment_id',$ap_model->id)
				->addCondition('is_generate_bill',true)
				->addCondition('status','<>','Deactive')
				->addCondition('member_id','>',0)
				->addCondition([['last_invoice_month','<>',$current_month_year],['last_invoice_month',NULL]])
				;
			
			if($this->debug){
				$this->owner->add('Grid')->setModel($flats);
			}

			$query = 'INSERT INTO `r_payment_transaction` (`apartment_id`, `member_id`, `flat_id`, `paid_by_id`, `affiliate_id`, `name`, `amount`, `panelty`, `created_at`, `updated_at`, `is_expences`, `is_invoice`, `status`, `invoice_narration`) VALUES ';
			$values = "";
			foreach ($flats as $flat) {
				$name = date('Ym',strtotime($this->app->today))."-".$flat->id."-".$flat['member_id'];
				$narration = "Maintenance fees for Flat: ".$flat['name']." for Member: ".$flat['member']." of apartment: ".$ap_model['name']." for month: ".$this->app->today;
				$values .= "('".$ap_model->id."', '".$flat['member_id']."', '".$flat->id."', NULL, NULL, '".$name."', '".$ap_model['maintenance_amount']."', '0', '".$this->app->now."', '".$this->app->now."', '0', '1', 'Due', '".$narration."'),";
			}
			$query .= trim($values,",").";";
			$query .= "UPDATE `r_apartment` SET `bill_generated_on_date` = '".$this->app->today."', `bill_generated_on_time`='".date('H:i:s',strtotime($this->app->now))."' WHERE `r_apartment`.`id` = '".$ap_model->id."';";
			
			try{
				$this->app->db->beginTransaction();
				$this->app->db->dsql()->expr($query)->execute();
				$this->app->db->commit();
			}catch(\Exception $e){
				if($this->app->db->intransaction()) $this->api->db->rollback();
			}

			if($this->debug) $this->owner->add('View')->set($query);
		}

	}
}