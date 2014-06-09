<?php namespace FintechFab\MPSP\Commands\Transfer;

use FintechFab\MPSP\Services\TransferService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractCommand extends Command
{
	/**
	 * @var TransferService
	 */
	protected $transfer;

	/**
	 * Create a new command instance.
	 *
	 * @param \FintechFab\MPSP\Services\TransferService $transfer
	 */
	public function __construct(TransferService $transfer)
	{
		parent::__construct();
		$this->transfer = $transfer;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		try {

			parent::execute($input, $output);

		} finally {

			// Вывести на экран запрос/ответ к удалённому серверу
			// Если запросов было несколько то выводится последний
			if ($this->output->getVerbosity() > 1) {

				$request = $this->transfer->getLastRequest();
				$response = $this->transfer->getLastResponse();

				$this->info('Request');
				$this->line($request);
				$this->info('Response');
				$this->line($response);

			}

		}
	}

}