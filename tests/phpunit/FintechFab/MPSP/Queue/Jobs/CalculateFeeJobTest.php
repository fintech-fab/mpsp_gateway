<?php
use FintechFab\MPSP\Queue\Jobs\CalculateFeeJob;
use FintechFab\MPSP\Services\TransferService;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Queue\QueueInterface;

/**
 * @property \Mockery\MockInterface|mixed $job
 * @property \Mockery\MockInterface       $transfer
 * @property \Mockery\MockInterface       oQueueManager
 * @property \Mockery\MockInterface       $queue
 * @property CalculateFeeJob              $calculateFeeJob
 */
class CalculateFeeJobTest extends TestCase
{

	public function setUp()
	{
		parent::setUp();

		$this->job = $this->mock(Job::class);
		$this->transfer = $this->mock(TransferService::class);
		$this->queue = $this->mock(QueueInterface::class);

		// подключаемся к очереди API
		Queue::shouldReceive('connection')
			->with('api')
			->andReturn($this->queue)
			->once()
			->ordered();

		Log::shouldReceive('info');

		$this->calculateFeeJob = new CalculateFeeJob($this->transfer);
	}

	/**
	 * получили задачу
	 * посчитали комиссию
	 * кинули задачу в API с результатом
	 * удалили текущую задачу
	 */
	public function testSuccess()
	{
		// Идентификатор в таблице transfer_costs
		$costId = 7;

		$params = [
			'amount'   => 100,
			'currency' => 'RUR',
			'cost_id'  => $costId,
			'city_id'  => 1,
		];

		$expectedCommission = 30;

		// начинаем считать комиссию
		$this->transfer->shouldReceive('fee')
			->with($params['city_id'], $params['amount'])
			->andReturn($expectedCommission)
			->once()
			->ordered();

		// ставим задачу в API с результатом
		$this->queue->shouldReceive('push')
			->withArgs([
				'calculateFeeResult',
				[
					'cost_id'    => $params['cost_id'],
					'city_id'    => $params['city_id'],
					'amount'     => $params['amount'],
					'currency'   => $params['currency'],
					'commission' => $expectedCommission,
				]
			])
			->once()
			->ordered();

		// удаляем задачу из очереди
		$this->job->shouldReceive('delete')
			->once()
			->ordered();

		$this->calculateFeeJob->fire($this->job, $params);
	}

	/**
	 * получили задачу
	 * попытались посчитать комиссию, но что-то пошло не так
	 * перевыставляем задачу с задержкой в 20 секунд
	 */
	public function testError()
	{
		$this->transfer->shouldReceive('fee')
			->andThrow(Exception::class);

		$this->job->shouldReceive('delete')->never();
		$this->queue->shouldReceive('push')->never();

		// логгируем ошибку
		Log::shouldReceive('warning')
			->once()
			->ordered();

		$this->job->shouldReceive('attempts')
			->once()
			->ordered();

		// перевыставляем задачу
		$this->job->shouldReceive('release')
			->with(20)
			->once()
			->ordered();

		$this->calculateFeeJob->fire($this->job, []);
	}

} 