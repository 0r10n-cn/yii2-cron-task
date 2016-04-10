<?php

namespace yii\cron\models;

use Yii;

/**
 * This is the model class for table "task".
 *
 * @property integer $id
 * @property string $hash
 * @property string $status
 * @property string $date_created
 * @property string $date_updated
 * @property string $type
 * @property string $task_name
 * @property string $params
 * @property integer $cron_id
 * @property integer $priority
 *
 * @property Cron $cron
 */
class Task extends \yii\db\ActiveRecord
{
    const SCENARIO_NEW = 'new';
    const SCENARIO_NEW_IF_NOT_EXISTS = 'newNotExist';

    const PRIORITY_LOW = 64;
    const PRIORITY_MEDIUM = 128;
    const PRIORITY_HIGH = 224;
    const PRIORITY_HIGHES = 256;

    const STATUS_NEW = 'new';
    const STATUS_QUEUED = 'queued';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETE = 'complete';
    const STATUS_TEST = 'test';
    const STATUS_ARCHIVE = 'archive';
    const STATUS_ERROR = 'error';

    const TYPE_YII = 'Yii';
    const TYPE_CALLBACK = 'Callback';
    const TYPE_BASH = 'Bash';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'task';
    }

    public function scenarios() {
        return [
            self::SCENARIO_NEW => ['hash', 'status', 'type', 'task_name', 'params', 'priority'],
            self::SCENARIO_NEW_IF_NOT_EXISTS => ['hash', 'status', 'type', 'task_name', 'params', 'priority'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'task_name'], 'required'],
            ['hash', function () {
                $this->hash = sha1(implode('', [$this->task_name, $this->params]));
            }],
            ['priority', 'default', 'value' => self::PRIORITY_MEDIUM],
            [['id', 'cron_id', 'priority'], 'integer'],
            ['priority', 'number', 'min' => 0, 'max' => 255 ],
            [['status'], 'in' ,
                'range' => [
                    self::STATUS_NEW,
                    self::STATUS_QUEUED,
                    self::STATUS_PROCESSING,
                    self::STATUS_COMPLETE,
                    self::STATUS_TEST,
                    self::STATUS_ARCHIVE,
                    self::STATUS_ERROR,
                ]
            ],
            [['hash'], 'string', 'max' => 40],
            [['type'], 'string', 'max' => 64],
            [['task_name'], 'string', 'max' => 128],
            [['params'], 'string', 'max' => 1024],
            [['cron_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cron::className(), 'targetAttribute' => ['cron_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hash' => 'Hash',
            'status' => 'Status',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'type' => 'Type',
            'task_name' => 'Task name',
            'params' => 'Params',
            'cron_id' => 'Cron ID',
            'priority' => 'Priority',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCron()
    {
        return $this->hasOne(Cron::className(), ['id' => 'cron_id']);
    }

    public static function makeHash($taskName, $params) {
        return sha1(
            implode('', [$taskName, $params])
        );
    }

    public function beforeValidate() {
        if (empty($this->hash)) {
            $this->hash = self::makeHash($this->task_name, $this->params);
        }

        return parent::beforeValidate();
    }
}
