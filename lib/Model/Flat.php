<?php

namespace rakesh\apartment;

class Model_Flat extends \xepan\base\Model_Table{

	public $table = 'r_flat';
	public $status = ['Sold','OnRent','Vacant','Deactive'];

	public $actions = [
			'Sold'=>['View','edit','delete'],
			'OnRent'=>['View','edit','delete'],
			'Vacant'=>['View','edit','delete'],
			'Deactive'=>['View','edit','delete']
		];
	public $acl_type = "Flat";

	function init(){
		parent::init();

		$this->hasOne('rakesh\apartment\Apartment','apartment_id');
		$this->hasOne('rakesh\apartment\Member','member_id');

		$this->addField('name')->sortable(true)->caption('Flat Name');
		$this->addField('size')->setValueList(['1BHK'=>'1 BHK','2BHK'=>'2 BHK','3BHK'=>'3 BHK','4BHK'=>'4 BHK','Other'=>'Other']);
		$this->addField('is_generate_bill')->type('boolean')->defaultValue(true);
		$this->addField('status')->enum($this->status);
		
		$this->is([
			'apartment_id|to_trim|required',
			'name|to_trim|required',
			'size|to_trim|required',
			'status|to_trim|required'
		]);

	}

	function getMemberId($flat_id){
		$flat_model = $this->add('rakesh\apartment\Model_Flat');
		$flat_model->load($flat_id);
		return $flat_model['member_id'];
	}

	function associateWith($flat_comma_string,$member_id){

		$flat_array = explode(",", $flat_comma_string);

		// remove first
		$flat_model = $this->add('rakesh\apartment\Model_Flat')
						->addCondition('member_id',$member_id);
		foreach ($flat_model as $model) {
			$model['member_id'] = 0;
			$model->saveAndUnload();
		}
		// new association
		foreach ($flat_array as $key => $flat_id){
			$flat_model = $this->add('rakesh\apartment\Model_Flat')->load($flat_id);
			$flat_model['member_id'] = $member_id;
			$flat_model->save();
		}
	}



	function checkMemberAssociation($flat_comma_string,$member_id){
		$flat_array = explode(",", $flat_comma_string);
		$result = ['result' => 1,'message'=>''];

		foreach ($flat_array as $key => $flat_id) {
			$flat_model = $this->add('rakesh\apartment\Model_Flat')->load($flat_id);
			if($flat_model['member_id'] AND $flat_model['member_id'] != $member_id){
				$result['result'] = 0;
				$result['message'] .= " Flat ".$flat_model['name']. ' associateWith '.$flat_model['member'].", ";
			} 
		}

		return $result;
	}
}