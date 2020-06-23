<?php

namespace common\models;

use Yii;

class WithdrawAddress extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jl_withdraw_address';
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
