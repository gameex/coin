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
class Article extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jl_sys_article';
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
            'id' => 'ID',
            'manager_id' => '用户ID',
            'title' => '标题',
            'name' => '标识',
            'cover' => '封面',
            'seo_key' => 'seo关键字',
            'seo_content' => 'seo内容',
            'cate_id' => '分类id',
            'description' => '描述',
            'position' => '推荐位',
            'content' => '文章内容',
            'link' => '外链',
            'deadline' => '截至时间',
            'author' => '作者',
            'view' => '浏览量',
            'comment' => '评论数',
            'bookmark' => '收藏数',
            'incontent' => '1显示在文章中',
            'sort' => '优先级',
            'status' => '状态',
            'append' => '创建时间',
            'updated' => '修改时间',
        ];
    }
}
