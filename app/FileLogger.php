<?php

	namespace StG;
	use Monolog\Logger;
	use Monolog\Handler\StreamHandler;

	class FileLogger {

		static function logError($errString, $errArray = null) {
			$log = new Logger('app');
			$log->pushHandler(new StreamHandler('app.log', Logger::WARNING));
			$log->addError($errString, $errArray);
		}

	}