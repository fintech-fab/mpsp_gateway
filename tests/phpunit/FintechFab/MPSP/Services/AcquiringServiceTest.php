<?php
use FintechFab\MPSP\Entities\Card;
use FintechFab\MPSP\Services\AcquiringService;
use FintechFab\MPSP\Services\Interfaces\AcquiringInterface;
use FintechFab\MPSP\Services\Interfaces\AcquiringResultInterface;

/**
 * @property \Mockery\MockInterface|AcquiringInterface                                         $gate
 * @property \Mockery\MockInterface|mixed                                                      $card
 * @property \Mockery\MockInterface                                                            $acquiringResult
 * @property AcquiringService                                                                  $acquiring
 */
class AcquiringServiceTest extends TestCase
{

	public function setUp()
	{
		parent::setUp();

		$this->gate = $this->mock(AcquiringInterface::class);
		$this->acquiringResult = $this->mock(AcquiringResultInterface::class);
		$this->card = $this->mock(Card::class);
		$this->acquiring = new AcquiringService($this->gate);
	}

	public function testDoWithdraw()
	{
		$currency = 'RUR';
		$amount = 500;

		$transferId = 71;

		// вызвано списание
		$this->gate->shouldReceive('doWithdraw')
			->with($transferId, $this->card, $currency, $amount)
			->andReturn($this->acquiringResult)
			->once()
			->ordered();

		$this->acquiringResult->shouldReceive('isSuccess')
			->andReturn(true);

		$result = $this->acquiring->doWithdraw($transferId, $this->card, $currency, $amount);

		$this->assertTrue($result->isSuccess());
	}

	public function testDoRefund()
	{
		$transferId = 1;
		$intRef = 'dsdasdasd';
		$RRN = 'sdlas;dkas';
		$currency = 'RUB';
		$amount = 1000;

		$additionalData = [
			'INT_REF'  => $intRef,
			'RRN'      => $RRN,
			'CURRENCY' => $currency,
			'AMOUNT'   => $amount,
		];

		// вызван возврат
		$this->gate->shouldReceive('doRefund')
			->with($transferId, $additionalData)
			->andReturn(true)
			->once()
			->ordered();

		$success = $this->acquiring->doRefund($transferId, $additionalData);

		$this->assertTrue($success);
	}
} 