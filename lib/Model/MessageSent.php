<?php

namespace rakesh\apartment;

class Model_MessageSent extends \xepan\communication\Model_Communication_AbstractMessage{

	function init(){
		parent::init();
		$this->addCondition('status','Sent');
		$this->addCondition('direction','Out');
		
		$this->addHook('afterSave',$this);
		$this->addHook('afterInsert',$this);
	}
	
	function afterSave(){

		// append html
		$default_img_url = 'websites/'.$this->app->current_website_name.'/www/dist/img/avatar04.png';
		$msg = [
				'title'=>$this['from'].' messaged you:',
				'message'=>$this['description'],
				'type'=>'',
				'sticky'=>false,
				'desktop'=>strip_tags($this['description']),
				'from'=>$this['from'],
				'to'=>$this['to'],
				'from_image'=>($this['image']?:$default_img_url),
				'send_date' => date('M d H:i a',strtotime($this['created_at'])),
				'cmd'=>'chatmessage'
			];
			// 'js'=>(string) $this->app->js()->_selector('.ap-chat-message-trigger-reload')->trigger('reload')

		$to_id = [];
		foreach ($this['to_raw'] as $key => $value) {
			$to_id[] = $value['id'];
		}

		$this->pushToWebSocket($to_id,$msg,'chatmessage');
	}

		// send custom message to
		// $ml = $this->add('rakesh\apartment\Model_MessageSent');
		// 	$ml->pushToWebSocket([$this->contact_to_id],$msg);
	function pushToWebSocket($to_id,$msg,$cmd='notification'){

		$this->server = $this->app->getConfig('ap-websocket-server','ws://127.0.0.1:8890');
		
		if(!$this->server) return;
		
		$response = [];

		$uu_ids = [];
		foreach ($to_id as $id) {
			$uu_ids [] = str_replace("_", "",$this->app->normalizeName($this->app->apartment['name'])).'_'.$this->app->apartment->id.'_'.$id;
		}

		$data = ['clients'=>$uu_ids,'message'=>$msg,'cmd'=>$cmd];
		
		$client = new \Hoa\Websocket\Client(
		    new \Hoa\Socket\Client($this->server)
		);

		$client->on('message', function (\Hoa\Event\Bucket $bucket) use(&$response) {
		    $data = $bucket->getData();
		    $response = $data['message'];
		    return $response;
		});

		$client->connect();
		
		$client->send(json_encode($data));

		$client->receive();

		$client->close();

		return $response;
	}

	function afterInsert(){
		$this->breakHook(null);
	}
}