<?php

namespace common\models;

use Yii;

class SysAddr extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jl_sys_addr';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['symbol', 'addr', 'account_name', 'password', 'coin_id'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'symbol'       => '币种标识',
            'addr'         => '地址',
            'account_name' => '账户名称',
            'password'     => '账户密码',
            'coin_id'      => '币种标识',
        ];
    }
}
