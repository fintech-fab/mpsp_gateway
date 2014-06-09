<?php namespace FintechFab\MPSP\Support;

use Config;
use FintechFab\MPSP\Services\Interfaces\TransferInterface;
use Illuminate\Support\ServiceProvider;

class TransferServiceProvider extends ServiceProvider
{

	public function register()
	{
		$this->app->bind(TransferInterface::class, Config::get('services/transfer.default'));
	}

}