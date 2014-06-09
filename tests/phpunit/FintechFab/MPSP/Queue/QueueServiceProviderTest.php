<?php
use FintechFab\MPSP\Queue\Jobs\AcquiringFinish3DSJob;
use FintechFab\MPSP\Queue\Jobs\AcquiringJob;
use FintechFab\MPSP\Queue\Jobs\CalculateFeeJob;
use FintechFab\MPSP\Queue\Jobs\CheckJob;
use FintechFab\MPSP\Queue\QueueServiceProvider;

/**
 * @property QueueServiceProvider oServiceProvider
 */
class QueueServiceProviderTest extends TestCase
{

	public function setUp()
	{
		parent::setUp();

		Queue::shouldReceive('connection');
	}

	/**
	 * роутинг для очередей задан корректно
	 */
	public function testRegister()
	{
		$bindings = $this->app->getBindings();

		$jobs = [
			'calculateFee'       => CalculateFeeJob::class,
			'transferCheck'      => CheckJob::class,
			'acquiring'          => AcquiringJob::class,
			'acquiringFinish3DS' => AcquiringFinish3DSJob::class,
		];

		foreach ($jobs as $name => $class) {

			$this->assertTrue(isset($bindings[$name]));

			$job = $this->app->make($name);

			$this->assertTrue(is_object($job));
			$this->assertInstanceOf($class, $job);
		}

	}

} 