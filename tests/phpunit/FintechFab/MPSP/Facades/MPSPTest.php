<?php

use FintechFab\MPSP\Entities\Card;
use FintechFab\MPSP\Services\AcquiringService;
use FintechFab\MPSP\Services\TransferService;
use Mockery\MockInterface;

/**
 * @property MockInterface|mixed transfer
 * @property MockInterface|mixed acquiring
 */
class MPSPTest extends TestCase
{

	public function setUp()
	{
		parent::setUp();
		$this->transfer = $this->mock(TransferService::class);
		$this->acquiring = $this->mock(AcquiringService::class);
	}

	public function test_getFacadeRoot()
	{
		$this->assertEquals(\FintechFab\MPSP\Facades\MPSP\BaseMPSP::class, get_class(MPSP::getFacadeRoot()));
	}

	public function test_withdraw()
	{
		$transferId = 1;
		$card = $this->mock(Card::class);
		$currency = 'RUR';
		$amount = 123.45;
		$returnValue = Str::random();

		$this->acquiring->shouldReceive('doWithdraw')
			->with($transferId, $card, $currency, $amount)
			->andReturn($returnValue)
			->once();

		$result = MPSP::withdraw($transferId, $card, $currency, $amount);

		$this->assertEquals($returnValue, $result);
	}

	public function test_refund()
	{
		$transferId = 5;
		$additionalData = ['key1' => 'value1', 'key2' => 'value2'];
		$returnValue = Str::random();

		$this->acquiring->shouldReceive('doRefund')
			->with($transferId, $additionalData)
			->andReturn($returnValue)
			->once();

		$result = MPSP::refund($transferId, $additionalData);

		$this->assertEquals($returnValue, $result);
	}

	public function test_cities()
	{
		$returnValue = Str::random();

		$this->transfer->shouldReceive('cities')
			->andReturn($returnValue)
			->once();

		$result = MPSP::cities();

		$this->assertEquals($returnValue, $result);
	}

	public function test_check()
	{
		$transferId = 312312;
		$data = ['dsaa', 'dszdsz'];

		$this->transfer->shouldReceive('check')
			->with($transferId, $data)
			->once();

		MPSP::check($transferId, $data);
	}

	public function test_fee()
	{
		$cityId = 3;
		$amount = 231.11;
		$returnValue = Str::random();

		$this->transfer->shouldReceive('fee')
			->with($cityId, $amount)
			->andReturn($returnValue)
			->once();

		$result = MPSP::fee($cityId, $amount);

		$this->assertEquals($returnValue, $result);
	}

	public function test_status()
	{
		$transferId = 321312;
		$checkNumber = Str::random();
		$receiverNumber = '321321321';
		$returnValue = Str::random();

		$this->transfer->shouldReceive('status')
			->with($transferId, $checkNumber, $receiverNumber)
			->once()
			->andReturn($returnValue);

		$result = MPSP::status($transferId, $checkNumber, $receiverNumber);

		$this->assertEquals($returnValue, $result);
	}

	public function test_cancel()
	{
		$checkNumber = Str::random();
		$receiverNumber = '32019321321';
		$returnValue = Str::random();

		$this->transfer->shouldReceive('cancel')
			->with($checkNumber, $receiverNumber)
			->andReturn($returnValue)
			->once();

		$result = MPSP::cancel($checkNumber, $receiverNumber);

		$this->assertEquals($returnValue, $result);
	}

} 