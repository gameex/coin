<?php

namespace common\models;

use Yii;

class BalanceLog extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'jl_balance_log';
    }

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
        
    }
}
