<?php

namespace common\models;

use Yii;

class WithdrawApply extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jl_withdraw_apply';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['type', 'status'], 'integer'],
            // [['ver_num'], 'required'],
            // [['description'], 'string'],
            // [['add_time'], 'safe'],
            // [['ver_num'], 'string', 'max' => 32],
            // [['package_url'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            // 'id' => 'ID',
            // 'type' => '设备类型',
            // 'ver_num' => '版本号',
            // 'package_url' => '安装包下载地址',
            // 'description' => '描述',
            // 'add_time' => '发布时间',
            // 'status' => '是否启用',
        ];
    }
}
