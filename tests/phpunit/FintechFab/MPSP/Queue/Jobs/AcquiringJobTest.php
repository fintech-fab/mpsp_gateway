<?php
use FintechFab\MPSP\Entities\Card;
use FintechFab\MPSP\Exceptions\AcquiringException;
use FintechFab\MPSP\Queue\Jobs\AcquiringJob;
use FintechFab\MPSP\Services\Interfaces\AcquiringResultInterface;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Queue\QueueInterface;

/**
 * @property \Mockery\MockInterface                                                                                            $queue
 * @property \Mockery\MockInterface                                                                                            $acquiringResult
 * @property \Mockery\MockInterface                                                                                            $card
 * @property \Mockery\MockInterface|mixed                                                                                      $job
 * @property AcquiringJob                                                                                                      $acquiringJob
 */
class AcquiringJobTest extends TestCase
{

	public function setUp()
	{
		parent::setUp();

		$this->queue = $this->mock(QueueInterface::class);
		$this->acquiringResult = $this->mock(AcquiringResultInterface::class);
		$this->card = $this->mock(Card::class);
		$this->job = $this->mock(Job::class);

		Queue::shouldReceive('connection')
			->with('api')
			->andReturn($this->queue)
			->once()
			->ordered();

		$this->acquiringJob = new AcquiringJob($this->card);
	}

	/**
	 * успешное снятие средств
	 */
	public function testSuccess()
	{
		$transferId = 11;
		$currency = 'RUB';
		$amount = 1000;
		$fee = 30;

		$cardNumber = '321321321321';
		$expireMonth = 04;
		$expireYear = 15;
		$CVV = 331;

		$irn = '3dsalkdkcjf192eic';
		$rrn = 'dsz;kjdlu12co21';

		// расшифровываем данные карты
		$this->card->shouldReceive('doImport');

		// снимаем деньги
		MPSP::shouldReceive('withdraw')
			->with($transferId, $this->card, $currency, $amount + $fee)
			->andReturn($this->acquiringResult)
			->once()
			->ordered();

		$this->acquiringResult->shouldReceive('isNeed3DS')
			->andReturn(true)
			->once()
			->ordered();

		$this->acquiringResult->shouldReceive('get3DSUrl')
			->andReturn('some_url')
			->once()
			->ordered();

		$this->acquiringResult->shouldReceive('get3DSData')
			->andReturn('some_data')
			->once()
			->ordered();

		$this->acquiringResult->shouldReceive('getResponseData')
			->twice()
			->andReturn([
				'irn' => $irn,
				'rrn' => $rrn,
			]);

		// кладем в очередь API результат
		$this->queue->shouldReceive('push')
			->with('acquiringResult', [
				'transfer_id'   => $transferId,
				'need_3ds'      => true,
				'3ds_url'       => 'some_url',
				'3ds_post_data' => 'some_data',
				'irn' => $irn,
				'rrn' => $rrn,
			])
			->once()
			->ordered();

		// удаляем текущую задачу
		$this->job->shouldReceive('delete')
			->once()
			->ordered();

		$this->acquiringJob->fire($this->job, [

			'transfer' => [
				'id'       => $transferId,
				'amount'   => $amount,
				'fee'      => $fee,
				'currency' => $currency,
			],

			'card'     => [
				'number'       => $cardNumber,
				'expire_month' => $expireMonth,
				'expire_year'  => $expireYear,
				'cvv'          => $CVV,
			],

		]);
	}

	/**
	 * ошибка списания средств
	 */
	public function testFail()
	{
		$this->card->shouldReceive('doImport');

		MPSP::shouldReceive('withdraw')
			->andThrow(AcquiringException::class, 'exception_message', 111);

		$this->queue->shouldReceive('push')
			->withArgs([
				'acquiringResult', [
					'transfer_id' => 11,
					'error'       => [
						'code'    => 111,
						'message' => 'exception_message',
					],
					'need_3ds'    => false,
					'3ds_url'     => '',
				]
			])
			->once()
			->ordered();

		$this->job->shouldReceive('delete')
			->once()
			->ordered();

		$this->acquiringJob->fire($this->job, [

			'transfer' => [
				'id'       => 11,
				'amount'   => 321,
				'fee'      => 321,
				'currency' => 'RUR',
			],

			'card'     => [
				'number'       => '32132321',
				'expire_month' => '11',
				'expire_year'  => '14',
				'cvv'          => 123,
			],

		]);
	}

} 