<?php

namespace api\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "member_feedback".
 *
 * @property int $id
 * @property tinyint $type
 * @property int $member_id
 * @property text $content
 * @property varchar $thumb
 * @property int $created_at
 * @property tinyint $status
 * @property tinyint $del_status
 * @property text $reply
 * @property integer $reply_at
 * @property integer $manager_id
*/
class Feedback extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%member_feedback}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'member_id', 'content'], 'required'],
            [['member_id', 'created_at', 'reply_at'], 'integer'],
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['reply_at'],
                ],
            ],
        ];
    }
}
