<?php

return [

	'debug'     => true,

	'providers' => [
		'Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider',

		\FintechFab\MPSP\Support\AcquiringBankEmulatorServiceProvider::class,
		\FintechFab\MPSP\Support\TransferEmulatorServiceProvider::class,

	],

];