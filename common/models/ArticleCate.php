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
class ArticleCate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jl_sys_article_cate';
    }

    /**
     * {@inheritdoc}
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
            
           
        ];
    }
}
