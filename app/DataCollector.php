<?php

	namespace StG;
	use FileStorage\Adapter\Local;
	use FileStorage\FileStorage;
	use FileStorage\Exception\FileNotFoundException;

	class DataCollector {

		private static $data = [];
		private static $openedConnections = [];

		public static function addData($source, $input) {
			$parts = explode(":", $source);

			if (count($parts) > 1) {
				if (array_key_exists($parts[0], self::$data)) {
					self::$data[$parts[0]][$parts[1]] = $input;
				} else
					self::$data[$parts[0]] = array($parts[1] => $input);

			} else
				self::$data[$source] = array($source => $input);

		}

		public static function getData() {
			return self::$data;
		}

		public static function getDatabaseConnection($dbName) {
			if (!array_key_exists($dbName, self::$openedConnections)) {
				try {
					self::$openedConnections[$dbName] = new DbConnection($dbName);
				} catch(\Exception $e) {
					return false; 
				}
			}
			return self::$openedConnections[$dbName];
		}

		public static function store($file, $key, $value) {
			$adapter = new Local("storage"); // @TODO: Save path in .env
			$storage = new FileStorage($adapter);
			try {
				$file = $storage->load($file);
			} catch(FileNotFoundException $e) {
				$file = $storage->init($file);
				$file->setContent("{}");
				$storage->save($file);
			}
			
			$content = json_decode($file->getContent());
			
			$content->$key = $value;
			$file->setContent(json_encode($content));
			$storage->save($file);	
		}

		public static function getFromStorage($file, $key = null) {
			$adapter = new Local("storage"); // @TODO: Save path in .env
			$storage = new FileStorage($adapter);
			try {
				$file = $storage->load($file);
				$content = json_decode($file->getContent());
				if ($key && array_key_exists($key, $content))
					return $content->$key;
				else
					return $content;
			} catch(FileNotFoundException $e) {
				return false;
			}
		}

	}