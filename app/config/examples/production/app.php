<?php

return [

	'debug'     => false,

	'providers' => [
		\FintechFab\MPSP\Support\AcquiringBankEmulatorServiceProvider::class,
		\FintechFab\MPSP\Support\TransferEmulatorServiceProvider::class,
	],

];