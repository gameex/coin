<?php

namespace common\models;

use Yii;

class PersonalAccount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jl_personal_account';
    }

    public function rules()
    {
        return [
            [['symbol', 'addr', 'coin_id'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'symbol'       => '币种标识',
            'addr'         => '地址',
            'coin_id'      => '币种标识',
        ];
    }
}
