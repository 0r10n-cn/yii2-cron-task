<?php

namespace yii\cron\models;

use Yii;

/**
 * This is the model class for table "cron".
 *
 * @property integer $id
 * @property integer $active
 * @property string $name
 * @property string $minute
 * @property string $hour
 * @property string $day
 * @property string $month
 * @property string $day_week
 * @property string $type
 * @property string $command
 * @property string $params
 * @property integer $priority
 *
 * @property Task[] $tasks
 */
class Cron extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cron';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'active', 'name', 'minute', 'hour', 'day', 'month', 'day_week', 'type', 'command', 'params', 'priority'], 'required'],
            [['id', 'active', 'priority'], 'integer'],
            [['name'], 'string', 'max' => 256],
            [['minute', 'hour', 'day', 'month', 'day_week'], 'string', 'max' => 64],
            [['type'], 'string', 'max' => 32],
            [['command', 'params'], 'string', 'max' => 1024],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'active' => 'Active',
            'name' => 'Name',
            'minute' => 'Minute',
            'hour' => 'Hour',
            'day' => 'Day',
            'month' => 'Month',
            'day_week' => 'Day Week',
            'type' => 'Type',
            'command' => 'Command',
            'params' => 'Params',
            'priority' => 'Priority',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::className(), ['cron_id' => 'id']);
    }
}
