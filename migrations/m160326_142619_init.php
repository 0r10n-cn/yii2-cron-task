<?php
namespace yii\cron\migrations;

use yii\db\Migration;

class m160326_142619_init extends Migration {

	public function up() {
		$this->createTable(
			'cron',
			[
				'id' => $this->integer(10)->unsigned(),
				'active' => $this->boolean()->unsigned()->notNull(),
				'name' => $this->string(256)->notNull(),
				'minute' => $this->string(64)->notNull(),
				'hour' => $this->string(64)->notNull(),
				'day' => $this->string(64)->notNull(),
				'month' => $this->string(64)->notNull(),
				'day_week' => $this->string(64)->notNull(),
				'type' => $this->string(32)->notNull(),
				'command' => $this->string(1024)->notNull(),
				'params' => $this->string(1024)->notNull(),
				'priority' => 'TINYINT unsigned NOT NULL',
				'PRIMARY KEY(`id`)',
 				'KEY `minute` (`minute`(5),`hour`(5),`day`(5),`month`(5),`day_week`(5))',
			],
			'ENGINE InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
		);

		$this->createTable(
			'task',
			[
				'id' => $this->integer(10)->unsigned(),
				'hash' => $this->string(40)->notNull(),
				'status' => "enum('new','queued','processing','complete','test','archive','error') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL",
				'date_created' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
				'date_updated' => 'timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\' ON UPDATE CURRENT_TIMESTAMP',
				'type' => $this->string(64)->notNull(),
				'task_name' => $this->string(128)->notNull(),
				'params' => $this->string(1024)->notNull(),
				'cron_id' => $this->integer()->unsigned()->defaultValue(null),
				'priority' => 'TINYINT unsigned NOT NULL',
				'PRIMARY KEY(`id`)',
			],
			'ENGINE InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
		);

		$this->createIndex('cron_id', 'task', 'cron_id');
		$this->createIndex('priority', 'task', ['priority', 'status']);
		$this->addForeignKey('fk_cron_id', 'task', 'cron_id', 'cron', 'id');

		return true;
	}

	public function down() {
		$this->dropTable('task');
		$this->dropTable('cron');

		return true;
	}
}
