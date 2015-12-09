<?php

	namespace StG;

	class SchemaCollector {
		private $interval;
		private $collectors;

		public function __construct($interval) {
			switch($interval) {
				case "daily":
					$this->collectors = [
						'StG\Collectors\ActiveMembersCollector',
						'StG\Collectors\GaCollector',
					];
				break;

				case "weekly":
					$this->collectors = [
						'StG\Collectors\StripeCollector',
						'StG\Collectors\PaypalCollector'
					];
				break;

				case "montly":
					$this->collectors = [

					];
				break;
					$this->collectors = [

					];
				default:
			}
		}

		public function collect() {
			foreach($this->collectors as $collector) {
				$c = new $collector;
				$c->scheme($this->interval);
			}
		}

	}