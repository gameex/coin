<?php

namespace api\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%varcode}}".
 *
 * @property string $id
 * @property string $mobile_phone 手机号
 * @property string $varcode
 * @property int $updated_at
 * @property string $ip ip
 * @property int $member_id 发送者用户ID
 */
class EmailCode extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%email_code}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
          //  [['varcode'], 'string', 'max' => 8],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
       return [
         //   'id' => 'ID',
        //    'mobile_phone' => 'Mobile Phone',
        //    'varcode' => 'Varcode',
 	  //'updated_at' => 'Update Time',
       //     'ip' => 'Ip',
       //     'member_id' => 'Member ID',
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
