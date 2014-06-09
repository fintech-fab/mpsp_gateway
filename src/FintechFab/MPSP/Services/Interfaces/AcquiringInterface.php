<?php namespace FintechFab\MPSP\Services\Interfaces;

use FintechFab\MPSP\Entities\Card;

interface AcquiringInterface
{

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
	public function doWithdraw($transferId, Card $card, $currency, $amount);

	/**
	 * Возврат списанных средств
	 *
	 * @param int   $transferId
	 * @param array $additionalData
	 *
	 * @return bool
	 */
	public function doRefund($transferId, array $additionalData = []);

	/**
	 * Обработать и завершить процедуру 3DS
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function doFinish3DS(array $data);

} 