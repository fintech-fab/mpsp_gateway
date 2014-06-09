<?php namespace FintechFab\MPSP\Queue\Jobs;

use Exception;
use Illuminate\Queue\Jobs\Job;
use Log;
use Queue;

abstract class AbstractJob
{

	/**
	 * @var Job
	 */
	protected $job;

	/**
	 * @return \Illuminate\Queue\QueueInterface
	 */
	public function getAPIQueueInterface()
	{
		return Queue::connection('api');
	}

	public function fire(Job $job, array $data)
	{
		$this->job = $job;

		$context = [
			'context' => get_class($this),
			'data'    => $data,
		];

		try {

			Log::info('fire', $context);
			$this->run($data);

			Log::info('delete', $context);
			$job->delete();

		} catch (Exception $exception) {

			if ($job->attempts() > 3) {

				Log::error($exception, $context);
				$job->delete();

				$this->failure($data, $exception);

				return;
			}

			// перевыставляем с тайм-аутом в 20 сек.
			Log::warning($exception, $context);
			Log::info('Перевыставляем задачу через 20 секунд');
			$job->release(20);
		}
	}

	abstract public function run(array $requestData);

	protected function failure($data, Exception $exception = null)
	{

	}

}