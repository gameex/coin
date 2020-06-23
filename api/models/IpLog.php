<?php

namespace api\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "dl_ip_log".
 *
 * @property int $id
 * @property string $ip
 * @property int $updated_at 请求时的时间戳
 * @property int $times 请求次数
 */
class IpLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%ip_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ip'], 'required'],
            [['updated_at', 'times'], 'integer'],
            [['ip'], 'string', 'max' => 17],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ip' => 'Ip',
            'updated_at' => 'Updated At',
            'times' => 'Times',
        ];
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }
}
