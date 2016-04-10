<?php

namespace yii\cron;

use Yii;
use yii\base\BootstrapInterface;

class Module extends \yii\base\Module implements BootstrapInterface {
	/**
	 * @inheritdoc
	 */
	public $controllerNamespace = 'yii\cron\controllers';

	/**
	 * @inheritdoc
	 */
	public function bootstrap($app) {
		if ($app instanceof \yii\web\Application) {
			/*$app->getUrlManager()->addRules([
				['class' => 'yii\web\UrlRule', 'pattern' => $this->id, 'route' => $this->id . '/default/index'],
				['class' => 'yii\web\UrlRule', 'pattern' => $this->id . '/<id:\w+>', 'route' => $this->id . '/default/view'],
				['class' => 'yii\web\UrlRule', 'pattern' => $this->id . '/<controller:[\w\-]+>/<action:[\w\-]+>', 'route' => $this->id . '/<controller>/<action>'],
			], false);*/
		} elseif ($app instanceof \yii\console\Application) {
			$app->controllerMap[$this->id] = [
				'class'      => 'yii\cron\console\GenerateController',
				'module'     => $this,
			];
		}
	}

	/**
	 * @inheritdoc
	 */
	public function beforeAction($action) {
		if (!parent::beforeAction($action)) {
			return false;
		}

		$this->resetGlobalSettings();

		return true;
	}

	/**
	 * Resets potentially incompatible global settings done in app config.
	 */
	protected function resetGlobalSettings() {
		if (Yii::$app instanceof \yii\web\Application) {
			Yii::$app->assetManager->bundles = [];
		}
	}

	/**
	 * @return boolean whether the module can be accessed by the current user
	 */
	protected function checkAccess() {
		return true;
	}


	protected function coreGenerators()
	{
		return [
			'scheduler' => ['class' => 'yii\cron\console\GenerateController '],
		];
	}

}
