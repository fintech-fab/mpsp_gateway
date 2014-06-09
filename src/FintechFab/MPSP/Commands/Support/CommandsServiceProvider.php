<?php namespace FintechFab\MPSP\Commands\Support;

use FintechFab\MPSP\Commands\Transfer\CancelCommand;
use FintechFab\MPSP\Commands\Transfer\FeeCommand;
use FintechFab\MPSP\Commands\Transfer\StatusCommand;
use Illuminate\Support\ServiceProvider;

class CommandsServiceProvider extends ServiceProvider
{

	public function register()
	{
		$commands = [
			FeeCommand::class,
			StatusCommand::class,
			CancelCommand::class,
		];

		$this->commands($commands);
	}

}