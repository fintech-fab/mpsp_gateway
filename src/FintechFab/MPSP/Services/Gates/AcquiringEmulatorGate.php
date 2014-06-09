<?php namespace FintechFab\MPSP\Services\Gates;

use FintechFab\BankEmulatorSdk\GateWay;
use FintechFab\MPSP\Entities\Card;
use FintechFab\MPSP\Exceptions\AcquiringException;
use FintechFab\MPSP\Services\Gates\Results\EmulatorAcquiringResult;
use FintechFab\MPSP\Services\Interfaces\AcquiringInterface;
use FintechFab\MPSP\Services\Interfaces\AcquiringResultInterface;

class AcquiringEmulatorGate implements AcquiringInterface
{

	private $gateway;

	public function __construct(GateWay $gateway)
	{
		$this->gateway = $gateway;
	}

	/**
	 * Списание средств
	 *
	 * @param int                            $transferId
	 * @param \FintechFab\MPSP\Entities\Card $card
	 * @param string                         $currency
	 * @param float                          $amount
	 *
	 * @return AcquiringResultInterface
	 */
	public function doWithdraw($transferId, Card $card, $currency, $amount)
	{
		$success = $this->gateway->auth([
			'orderId'      => $transferId,
			'orderName'    => 'Order #' . $transferId,
			'orderDesc'    => '',
			'orderAmount'  => $amount,
			'cardNumber'   => $card->number,
			'expiredYear'  => $card->expire_year,
			'expiredMonth' => $card->expire_month,
			'cvcCode'      => $card->cvv,
		]);

		if (!$success) {
			return new EmulatorAcquiringResult(false);
		}

		$success = $this->gateway->complete([
			'orderId'     => $this->gateway->getResultOrderId(),
			'orderAmount' => $this->gateway->getResultAmount(),
			'rrn'         => $this->gateway->getResultRRN(),
			'irn'         => $this->gateway->getResultIRN(),
		]);

		if (!$success) {
			return new EmulatorAcquiringResult(false);
		}

		$responseData = [
			'orderId'     => $this->gateway->getResultOrderId(),
			'orderAmount' => $this->gateway->getResultAmount(),
			'rrn'         => $this->gateway->getResultRRN(),
			'irn'         => $this->gateway->getResultIRN(),
		];

		return new EmulatorAcquiringResult(true, $responseData, false, null, null);
	}

	/**
	 * Возврат списанных средств
	 *
	 * @param int   $transferId
	 * @param array $additionalData
	 *
	 * @return bool
	 */
	public function doRefund($transferId, array $additionalData = [])
	{
		$success = $this->gateway->refund([
			'orderId'     => $transferId,
			'orderAmount' => $additionalData['amount'],
			'rrn'         => $additionalData['rrn'],
			'irn'         => $additionalData['irn'],
		]);

		return new EmulatorAcquiringResult($success);
	}

	/**
	 * Обработать и завершить процедуру 3DS
	 *
	 * @param array $data
	 *
	 * @throws \FintechFab\MPSP\Exceptions\AcquiringException
	 * @return bool
	 */
	public function doFinish3DS(array $data)
	{
		throw new AcquiringException("not supported yet");
	}
}