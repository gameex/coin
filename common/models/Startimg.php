<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "jl_startimg".
 *
 * @property int $id
 * @property string $title 标题
 * @property string $img 图片路径
 * @property string $url 链接
 * @property int $status 状态，默认0未启用，1启用，2已删除
 * @property int $add_time 添加时间
 */
class Startimg extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jl_startimg';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'add_time'], 'integer'],
            [['title'], 'string', 'max' => 128],
            [['img', 'url'], 'string', 'max' => 512],
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
            'img' => '图片路径',
            'url' => '链接',
            'status' => '状态',
            'add_time' => '添加时间',
        ];
    }
}
