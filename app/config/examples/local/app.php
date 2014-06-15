<?php

return [

	'debug'     => true,

	'key'       => 'random-key',

	'providers' => [
		'Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider',

		\FintechFab\MPSP\Support\AcquiringBankEmulatorServiceProvider::class,
		\FintechFab\MPSP\Support\TransferEmulatorServiceProvider::class,

	],

];