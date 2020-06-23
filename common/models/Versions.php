<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "jl_versions".
 *
 * @property int $id
 * @property int $type 类型，1Android，2Ios
 * @property string $ver_num 版本号
 * @property string $package_url APP下载链接
 * @property string $description 描述
 * @property string $add_time 添加时间
 * @property int $status 版本状态，默认0未启用，1启用
 */
class Versions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jl_versions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'status'], 'integer'],
            [['ver_num'], 'required'],
            [['description'], 'string'],
            [['add_time'], 'safe'],
            [['ver_num'], 'string', 'max' => 32],
            [['package_url'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '设备类型',
            'ver_num' => '版本号',
            'package_url' => '安装包下载地址',
            'description' => '描述',
            'add_time' => '发布时间',
            'status' => '是否启用',
        ];
    }
}
