<?php
	namespace StG\Collectors;
	use \StG\DataCollector;
	use \StG\CollectorService;
	use \Google_Client;
	use \Google_Service_Analytics;
	use \Google_Auth_AssertionCredentials;

	class GaCollector implements CollectorService {

		private $service;
		private $profile;

		public function run() {
			$a1 = DataCollector::getFromStorage("GA.PageViews");
			DataCollector::addData("GA:PageViews", $a1);		
		}

		public function scheme($interval) {
			try {
				$this->analytics = $this->getService();
				$this->profile = $this->getFirstProfileId($this->analytics);
				$an = $this->analytics->data_ga->get(
					'ga:' . $this->profile,
					'1daysAgo',
					'1daysAgo',
					'ga:pageviews');
				$rows = $an->getRows();
				DataCollector::store("GA.PageViews", date("Y-m-d", strtotime("-1 day")), $rows[0][0]);
			} catch(\Exception $e) {
				\StG\FileLogger::logError("Error fetching GA stats", array('ExceptionMessage' => $e->getMessage()));
			}
		}

		private function getService() {
			$service_account_email = getenv('ga_service_account');
			$key_file_location = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'stg-3cbe1b07c32b.p12';

			// Create and configure a new client object.
			$client = new Google_Client();
			$client->setApplicationName("StG Analytics Stats");
			$analytics = new Google_Service_Analytics($client);

			// Read the generated client_secrets.p12 key.
			$key = file_get_contents($key_file_location);
			$cred = new Google_Auth_AssertionCredentials(
				$service_account_email,
				array(Google_Service_Analytics::ANALYTICS_READONLY),
				$key
			);
			$client->setAssertionCredentials($cred);

			if($client->getAuth()->isAccessTokenExpired()) {
				$client->getAuth()->refreshTokenWithAssertion($cred);
			}

			return $analytics;
		}

		private function getFirstprofileId($analytics) {
			// Get the user's first view (profile) ID.

			// Get the list of accounts for the authorized user.
			$accounts = $analytics->management_accounts->listManagementAccounts();

			if (count($accounts->getItems()) > 0) {
				$items = $accounts->getItems();
				$firstAccountId = $items[0]->getId();

				// Get the list of properties for the authorized user.
				$properties = $analytics->management_webproperties
				->listManagementWebproperties($firstAccountId);

				if (count($properties->getItems()) > 0) {
					$items = $properties->getItems();
					$firstPropertyId = $items[0]->getId();

					// Get the list of views (profiles) for the authorized user.
					$profiles = $analytics->management_profiles
					->listManagementProfiles($firstAccountId, $firstPropertyId);

					if (count($profiles->getItems()) > 0) {
						$items = $profiles->getItems();

						// Return the first view (profile) ID.
						return $items[0]->getId();

					} else {
						throw new Exception('No views (profiles) found for this user.');
					}
				} else {
					throw new Exception('No properties found for this user.');
				}
			} else {
				throw new Exception('No accounts found for this user.');
			}
		}

	}