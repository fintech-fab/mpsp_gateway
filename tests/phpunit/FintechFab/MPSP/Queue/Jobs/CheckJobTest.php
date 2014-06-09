<?php
use FintechFab\MPSP\Exceptions\TransferException;
use FintechFab\MPSP\Queue\Jobs\CheckJob;
use FintechFab\MPSP\Services\TransferService;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Queue\QueueInterface;
use Mockery\MockInterface;

/**
 * @property MockInterface|Job $job
 * @property MockInterface     $transfer
 * @property MockInterface     $queue
 * @property CheckJob          $checkJob
 */
class CheckJobTest extends TestCase
{

	public function setUp()
	{
		parent::setUp();

		$this->job = $this->mock(Job::class);
		$this->transfer = $this->mock(TransferService::class);
		$this->queue = $this->mock(QueueInterface::class);

		// подключаемся к очереди API
		Queue::shouldReceive('connection')
			->withArgs(array('api'))
			->andReturn($this->queue)
			->once()
			->ordered();

		$this->checkJob = new CheckJob($this->transfer);
	}

	public function testSuccess()
	{
		$checkNumber = '7c28525ab';
		$transferId = 101;

		$params = [
			'receiver' => [
				'name'      => '{NAME}',
				'thirdname' => '{THIRD NAME}',
				'surname'   => '{SURNAME}',
				'city'      => 'Киев',
			],
			'transfer' => [
				'id'       => $transferId,
				'amount'   => 1000,
				'fee'      => 30,
				'currency' => 'RUR',
			],
		];

		// начинаем считать комиссию
		$this->transfer->shouldReceive('check')
			->with($transferId, $params)
			->once()
			->ordered()
			->andReturn($checkNumber);

		$expectedResponse = [
			'transfer_id' => $transferId,
		];

		// Задача будет поставлена в API: transferCheckResult
		$this->queue->shouldReceive('push')
			->with('transferCheckResult', $expectedResponse)
			->once()
			->ordered();

		// удаляем задачу из очереди
		$this->job->shouldReceive('delete')
			->once()
			->ordered();

		$this->checkJob->fire($this->job, $params);
	}

	/**
	 *
	 * Во время проверки перевода происходит TransferException
	 * Это означает что провайдеру нехватает данных, либо накладываются какие-то ограничения на перевод
	 *
	 * Задача помечается как отработанная (метод delete из Queue)
	 * В логи попадает Warning
	 */
	public function testErrorTransferException()
	{
		$transferId = 101;
		$errorCode = 7;

		$errorMessage = 'Такого города не существует';

		$params = [
			'receiver' => [
				'name'      => '{NAME}',
				'thirdname' => '{THIRD NAME}',
				'surname'   => '{SURNAME}',
				'city'      => 'Тьмутаракань',
			],
			'transfer' => [
				'id'       => $transferId,
				'amount'   => 1000,
				'fee'      => 30,
				'currency' => 'RUR',
			],
			'card'     => []
		];

		// начинаем считать комиссию
		$this->transfer->shouldReceive('check')
			->with($transferId, $params)
			->andThrow(TransferException::class, $errorMessage, $errorCode)
			->once()
			->ordered();

		$expectedResponse = [
			'transfer_id' => $transferId,
			'error'       => [
				'code'    => $errorCode,
				'message' => $errorMessage
			],
		];

		// Задача будет поставлена в API: transferCheckResult
		$this->queue->shouldReceive('push')
			->with('transferCheckResult', $expectedResponse)
			->once()
			->ordered();

		// удаляем задачу из очереди
		$this->job->shouldReceive('delete')
			->once()
			->ordered();

		// Как минимум четыре сообщения info
		Log::shouldReceive('info')
			->atLeast()->times(4);

		// И одно warning
		Log::shouldReceive('warning')
			->with('CheckJob::run()', Mockery::on(function ($context) use ($transferId, $errorMessage) {
				$this->assertTrue(isset($context['transfer_id']));
				$this->assertTrue(isset($context['error']));

				$this->assertSame($transferId, $context['transfer_id']);
				$this->assertSame($errorMessage, $context['error']);

				return true;
			}))
			->once();

		$this->job
			->shouldReceive('attempts')
			->never();

		$this->checkJob->fire($this->job, $params);
	}

} 