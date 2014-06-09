<?php namespace FintechFab\MPSP\Services;

use FintechFab\MPSP\Entities\Card;
use FintechFab\MPSP\Exceptions\AcquiringException;
use FintechFab\MPSP\Services\Interfaces\AcquiringInterface;
use FintechFab\MPSP\Services\Interfaces\AcquiringResultInterface;

class AcquiringService
{

	private $acquiring;

	public function __construct(AcquiringInterface $acquiring)
	{
		$this->acquiring = $acquiring;
	}

	/**
	 * снять средства
	 *
	 * @param int    $transferId
	 * @param Card   $card
	 * @param string $currency
	 * @param float  $amount
	 *
	 * @return AcquiringResultInterface
	 */
	public function doWithdraw($transferId, Card $card, $currency, $amount)
	{
		return $this->acquiring->doWithdraw($transferId, $card, $currency, $amount);
	}

	/**
	 * вернуть списанные средства
	 *
	 * @param int   $transferId
	 * @param array $additionalData
	 *
	 * @return bool
	 */
	public function doRefund($transferId, array $additionalData = [])
	{
		return $this->acquiring->doRefund($transferId, $additionalData);
	}

	/**
	 * Обработать и завершить процедуру 3DS
	 *
	 * @param $data
	 *
	 * @throws AcquiringException
	 *
	 * @return bool
	 */
	public function doFinish3DS(array $data)
	{
		return $this->acquiring->doFinish3DS($data);
	}

}