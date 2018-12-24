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
	    echo 'wsp new connection '."\n";
	    return;
	});


	$websocket->on('message', function (Hoa\Event\Bucket $bucket) use(&$clients) {
		echo "wsp on message"."\n";
	    $data = $bucket->getData();
	    // echo "data \n";
	    // print_r($data);
	    $message = json_decode($data['message'],true);
	    // echo "message \n";
	    // print_r($message);
	    $response = "";

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

	    				// echo "message client ".$client."\n";
	    				// echo "registered saved client "."\n";
	    				// echo "<pre>";
	    				// print_r($clients);
	    				// echo "<\pre>";
	    				if(isset($clients[$client])){
	    					echo "wsp notification send \n";
		    				$bucket->getSource()->send(json_encode($message['message']),$clients[$client]);
		    				$response[]=$client; 
	    				}
	    			}

	    			$response = json_encode($response);
	    			break;
	    	}
	    }

	    echo "wsp bucket send "."\n";
	    $bucket->getSource()->send($response);
	    $config['wsclients'] = $clients;
	    return;
	});
	$websocket->on('close', function (Hoa\Event\Bucket $bucket) {
	    echo 'wsp websocket connection closed', "\n";
	    return;
	});

	$websocket->run();
