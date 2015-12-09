<?php
	namespace StG\Collectors;

	use \StG\DataCollector;
	use \StG\CollectorService;

	class PaymentsCollector implements CollectorService {

		public function run() {

			// Stripe payments
			$stripeTrans = DataCollector::getFromStorage("Stripe.Charges");
			// Paypal payments
			$paypalTrans = DataCollector::getFromStorage("PayPal.Transactions");


			foreach($stripeTrans as $key => $value) {
				$sum = $value;
				if (isset($paypalTrans->$key))
					$sum += $paypalTrans->$key;
				DataCollector::addData("Payments:" . $key, $sum);
			}

		}

	}