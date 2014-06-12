<?php namespace FintechFab\MPSP\Facades;

use FintechFab\MPSP\Entities\Card;
use FintechFab\MPSP\Services\AcquiringService;
use FintechFab\MPSP\Services\Interfaces\AcquiringResultInterface;
use FintechFab\MPSP\Services\TransferService;
use Illuminate\Support\Facades\Facade;

class MPSP extends Facade
{

	/**
	 * Списать средства
	 *
	 * @param        $transferId
	 * @param Card   $card
	 * @param string $currency
	 * @param float  $amount
	 *
	 * @return AcquiringResultInterface
	 */
	public static function withdraw($transferId, Card $card, $currency, $amount)
	{
		return static::$app[AcquiringService::class]->doWithdraw($transferId, $card, $currency, $amount);
	}

	/**
	 * Вернуть средства
	 *
	 * @param int   $transferId
	 * @param array $additionalData
	 *
	 * @return bool
	 */
	public static function refund($transferId, array $additionalData = [])
	{
		return static::$app[AcquiringService::class]->doRefund($transferId, $additionalData);
	}

	/**
	 * Список городов
	 *
	 * @return array
	 */
	public static function cities()
	{
		return static::$app[TransferService::class]->cities();
	}

	/**
	 * Проверка возможности осуществления платежа
	 *
	 * @param int   $transferId
	 * @param array $data
	 *
	 * @return void
	 */
	public static function check($transferId, array $data)
	{
		static::$app[TransferService::class]->check($transferId, $data);
	}

	/**
	 * Вычислить стоимость комиссии
	 *
	 * @param int    $cityId
	 * @param string $amount
	 *
	 * @return float
	 */
	public static function fee($cityId, $amount)
	{
		return static::$app[TransferService::class]->fee($cityId, $amount);
	}

	/**
	 * Получить информацию о переводе
	 *
	 * @param int    $transferId Цифровой код (КНП)
	 * @param int    $checkNumber
	 * @param string $receiverNumber
	 *
	 * @return int
	 */
	public static function status($transferId, $checkNumber, $receiverNumber)
	{
		return static::$app[TransferService::class]->status($transferId, $checkNumber, $receiverNumber);
	}

	/**
	 * Отменить перевод
	 *
	 * @param string $checkNumber
	 * @param string $receiverNumber
	 *
	 * @return bool
	 */
	public static function cancel($checkNumber, $receiverNumber)
	{
		return static::$app[TransferService::class]->cancel($checkNumber, $receiverNumber);
	}

	protected static function getFacadeAccessor()
	{
		return self::class;
	}

} 