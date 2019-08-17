<?php

// browers runs websocket if set true
$ip = '127.0.0.1';
$config['ap-websocket-notifications'] =  true;
// Websocket server selects ssl-websocket-server to run if set true
$config['ap-ssl-websocket-notifications'] =  false;

$config['ap-websocket-server']='ws://'.$ip.':8890';

if($config['ap-ssl-websocket-notifications']) { //HTTPS 
	// for web browser target variable
	$config['ap-websocket-server']='wss://'.$ip.':8890';
	$config['ap-ssl-certificate-pem-path']='./cert/wss_lets.pem';
}

$config['wsclients'] = [];