<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "message".
 *
 * @property int $id 主键id
 * @property string $title 消息标题
 * @property string $content 消息内容
 * @property int $uid 指定用户id,仅type为1时有效
 * @property int $type 0 公告 1单个用户消息
 * @property int $status 消息状态 0删除 1正常
 * @property int $add_time 添加时间
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%message}}';
    }

    /**
     * {@inheritdoc}
            [['title', 'content'], 'required'],
            [['uid', 'type', 'status', 'add_time'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['content'], 'string', 'max' => 1000],
     */
    public function rules()
    {
        return [


        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
            'uid' => 'Uid',
            'type' => 'Type',
            'status' => 'Status',
            'add_time' => 'Add Time',
        ];
    }
}
