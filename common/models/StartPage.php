<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "jl_start_page".
 *
 * @property int $id
 * @property string $title 标题
 * @property string $img 图片路径
 * @property string $url 链接
 * @property int $status 启用状态,默认0未启用,1启用,2已删除
 * @property int $add_time 添加时间
 */
class StartPage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jl_start_page';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['img'], 'string'],
            [['status', 'type', 'add_time'], 'integer'],
            [['title'], 'string', 'max' => 128],
            [['url'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '类型',
            'title' => '标题',
            'img' => '图片路径',
            'url' => '跳转链接',
            'status' => '是否启用',
            'add_time' => 'Add Time',
        ];
    }
}
