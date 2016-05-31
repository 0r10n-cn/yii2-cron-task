<?php
namespace yii\cron\console;

use yii\cron\models\Cron;
use yii\cron\models\Task;
use yii\mutex\FileMutex;

class CronController extends \yii\console\Controller{
	protected $mutex;

	public function actionIndex() {
		$this->mutex = new FileMutex();
		$this->mutex->acquire(\Yii::$app->id.__CLASS__);
		$cronJobs = Cron::findBySql('
			SELECT
				*
			FROM
				`cron`
			WHERE
				(
					`minute` in ("*", "*\/1", minute(now()))
					or (
						`minute` REGEXP "^\\\\*\\\\/[0-9]$"
						and if( minute( now( ) ) = 0, 60, minute( now( ) ) ) % replace(`minute`, "*/", "" ) = 0
					)
				)
				and (`hour` in ("*", "*\/1", hour(now()))
						or (
						`hour` REGEXP "^\\\\*\\\\/[0-9]$"
						and if( hour( now( ) ) = 0, 60, hour( now( ) ) ) % replace(`hour`, "*/", "" ) = 0
					)
				)
				and (
					`day` in ("*", "*\/1", day(now()))
					or `day` % replace(`day`, "*/", "" ) = 0
				)
				and (
					`month` in ("*", "*\/1", month(now()))
					or `month` % replace(`month`, "*/", "" ) = 0

				)
				and `active` = 1')
			->all();

		foreach ($cronJobs as $job) {
			\Yii::$app->cron->createTaskFromCron($job);
		}
		$cronJobs = [];
		\Yii::$app->runAction('cron/cron/process-tasks');
/*		$task = new Task();
		foreach ($cronJobs as $job) {
			$TaskQueue = $task->createTask();
			$TaskQueue->create($job->command, $job->params, $job->type, $job->command, $job->id);
		}
		unset($TaskQueue);
		/** @var Entity $tsk */
		/*foreach ($task->findAllNewTasks() as $tsk) {
			try {
				$tsk->getCommand()->execute();
				$tsk->complete();
			} catch (\Exception $e) {

				$tsk->error();
			}
		}*/
		$this->mutex->release(\Yii::$app->id.__CLASS__);
	}

	public function actionProcessTasks() {
		/** @var Task[] $tasks */
		$tasks = Task::find()->where(['status' => 'new'])->orderBy(['priority' => SORT_DESC])->all();

		foreach ($tasks as $task) {
			$task->process();
		}
	}

	/**
	 * I`m gonna nothing to do
	 */
	public function actionTest() {

	}
}