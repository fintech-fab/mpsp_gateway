<?php

return [

	'connections' => [

		'api'     => [
			'driver'  => 'iron',
			'host'    => 'mq-aws-us-east-1.iron.io',
			'token'   => 'your-token',
			'project' => 'your-project',
			'queue'   => 'api',
		],

		'gateway' => [
			'driver'  => 'iron',
			'host'    => 'mq-aws-us-east-1.iron.io',
			'token'   => 'your-token',
			'project' => 'your-project',
			'queue'   => 'gateway',
		],

	],

];