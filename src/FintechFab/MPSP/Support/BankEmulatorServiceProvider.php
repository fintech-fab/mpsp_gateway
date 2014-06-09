<?php namespace FintechFab\MPSP\Support;

use Config;
use FintechFab\BankEmulatorSdk\Gateway;
use Illuminate\Support\ServiceProvider;

class BankEmulatorServiceProvider extends ServiceProvider
{

	public function register()
	{
		$this->app->bind(Gateway::class, function () {

			$config = Config::get('providers/bankemulator');

			return Gateway::newInstance($config);
		});
	}

}