<?php
	namespace StG\Collectors;

	use \StG\DataCollector;
	use \StG\CollectorService;
	use Stripe\Stripe;
	use Stripe\Charge;
	use Stripe\Transfer;

	class StripeCollector implements CollectorService {

		private $stripe_api_key;

		public function run() {
			DataCollector::addData("Stripe:charges", DataCollector::getFromStorage("Stripe.Charges"));
			DataCollector::addData("Stripe:transfers", DataCollector::getFromStorage("Stripe.Transfers"));
		}

		public function scheme($interval) {
			$this->stripe_api_key = getenv('stripe_api_key');
			Stripe::setApiKey($this->stripe_api_key);

			DataCollector::store("Stripe.Charges", date("W"), $this->getChargesSumLastWeek());
			DataCollector::store("Stripe.Transfers", date("W"), $this->getTransfersSumLastWeek());

		}

		private function getChargesSumLastWeek() {
			$backLimit = strtotime(date("Y-m-d", strtotime("-1 week")));

			$charges = Charge::all(array(
				'created' => array (
					'gte' => $backLimit
				)
			));
			$sum = 0;
			foreach($charges->data as $charge) {
				$sum += substr($charge->amount, 0, -2);
			}
			return $sum;
		}

		private function getTransfersSumLastWeek() {
			$backLimit = strtotime(date("Y-m-d", strtotime("-1 week")));

			$transfers = Transfer::all(array(
				'created' => array (
					'gte' => $backLimit
				)
			));
			$sum = 0;
			foreach($transfers->data as $transfer) {
				$p1 = substr($transfer->amount, 0, -2);
				$p2 = substr($transfer->amount, -2);
				$sum += $p1 . "." . $p2;
			}
			return $sum;
		}

	}