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
	public $size = ['1BHK'=>'1 BHK','2BHK'=>'2 BHK','3BHK'=>'3 BHK','4BHK'=>'4 BHK','Other'=>'Other'];
	public $acl_type = "Flat";
	public $title_field = "name_with_block";

	function init(){
		parent::init();

		$this->hasOne('rakesh\apartment\Apartment','apartment_id');
		$this->hasOne('rakesh\apartment\Block','block_id');
		$this->hasOne('rakesh\apartment\Member','member_id');

		$this->addField('name')->sortable(true)->caption('Flat Name');
		$field_size = $this->addField('size');
		$this->addField('is_generate_bill')->type('boolean')->defaultValue(true);
		$field_status = $this->addField('status');
		// $this->addField('last_bill_generation_date');
		
		$this->addExpression('name_with_block')->set(function($m,$q){
			return $q->expr('CONCAT([1]," (",IFNULL([0]," "),")")',[$m->getElement('block'),$m->getElement('name')]);
		});

		$this->addExpression('name_with_member')->set(function($m,$q){
			return $q->expr('CONCAT([0]," :: ",IFNULL([1]," "))',[$m->getElement('name_with_block'),$m->getElement('member')]);
		});

		$this->is([
			'apartment_id|to_trim|required',
			'name|to_trim|required',
			'size|to_trim|required',
			'status|to_trim|required'
		]);

		// flat master data
		$config_model = $this->add('rakesh\apartment\Model_Config_Master')->tryLoadAny();
		if($config_model['flat_size']){
			$this->size = [];
			foreach (explode(",", $config_model['flat_size']) as $key => $value) {
				$this->size[trim($value)] = trim($value);
			}
		}
		$field_size->setValueList($this->size);
		if($config_model['flat_status']){
			$this->status = [];
			foreach (explode(",", $config_model['flat_status']) as $key => $value) {
				$this->status[trim($value)] = trim($value);
			}
			$this->status['Deactive'] = 'Deactive';
		}
		$field_status->setValueList($this->status);

		$this->addHook('beforeSave',[$this,'checkDuplicate']);
	}

	function checkDuplicate(){
		$model = $this->add('rakesh\apartment\Model_Flat');
		$model->addCondition('apartment_id',$this->app->apartment->id);
		$model->addCondition('block_id',$this['block_id']);
		$model->addCondition('name',$this['name']);
		$model->addCondition('id','<>',$this->id);
		$model->tryLoadAny();

		if($model->loaded()) throw $this->exception('name is already taken','ValidityCheck')->setField('name');

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

	function removeAssociation($member_id){
		if(!$member_id) throw new \Exception("member id not defined");
		
		$flat_model = $this->add('rakesh\apartment\Model_Flat')
			->addCondition('member_id',$member_id);
		foreach ($flat_model as $m) {
			$m['member_id'] = 0;
			$m->saveAndUnload();
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