<?php

namespace common\models;

use common\models\member\Member;
use Yii;

/**
 * This is the model class for table "jl_member_proceeds_type".
 *
 * @property int $id
 * @property int $member_id 用户ID
 * @property string $proceeds_type 收款类型[alipay|wxpay|bank]
 * @property string $account 微信支付宝账号 或 银行卡号
 * @property string $username 持卡人姓名（银行卡）
 * @property string $qrcode 支付宝微信  收款码地址
 * @property string $bank_name 开户行（银行卡）
 * @property string $icon icon（银行卡支付宝微信）
 * @property int $is_delete 0:未删除 1：已删除
 * @property string $ctime 创建时间
 */
class MemberProceeds extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jl_member_proceeds_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'proceeds_type', 'account', 'icon'], 'required'],
            [['member_id', 'is_delete'], 'integer'],
            [['ctime'], 'safe'],
            [['proceeds_type'], 'string', 'max' => 16],
            [['account', 'username', 'qrcode', 'bank_name', 'icon'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'proceeds_type' => 'Proceeds Type',
            'account' => 'Account',
            'username' => 'Username',
            'qrcode' => 'Qrcode',
            'bank_name' => 'Bank Name',
            'icon' => 'Icon',
            'is_delete' => 'Is Delete',
            'ctime' => 'Ctime',
        ];
    }

    public function getUser(){
        return $this->hasOne(Member::className(),['id' => 'member_id']);
    }

    public function getProceeds(){
        return $this->hasOne(Proceeds::className(), ['proceeds_type' => 'proceeds_type']);
    }
}
