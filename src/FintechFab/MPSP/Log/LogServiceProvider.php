<?php namespace FintechFab\MPSP\Log;

use Illuminate\Support\ServiceProvider;
use Monolog\Logger;

class LogServiceProvider extends ServiceProvider
{

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$logger = new Writer(
			new Logger($this->app['env']), $this->app['events']
		);

		$this->app->instance('log', $logger);
	}

}