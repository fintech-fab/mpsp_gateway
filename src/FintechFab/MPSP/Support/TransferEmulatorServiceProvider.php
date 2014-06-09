<?php namespace FintechFab\MPSP\Support;

use Config;
use FintechFab\MoneyTransferEmulatorSdk\Gateway;
use Illuminate\Support\ServiceProvider;

class TransferEmulatorServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind(Gateway::class, function () {

			$config = Config::get('providers/transfer_emulator');

			return Gateway::newInstance($config);
		});
	}
}