<?php
	namespace StG;

	class Collector {

		private $collectors = [
			'StG\Collectors\ActiveMembersCollector',
			'StG\Collectors\GaCollector',
			'StG\Collectors\StripeCollector',
			'StG\Collectors\PaypalCollector',
			'StG\Collectors\PaymentsCollector',
			'StG\Collectors\SocialInteractionCollector'
		];

		public function __construct() {
			// What to do here?
		}

		public function collect() {
			foreach($this->collectors as $collector) {
				$c = new $collector;
				$c->run();
			}
		}

	}