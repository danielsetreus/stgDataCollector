<?php

return [
	'local' => [
		'host' => getenv("locDbHost"),
		'user' => getenv("locDbUser"),
		'pasw' => getenv("locDbPassword"),
		'name' => getenv("locDbName"),
	],
	'stg_g' => [
		'host' => getenv("shiptoga_g_host"),
		'user' => getenv("shiptoga_g_user"),
		'pasw' => getenv("shiptoga_g_pasw"),
		'name' => getenv("shiptoga_g_name"),
	],
	'stg_m' => [
		'host' => getenv("shiptoga_m_host"),
		'user' => getenv("shiptoga_m_user"),
		'pasw' => getenv("shiptoga_m_pasw"),
		'name' => getenv("shiptoga_m_name"),
	],
];