<?php namespace FintechFab\MPSP\Sms;

use Config;
use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{

	public function register()
	{
		$this->app->singleton(SmsInterface::class, function () {
			$config = Config::get('providers/sms');
			$config = $config['connections'][$config['default']];

			$className = 'FintechFab\MPSP\\Sms\\Gates\\' . $config['class'] . 'Gate';
			$instance = new $className($config);

			return $instance;
		});
	}

}
