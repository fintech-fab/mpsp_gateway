<?php
use FintechFab\MPSP\Entities\Card;
use FintechFab\MPSP\Services\AcquiringService;
use Mockery as m;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{

	public function setUp()
	{
		parent::setUp();

		Queue::shouldReceive();
	}

	/**
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		/** @noinspection PhpUnusedLocalVariableInspection */
		$unitTesting = true;

		/** @noinspection PhpUnusedLocalVariableInspection */
		$testEnvironment = 'testing';

		return require 'bootstrap/start.php';
	}

	public function tearDown()
	{
		m::close();

		parent::tearDown();
	}

	/**
	 * @param $class_name
	 *
	 * @return \Mockery\MockInterface|\Mockery\Expectation|\FintechFab\MPSP\Services\TransferService|Card|AcquiringService|\FintechFab\MPSP\Services\Interfaces\AcquiringInterface|\FintechFab\MPSP\Queue\Jobs\AbstractJob
	 */
	protected function mock($class_name)
	{
		$mock = m::mock($class_name);

		$this->app->instance($class_name, $mock);

		return $mock;
	}

}
