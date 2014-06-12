<?php namespace FintechFab\MPSP\Support;

use FintechFab\MPSP\Facades\MPSP;
use Illuminate\Support\ServiceProvider;

class MPSPServiceProvider extends ServiceProvider
{

	public function register()
	{
		$this->app->singleton('mpsp', function () {

			return new MPSP\BaseMPSP($this->app);
		});
	}

}