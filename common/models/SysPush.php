<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "jl_sys_push".
 *
 * @property int $id
 * @property string $type 推送类型,0 URL
 * @property string $title 推送标题
 * @property string $object 推送内容
 * @property int $status 状态，默认 0推送失败 1推送成功，2已删除
 * @property int $add_time 推送时间
 */
class SysPush extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jl_sys_push';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'add_time'], 'integer'],
            [['type'], 'string', 'max' => 16],
            [['title'], 'string', 'max' => 128],
            [['object'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '推送类型',
            'title' => '标题',
            'object' => '推送内容',
            'status' => '状态',
            'add_time' => '推送时间',
        ];
    }
}
