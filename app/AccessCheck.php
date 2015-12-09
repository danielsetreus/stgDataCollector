<?php

	namespace StG;

	class AccessCheck {

		public static function check() {
			if (getenv("develop") == "true")
				return array(true, null);

			elseif (isset($_POST['TOKEN'])) {
				if ($_POST['TOKEN'] == md5(getenv("access_token")))
					return array(true, null);
				else
					return array(false, "Incorrect access token");
			}

			else
				return array(false, "No access token supplied");

		}
	}

