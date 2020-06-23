<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "jl_proceeds".
 *
 * @property int $id
 * @property string $name 收款类型名称
 * @property string $proceeds_type 收款类型标识
 * @property string $icon
 * @property int $status 1:启用  0：关闭
 * @property int $is_qrcode 是否需要付款码（是否需要上传图片） 1：需要 0：不需要
 * @property string $ctime 创建时间
 */
class Proceeds extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jl_proceeds';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'proceeds_type','icon'], 'required'],
            [['name', 'proceeds_type'], 'unique'],
            [['id', 'status', 'is_qrcode'], 'integer'],
            [['ctime'], 'safe'],
            [['name', 'proceeds_type', 'icon'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '类型名称',
            'proceeds_type' => '类型标识',
            'icon' => 'Icon',
            'status' => '状态',
            'is_qrcode' => '是否需要付款码',
            'ctime' => 'Ctime',
        ];
    }

    public function beforeSave($insert)
    {
        if($this->isNewRecord)
        {
            $this->ctime = date('Y-m-d H:i:s');
        }

        return parent::beforeSave($insert);
    }
}
