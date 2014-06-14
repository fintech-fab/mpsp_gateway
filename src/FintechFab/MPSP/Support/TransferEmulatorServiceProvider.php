<?php namespace FintechFab\MPSP\Support;

use Config;
use FintechFab\MoneyTransferEmulatorSdk\Gateway;
use FintechFab\MPSP\Services\Gates\TransferEmulatorGate;
use FintechFab\MPSP\Services\Interfaces\TransferInterface;
use Illuminate\Support\ServiceProvider;

class TransferEmulatorServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind(TransferInterface::class, TransferEmulatorGate::class);

		$this->app->bind(Gateway::class, function () {

			$config = Config::get('providers/transfer_emulator');

			return Gateway::newInstance($config);
		});
	}
}