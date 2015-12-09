<?php
	namespace StG\Collectors;
	use \StG\DataCollector;
	use \StG\CollectorService;

	class ActiveMembersCollector implements CollectorService {
		private $s;
		public function run() {

			DataCollector::addData("ActiveMembers:runtimestamp", time());
			$a1 = DataCollector::getFromStorage("ActiveMembersByDate", "createddd");
			DataCollector::addData("ActiveMembers:count", $a1);
	
		}

		public function scheme($interval) {
			if ($con = DataCollector::getDatabaseConnection("stg_g")) {
				$res = $con->query("SELECT stgmembers_members.memId AS activeMembers FROM stgmembers_payment LEFT JOIN stgmembers_members ON stgmembers_payment.memId = stgmembers_members.memId WHERE stgmembers_payment.payDate > '".date("Y", strtotime("-1 year"))."-11-01' GROUP BY stgmembers_payment.memId");
				DataCollector::store("ActiveMembersByDate", date("Y-m-d"), $res->num_rows);
			}
		}

	}