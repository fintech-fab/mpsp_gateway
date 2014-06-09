<?php
use FintechFab\MPSP\Services\Interfaces\TransferInterface;
use FintechFab\MPSP\Services\TransferService;
use Mockery\MockInterface;

/**
 * @property MockInterface                             $transfer
 * @property \FintechFab\MPSP\Services\TransferService $service
 */
class TransferServiceTest extends TestCase
{

	public function setUp()
	{
		parent::setUp();

		/* @var \FintechFab\MPSP\Services\Interfaces\TransferInterface $transfer */
		$transfer = $this->mock(TransferInterface::class);

		$this->transfer = $transfer;
		$this->service = new TransferService($transfer);
	}

	public function testFee()
	{
		$this->transfer->shouldReceive('fee')
			->with(1, 'RUR')
			->andReturn(true)
			->once()
			->ordered();

		$result = $this->service->fee(1, 'RUR');
		$this->assertSame(true, $result);
	}

	public function testFeeResult()
	{
		$this->transfer->shouldReceive('fee')
			->with(1, 'RUR')
			->andReturn(false)
			->once()
			->ordered();

		$result = $this->service->fee(1, 'RUR');
		$this->assertSame(false, $result);
	}

	public function testFeeLog()
	{
		$this->transfer->shouldReceive('fee')
			->with(1, 'RUR')
			->andReturn(false)
			->once();

		Log::shouldReceive('info')
			->twice();

		$this->service->fee(1, 'RUR');
	}

	/**
	 * Параметры метода TransferComponent::check() передаются в провайдер TransferInterface без изменений
	 */
	public function testCheckSuccessful()
	{
		$params = [
			'amount'   => 100,
			'currency' => 'RUR',
			'fee'      => 15,
		];

		$transferId = rand(100, 200);

		$this->transfer->shouldReceive('check')
			->with($transferId, $params)
			->once();

		$this->service->check($transferId, $params);
	}

	/**
	 * Метод TransferComponent::check делает две записи в лог с уровнем важности "info"
	 */
	public function testCheckSuccessfulLog()
	{
		$params = [
			'amount'   => 100,
			'currency' => 'RUR',
			'fee'      => 15,
		];

		$this->transfer->shouldReceive('check');

		// Как минимум два сообщения: начало работы метода check и его завершение
		Log::shouldReceive('info')
			->twice();

		$this->service->check(42, $params);
	}

	/**
	 * Результат провайдера TransferInterface возвращается методом TransferComponent::check()
	 */
	public function testCheckSuccessfulResult()
	{
		$params = [
			'amount'   => 100,
			'currency' => 'RUR',
			'fee'      => 15,
		];

		$transferId = 42;

		$this->transfer->shouldReceive('check')
			->with($transferId, $params)
			->once()
			->andReturn(true, false, 'checknumber');

		$this->service->check($transferId, $params);
	}

	/**
	 * При пустом параметре fee комиссия будет вычиссляться используя TransferInterface
	 */
	public function testCheckCalculateFee()
	{
		$params = [
			'transfer' => [
				'amount'   => 100,
				'currency' => 'RUR',
			],
			'receiver' => [
				'city' => 6,
			],
			// 'fee'      => 0, Параметр fee не передаётся умышленно
		];

		$transferId = 42;

		$this->transfer->shouldReceive('fee')
			->with($params['receiver']['city'], $params['transfer']['amount'])
			->once()
			->ordered()
			->andReturn('15');

		// Метод check получить рассчитанную комиссию
		$this->transfer->shouldReceive('check')
			->with($transferId, array_merge($params, ['fee' => '15']))
			->once()
			->ordered();

		$this->service->check($transferId, $params);
	}

	/**
	 * Запрос на получения статуса перевода
	 *
	 * Возвращаемое значение зависит от провайдера
	 */
	public function testStatusSuccessful()
	{
		$checkNumber = '52b01202c4b95';
		$receiverNumber = '79651234567';
		$transferId = 7;

		$this->transfer->shouldReceive('status')
			->with($transferId, $checkNumber, $receiverNumber)
			->twice()
			->andReturn(true, false);

		$this->assertTrue($this->service->status($transferId, $checkNumber, $receiverNumber));
		$this->assertFalse($this->service->status($transferId, $checkNumber, $receiverNumber));
	}

	public function testGetLastResponse()
	{
		$this->transfer->shouldReceive('getLastResponse')
			->andReturn(true, false)
			->twice();

		$this->assertTrue($this->service->getLastResponse());
		$this->assertFalse($this->service->getLastResponse());
	}

	public function testGetLastRequest()
	{
		$this->transfer->shouldReceive('getLastRequest')
			->andReturn(true, false)
			->twice();

		$this->assertTrue($this->service->getLastRequest());
		$this->assertFalse($this->service->getLastRequest());
	}
}
