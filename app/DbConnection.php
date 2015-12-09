<?php
	namespace StG;
	use Monolog\Logger;
	use Monolog\Handler\StreamHandler;
	use mysqli;

	class DbConnection extends mysqli {

		public function __construct($db) {
			$available = require("helpers/dbConnections.php");
			$host = $available[$db]['host'];
			$user = $available[$db]['user'];
			$pasw = $available[$db]['pasw'];
			$name = $available[$db]['name'];

			@$this->conection = parent::__construct($host, $user, $pasw, $name);

			if (mysqli_connect_errno()) {
				$this->log("Error connection to database " . $db . " (" . $host . ")", array('error' => mysqli_connect_error()));
				throw new \Exception("Error Connecton to Database");
			}
			parent::set_charset("utf8");
		}

		public function connectionInfo() {
			return "Version: " . $this->server_info;
		}

		public function query($str) {
			$res = parent::query($str);
			if (!$res) {
				$this->log("Query returned error", array('query' => $str, 'error' => $this->error));
			}
			return $res;
		}

		private function log($errString, $errArray = null) {
			$log = new Logger('app');
			$log->pushHandler(new StreamHandler('app.log', Logger::WARNING));
			$log->addError($errString, $errArray);
		}

		public static function resultToArray($result) {
			$arr = array();
			while($row=mysqli_fetch_assoc($result)){
				$arr[] = $row;
			}
			
			return $arr;
		}

	}