<?php

namespace common\models;

use common\models\member\Member;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "jl_message".
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
        return 'jl_message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['uid', 'type', 'status', 'add_time'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['content'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'content' => '内容',
            'uid' => '用户',
            'type' => '类型',
            'status' => 'Status',
            'add_time' => 'Add Time',
        ];
    }

    public function getUser(){
        return $this->hasOne(Member::className(),['id' => 'uid']);
    }

    public function beforeSave($insert)
    {
        if($this->isNewRecord)
        {
            $this->add_time = time();
        }

        return parent::beforeSave($insert);
    }

    public static function getUsers(){
        $data = Member::find()->select('id,nickname')->asArray()->all();
        return ArrayHelper::map($data, 'id', 'nickname');
    }
}
