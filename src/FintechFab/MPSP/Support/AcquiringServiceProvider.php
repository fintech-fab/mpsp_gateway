<?php namespace FintechFab\MPSP\Support;

use Config;
use FintechFab\MPSP\Services\Interfaces\AcquiringInterface;
use Illuminate\Support\ServiceProvider;

class AcquiringServiceProvider extends ServiceProvider
{

	public function register()
	{
		$this->app->singleton(AcquiringInterface::class, Config::get('services/acquiring.default'));
	}

}