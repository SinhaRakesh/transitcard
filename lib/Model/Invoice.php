<?php

namespace rakesh\apartment;

class Model_Invoice extends \rakesh\apartment\Model_PaymentTransaction{
	function init(){
		parent::init();

		$this->addCondition('is_expences',false);
		$this->addCondition('is_invoice',true);
		$this->getElement('name')->caption('Bill No');
		$this->getElement('status')->defaultValue('Due');
		
	}

	function paid(){
		return $this->app->redirect($this->app->url('dashboard',['mode'=>'invoiceedit','action'=>'edit','r_payment_transaction_id'=>$this->id]));
	}

	function sendNotification(){
		if($this['status'] != "Paid") return;

		$title = "Invoice Paid ";
		$description = "Bill No: ".$this['name']." of month ".date('M-Y',strtotime($this['created_at']))." is Paid Successfully";
		$send_msg = $this->add('rakesh\apartment\Model_MessageSent');
		$send_msg['from_id'] = $this['paid_by_id'];
		$send_msg['from_raw'] = ['name'=>$this['paid_by'],'id'=>$this['paid_by_id']];
		$send_msg['to_id'] = $this['member_id'];
		$send_msg['to_raw'] = json_encode([['name'=>$this['member'],'id'=>$this['member_id']]]);
		$send_msg['mailbox'] = "Invoice";
		// $send_msg['related_document_id'] = $this->id;
		$send_msg['created_by_id'] = $this->app->apartmentmember->id;
		$send_msg['related_id'] = $this->id;
		$send_msg['title'] = $title;
		$send_msg['description'] = $description;
		$send_msg['type'] = 'notification';
		$send_msg['sub_type'] = "InvoicePaid";
		$send_msg->save();
		$send_msg->sendNotification();
	}
}