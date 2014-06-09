<?php

return [

	'debug'     => false,

	'providers' => [
		\FintechFab\MPSP\Support\BankEmulatorServiceProvider::class,
		\FintechFab\MPSP\Support\TransferEmulatorServiceProvider::class,
	],

];