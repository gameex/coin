<?php

namespace common\models;

use Yii;

class ApiAccessToken extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jl_api_access_token';
    }
}
