<?php

use FintechFab\MPSP\Queue\Jobs\AbstractJob;
use Illuminate\Queue\Jobs\Job;
use Mockery\MockInterface;

class AbstractJobTest extends TestCase
{

	/**
	 * Создаем Mock методы для компонента Log, потому что нам нужно тестировать не физическую запись в логи,
	 * а вызовы методов из компонента
	 */
	public function setUp()
	{
		parent::setUp();

		// Игнорируем сообщения в info
		Log::shouldReceive('info');
	}

	/**
	 * Проверяет, что если превышено число ошибок в задаче мы получим сообщение в лог уровня "error"
	 *
	 * Задача не будет перевыставлена (с помощью метода release)
	 */
	public function testAttemptsLogError()
	{
		$job = $this->mock(AbstractJob::class . '[run]');

		$job->shouldReceive('run')
			->andThrow(Exception::class, 'Unknown exception for unit tests')
			->once();

		/** @var $envelope Job|MockInterface */
		$envelope = $this->mock(Job::class);

		// Количество неудачных попыток - 4
		$envelope
			->shouldReceive('attempts')
			->once()
			->andReturn(4);

		// Удалется задача из очереди
		$envelope
			->shouldReceive('delete')
			->once();

		// И не перевыставляется
		$envelope
			->shouldReceive('release')
			->never();

		Log::shouldReceive('error')
			->once();

		$job->fire($envelope, []);
	}

	/**
	 * Проверяет что в случае необработанной ошибки метод fire пытается отложить задачу "на потом"
	 */
	public function testAttemptsRepeatJob()
	{
		$job = $this->mock(AbstractJob::class . '[run]');

		$job->shouldReceive('run')
			->andThrow(Exception::class, 'Unknown exception')
			->once();

		/** @var $envelope Job|MockInterface */
		$envelope = $this->mock(Job::class);

		// Первая неудачная попытка
		$envelope
			->shouldReceive('attempts')
			->once()
			->andReturn(0);

		// Задача не удаляется из очереди
		$envelope
			->shouldReceive('delete')
			->never();

		// А отправляется обратно в очередь
		$envelope
			->shouldReceive('release')
			->with(20) // через 20 секунд задача попадёт обратно в воркер
			->once();

		Log::shouldReceive('error')
			->never();

		Log::shouldReceive('warning')
			->with(Mockery::on(function ($exception) {

				// Log::warning получил оригинальный Exception
				if ($exception instanceof Exception) {
					$this->assertEquals('Unknown exception', $exception->getMessage());

					return true;

				}

				return false;

			}), Mockery::any())
			->once();

		$job->fire($envelope, []);
	}
}