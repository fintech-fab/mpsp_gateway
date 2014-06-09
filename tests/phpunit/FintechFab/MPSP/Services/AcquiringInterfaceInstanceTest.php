<?php
use FintechFab\MPSP\Services\Gates\AcquiringEmulatorGate;
use FintechFab\MPSP\Services\Interfaces\AcquiringInterface;

class AcquiringInterfaceInstanceTest extends TestCase
{

	/**
	 * при запросе из IoC AcquiringInterface - получается объект заданный в конфиге
	 */
	public function test()
	{
		$acquiring = App::make(AcquiringInterface::class);

		$this->assertInstanceOf(AcquiringEmulatorGate::class, $acquiring);
	}

} 