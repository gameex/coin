<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "jl_bank".
 *
 * @property int $id
 * @property string $bank_name 银行名称
 * @property string $icon 图标
 * @property int $status 0:禁用1启用
 */
class Bank extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jl_bank';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bank_name','icon'], 'required'],
            [['bank_name'], 'unique'],
            [['status'], 'integer'],
            [['bank_name'], 'string', 'max' => 32],
            [['icon'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bank_name' => '开户行名称',
            'icon' => 'Icon',
            'status' => '状态',
        ];
    }
}
