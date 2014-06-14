<?php namespace FintechFab\MPSP\Support;

use Config;
use FintechFab\BankEmulatorSdk\Gateway;
use FintechFab\MPSP\Services\Gates\AcquiringEmulatorGate;
use FintechFab\MPSP\Services\Interfaces\AcquiringInterface;
use Illuminate\Support\ServiceProvider;

class AcquiringBankEmulatorServiceProvider extends ServiceProvider
{

	public function register()
	{
		$this->app->singleton(AcquiringInterface::class, AcquiringEmulatorGate::class);

		$this->app->bind(Gateway::class, function () {

			$config = Config::get('providers/bank_emulator');

			return Gateway::newInstance($config);
		});
	}

}