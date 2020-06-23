<?php

namespace api\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "member_contacts".
 *
 * @property string $id
 * @property int $uid
 * @property string $name
 * @property string $wallet_addr
 * @property string $coin_symbol
 * @property string $mobile
 * @property string $email
 * @property string $remark
 * @property integer $created_at
 * @property integer $updated_at
 */
class MemberContact extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%member_contacts}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'name','coin_symbol' ,'wallet_addr'], 'required'],
            [['uid', 'created_at', 'updated_at'], 'integer'],
            [['name', 'wallet_addr', 'email'], 'string', 'max' => 100],
            [['mobile'], 'string', 'max' => 20],
            [['remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'UID',
            'name' => 'Name',
            'wallet_addr' => 'Wallet Addr',
            'coin_symbol' => 'Coin Symbol',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'remark' => 'Remark',
            'created_at'            => '创建时间',
            'updated_at'            => '修改时间',
        ];
    }

    /**
     * 行为插入时间戳
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }
}
