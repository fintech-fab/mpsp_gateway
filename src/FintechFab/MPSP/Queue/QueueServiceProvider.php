<?php namespace FintechFab\MPSP\Queue;

use FintechFab\MPSP\Queue\Jobs\AcquiringFinish3DSJob;
use FintechFab\MPSP\Queue\Jobs\AcquiringJob;
use FintechFab\MPSP\Queue\Jobs\AcquiringRefundJob;
use FintechFab\MPSP\Queue\Jobs\CalculateFeeJob;
use FintechFab\MPSP\Queue\Jobs\CancelJob;
use FintechFab\MPSP\Queue\Jobs\CheckJob;
use FintechFab\MPSP\Queue\Jobs\CitiesListJob;
use FintechFab\MPSP\Queue\Jobs\SendJob;
use FintechFab\MPSP\Queue\Jobs\SmsJob;
use FintechFab\MPSP\Queue\Jobs\StatusJob;
use Illuminate\Support\ServiceProvider;

class QueueServiceProvider extends ServiceProvider
{

	const C_CITIES_LIST = 'citiesList';
	const C_CALCULATE_FEE = 'calculateFee';
	const C_TRANSFER_CHECK = 'transferCheck';
	const C_TRANSFER_SEND = 'transferSend';
	const C_TRANSFER_STATUS = 'transferStatus';
	const C_TRANSFER_CANCEL = 'transferCancel';
	const C_ACQUIRING = 'acquiring';
	const C_ACQUIRING_FINISH_3DS = 'acquiringFinish3DS';
	const C_ACQUIRING_REFUND = 'acquiringRefund';
	const C_SMS_SEND = 'sms';

	public function register()
	{

		// список городов
		$this->app->bind(self::C_CITIES_LIST, CitiesListJob::class);

		// посчитать комиссию
		$this->app->bind(self::C_CALCULATE_FEE, CalculateFeeJob::class);

		// проверка возможности осуществления перевода
		$this->app->bind(self::C_TRANSFER_CHECK, CheckJob::class);

		// осуществить перевод денежных средств получателю
		$this->app->bind(self::C_TRANSFER_SEND, SendJob::class);

		// узнать статус перевода
		$this->app->bind(self::C_TRANSFER_STATUS, StatusJob::class);

		// отменить перевод
		$this->app->bind(self::C_TRANSFER_CANCEL, CancelJob::class);

		// снятие средств
		$this->app->bind(self::C_ACQUIRING, AcquiringJob::class);

		// отмена транзакции
		$this->app->bind(self::C_ACQUIRING_REFUND, AcquiringRefundJob::class);

		// завершение 3DS
		$this->app->bind(self::C_ACQUIRING_FINISH_3DS, AcquiringFinish3DSJob::class);

		// отправить смс
		$this->app->bind(self::C_SMS_SEND, SmsJob::class);
	}
}