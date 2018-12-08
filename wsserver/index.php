<?php

require_once '../../../../../wsserver/vendor/autoload.php';

include '../config-default.php';

	$clients=[];

	if(isset($config['ap-ssl-websocket-notifications']) && $config['ap-ssl-websocket-notifications']){
		$Context = \Hoa\Stream\Context::getInstance('APAPP');
		$Context->setOptions([
		    'ssl' => [
		        'local_cert' => $config['ap-ssl-certificate-pem-path'],
		        'verify_peer'=>false,
		        'allow_self_signed'=>true,
			'cafile'=>'/xavoc.com-ssl-cert/xavoc.com/ca.cer'
		    ]
		]);
		$ssl_config='APAPP';
	}else{
		$ssl_config=null;
	}
	$ssl_config=null;

	echo "Running at ".$config['ap-websocket-server']."\n";

	$websocket = new Hoa\Websocket\Server(
	    new Hoa\Socket\Server($config['ap-websocket-server'],30,-1,$ssl_config)
	);

	$websocket->on('open', function (Hoa\Event\Bucket $bucket) {
	    echo 'new connection '."\n";
	    return;
	});


	$websocket->on('message', function (Hoa\Event\Bucket $bucket) use(&$clients) {
		echo "on message";
	    $data = $bucket->getData();
	    print_r($data);
	    $message = json_decode($data['message'],true);
	    $response="";
	    if(isset($message['cmd'])){
	    	switch ($message['cmd']) {
	    		case 'register':
	    			$clients[$message['uu_id']] = $bucket->getSource()->getConnection()->getCurrentNode();
	    			$response  = "Client ".$message['uu_id']." registred \n";
	    			$response  = "";
	    			break;
	    		
	    		case "notification":
	    		default:
	    			$response=[];
	    			foreach ($message['clients'] as $client) {
	    				if(isset($clients[$client])){
		    				$bucket->getSource()->send($message['message'],$clients[$client]);
		    				$response[]=$client; 
	    				}
	    			}

	    			$response = json_encode($response);
	    			break;
	    	}
	    }

	    $bucket->getSource()->send($response);
	    return;
	});
	$websocket->on('close', function (Hoa\Event\Bucket $bucket) {
	    echo 'connection closed', "\n";

	    return;
	});

	$websocket->run();
