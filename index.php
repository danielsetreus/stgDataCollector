<?php

	$loader = require __DIR__ . '/vendor/autoload.php';

	$dotenv = new Dotenv\Dotenv(__DIR__);
	$dotenv->load();




	if (isset($_GET['schema'])) {
		$schema = new StG\SchemaCollector($_GET['schema']);
		$schema->collect();

	} else {
		// First - check if the request was made correclty
		$check = StG\AccessCheck::check();
		if (! $check[0]) {
			header('HTTP/1.0 403 Forbidden');
			echo $check[1];
			die();
		}
		
			
		$stg = new StG\Collector;
		$stg->collect();
		$data = Stg\DataCollector::getData();
		echo json_encode($data, JSON_FORCE_OBJECT);
	}