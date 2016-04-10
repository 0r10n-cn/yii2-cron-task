<?php
namespace yii\cron\components\Cron;


use yii\base\Exception;
use yii\cron\CronException;
use yii\cron\models\Task;


class Cron extends \yii\base\Component{
	/** @var bool|array */
	protected static $taskHashes = false;

	public function isTaskHashExist($hash) {
		if (self::$taskHashes === false) {
			self::$taskHashes = Task::find()->select(['hash'])->where(['status' => Task::STATUS_NEW])->column();
		}
		return (bool)array_search($hash, self::$taskHashes);
	}


	public function create($name, $commandType, $command, $params, $minute = '*', $hour = '*', $day = '*', $month = '*', $weekDay = '*', $active = 1) {

	}

	public function delete($id) {

	}

	/**
	 * @param \yii\cron\models\Cron $cronModel
	 * @return Task
	 * @throws Exception
	 */
	public function createTaskFromCron(\yii\cron\models\Cron $cronModel) {
		return $this->createTask(
			$cronModel->type,
			$cronModel->command,
			$cronModel->params,
			$cronModel->priority,
			$cronModel->id,
			true
		);
	}

	public function createTask($type, $command, $params, $priority = Task::PRIORITY_MEDIUM, $cronID = null, $ifNotExists = true) {
		$task = new Task();
		$task->setScenario($ifNotExists ? Task::SCENARIO_NEW_IF_NOT_EXISTS : Task::SCENARIO_NEW);
		$task->setAttributes([
			'status' => Task::STATUS_NEW,
			'type' => $type,
			'task_name' => $command,
			'params' => $params,
			'cron_id' => $cronID,
			'priority' => $priority,
		]);

		if (!$task->save()) {
			throw new CronException($task->getFirstErrors()[0]);
		}
		self::$taskHashes[] = $task->hash;
		return $task;
	}
}