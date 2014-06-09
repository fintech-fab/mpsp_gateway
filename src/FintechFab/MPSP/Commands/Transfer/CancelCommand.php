<?php namespace FintechFab\MPSP\Commands\Transfer;

use Symfony\Component\Console\Input\InputArgument;

/**
 * Class CancelCommand
 *
 * Отмена денежного перевода
 *
 * @package Command\Transfer
 */
class CancelCommand extends AbstractCommand
{

	protected $name = 'transfer:cancel';
	protected $description = 'Отменить денежный перевод.';

	public function fire()
	{
		$checkNumber = $this->argument('checknumber');
		$receiverNumber = $this->argument('receiver-number');

		$this->transfer->cancel($checkNumber, $receiverNumber);
	}

	protected function getArguments()
	{
		return [
			['checknumber', InputArgument::REQUIRED, 'Цифровой код'],
			['receiver-number', InputArgument::REQUIRED, 'Номер телефона получателя'],
		];
	}

}