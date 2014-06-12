<?php namespace FintechFab\MPSP\Facades;

use Illuminate\Support\Facades\Facade;

class MPSP extends Facade
{

	protected static function getFacadeAccessor()
	{
		return 'mpsp';
	}

} 