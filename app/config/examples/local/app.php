<?php

return [

	'debug'     => true,

	'providers' => [
		'Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider', // ide-helper

		\FintechFab\MPSP\Support\AcquiringBankEmulatorServiceProvider::class,
		\FintechFab\MPSP\Support\TransferEmulatorServiceProvider::class,
		\FintechFab\MoneyTransferEmulator\MoneyTransferEmulatorServiceProvider::class,
		\FintechFab\BankEmulator\BankEmulatorServiceProvider::class,

	],

];