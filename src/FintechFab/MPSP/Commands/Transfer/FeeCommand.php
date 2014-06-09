<?php namespace FintechFab\MPSP\Commands\Transfer;

use Symfony\Component\Console\Input\InputArgument;

class FeeCommand extends AbstractCommand
{

	protected $name = 'transfer:fee';
	protected $description = 'Получить значение комиссии';

	public function fire()
	{
		$cityId = $this->argument('city-id');
		$amount = $this->argument('amount');

		$cost = $this->transfer->fee($cityId, $amount);

		$this->info('Стоимость комиссии: ' . $cost);
	}

	protected function getArguments()
	{
		return [
			['amount', InputArgument::REQUIRED, 'Сумма перевода.'],
			['city-id', InputArgument::REQUIRED, 'Город (ID)'],
		];
	}

}