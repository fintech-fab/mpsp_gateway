<?php namespace FintechFab\MPSP\Commands\Transfer;

use FintechFab\MPSP\Constants\Status;
use Symfony\Component\Console\Input\InputArgument;

class StatusCommand extends AbstractCommand
{

	protected $name = 'transfer:status';
	protected $description = 'Получить состояние перевода';

	public function fire()
	{
		$transferId = $this->argument('transfer-id');
		$checkNumber = $this->argument('checknumber');
		$receiverNumber = $this->argument('receiver-number');

		$status = $this->transfer->status($transferId, $checkNumber, $receiverNumber);

		switch ($status) {

			case Status::C_SEND:
				$this->comment("Перевод <info>$checkNumber</info> ожидает выплаты");
				break;

			case Status::C_PAID:
				$this->comment("Перевод <info>$checkNumber</info> успешно выполнен");
				break;

			case Status::C_DELETE:
				$this->comment("Перевод <info>$checkNumber</info> не найден");
				break;

			case Status::C_NEW:
				$this->comment("Перевод <info>$checkNumber</info> ожидает отправки");
				break;

			default:
				$this->error("Перевод <info>$checkNumber</info> находится в неизвестном состоянии");
		}
	}

	protected function getArguments()
	{
		return [
			['transfer-id', InputArgument::REQUIRED, 'Номер транзакции'],
			['checknumber', InputArgument::REQUIRED, 'Цифровой код'],
			['receiver-number', InputArgument::REQUIRED, 'Номер телефона получателя'],
		];
	}

}

