<?php namespace FintechFab\MPSP\Services\Gates;

use FintechFab\MoneyTransferEmulatorSdk\Gateway;
use FintechFab\MPSP\Constants\Status;
use FintechFab\MPSP\Exceptions\TransferException;
use FintechFab\MPSP\Services\Interfaces\TransferInterface;
use Log;

class TransferEmulatorGate implements TransferInterface
{

	const PROCESSED = 'processed';
	const ERROR = 'error';
	const SENT = 'sent';
	const TRANSFERED = 'transfered';
	const CANCELED = 'canceled';
	const ENABLED = 'enabled';
	const DISABLED = 'disabled';
	const PAY = 'pay';

	private $gateway;

	public function __construct(Gateway $gateway)
	{
		$this->gateway = $gateway;
	}

	public function cities()
	{
		$cities = $this->gateway->getCityList();
		if(!$cities){
			Log::warning('Fetch empty city list');
			Log::info('Gate response', $this->gateway->getResultRaw());
			Log::info('Gate message', $this->gateway->getResultMessage());
			return array();
		}
		return $cities;
	}

	public function fee($cityId, $amount)
	{
		$result = $this->gateway->fee($cityId, $amount);

		if (!$result) {
			$this->logError();

			throw new TransferException($this->gateway->getError() . ' ' . $this->gateway->getErrorType());
		}

		return $this->gateway->getResultFeeValue();
	}

	private function logError()
	{
		Log::error('TransferEmulatorGateError', [
			$this->gateway->getError(),
			$this->gateway->getErrorType(),
			$this->gateway->getResultMessage(),
		]);
	}

	public function check($transferId, $data)
	{
		$receiver = $data['receiver'];

		$toName = $receiver['name'] . ' ' . $receiver['surname'] . ' ' . $receiver['thirdname'];

		$result = $this->gateway->check([
			'cityId'      => $data['receiver']['city'],
			'toName'      => $toName,
			'orderAmount' => $data['transfer']['amount'],
			'fromNumber'  => $data['sender']['phone'],
			'toNumber'    => $data['receiver']['phone'],
		]);

		if (!$result) {
			$this->logError();

			throw new TransferException($this->gateway->getError() . ' ' . $this->gateway->getErrorType());
		}

		if ($this->gateway->getResultStatus() !== self::ENABLED) {
			$this->logError();

			throw new TransferException;
		}

		return null;
	}

	public function status($transferId, $checkNumber, $receiverNumber)
	{
		$result = $this->gateway->status($checkNumber, $receiverNumber);

		if (!$result) {
			$this->logError();

			throw new TransferException($this->gateway->getError() . ' ' . $this->gateway->getErrorType());
		}

		switch ($this->gateway->getResultStatus()) {
			case self::PROCESSED:

				return Status::C_NEW;

			case self::ERROR:

				return Status::C_DELETE;

			case self::SENT:

				return Status::C_SEND;

			case self::TRANSFERED:

				return Status::C_PAID;

			case self::CANCELED:

				return Status::C_DELETE;
		}

		return null;
	}

	public function cancel($checkNumber, $receiverNumber)
	{
		$result = $this->gateway->cancel($checkNumber, $receiverNumber);

		if (!$result) {
			$this->logError();

			throw new TransferException($this->gateway->getError(), $this->gateway->getErrorType());
		}

		if ($this->gateway->getResultStatus() !== self::PROCESSED) {
			$this->logError();

			throw new TransferException;
		}

		return true;
	}

	public function pay($transferId, $data)
	{
		$receiver = $data['receiver'];

		$toName = $receiver['name'] . ' ' . $receiver['surname'] . ' ' . $receiver['thirdname'];

		$result = $this->gateway->pay([
			'cityId'      => $data['receiver']['city'],
			'toName'      => $toName,
			'orderAmount' => $data['transfer']['amount'],
			'fromNumber'  => $data['sender']['phone'],
			'toNumber'    => $data['receiver']['phone'],
		]);

		if (!$result) {
			$this->logError();

			throw new TransferException($this->gateway->getError() . ' ' . $this->gateway->getErrorType());
		}

		if ($this->gateway->getResultStatus() !== self::PROCESSED) {
			$this->logError();

			throw new TransferException;
		}

		return $this->gateway->getResultCode();
	}

	public function getLastRequest()
	{
		return null;
	}

	public function getLastResponse()
	{
		return null;
	}

}