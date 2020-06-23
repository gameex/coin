<?php

namespace api\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%balance_log}}".
 *
 */
class BalanceLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%balance_log}}';
    }

    /**
     * 行为插入时间戳
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ctime'],
                ],
            ],
        ];
    }
}
