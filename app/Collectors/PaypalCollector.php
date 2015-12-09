<?php
	namespace StG\Collectors;

	use \StG\DataCollector;
	use \StG\CollectorService;

	class PaypalCollector implements CollectorService {

		public function run() {
			DataCollector::addData("PayPal:transactions", DataCollector::getFromStorage("PayPal.Transactions"));
		}

		public function scheme() {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://api-3t.paypal.com/nvp");
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

			$postArr = array(
				'USER' 		=> 	urlencode(getenv("paypal_user")),
				'PWD' 		=> 	urlencode(getenv("paypal_pwd")),
				'SIGNATURE' => 	urlencode(getenv("paypal_signature")),
				'VERSION' 	=>	urlencode('78'),
				'METHOD' 	=> 	urlencode('TransactionSearch'),
				'STARTDATE' => 	urlencode(date("c", strtotime("-1 week"))),
				'STATUS'	=> 	urlencode("Success"),
			);

			$postStr = "";
			foreach ($postArr as $key => $value) {
				$postStr .= "&" . $key . "=" . $value;
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postStr);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

			$result = curl_exec($ch);
			if (curl_errno($ch)) {
				\StG\FileLogger::logError("Error fetching PayPal Transactions", array("Curl_error" => curl_error($ch)));
			} else {
				$sum = 0;
				foreach($this->process_response($result) as $transaction) {
					$sum += $transaction['L_AMT'];
				}
				DataCollector::store("PayPal.Transactions", date("W"), $sum);
			}
			curl_close($ch);
		}

		function process_response($str) {
			$data = array();
			$x = explode("&", $str);

			foreach($x as $val) {
				$y = explode("=", $val);

				preg_match_all('/^([^\d]+)(\d+)/', $y[0], $match);

				if (isset($match[1][0])) {
					$text = $match[1][0];
					$num = $match[2][0];

					$data[$num][$text] = urldecode($y[1]);
				}
				else {
					$text = $y[0];
				}
			}

			return $data;
		}

	}