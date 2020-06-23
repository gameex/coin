<?php

namespace api\models;

use Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "users_wallets".
 *
 * @property string $id
 * @property string $uid
 * @property string $coin_symbol
 * @property string $balance
 * @property string $addr
 * @property integer $block
 * @property integer $status
 */
class MemberWallet extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%member_wallets}}';
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
            'id' => 'ID',
            'uid' => 'Uid',
            'coin_symbol' => 'Coin Symbol',
            'unit' => 'Unit',
            'balance' => 'Balance',
            'addr' => 'Addr',
            'memo' => 'Memo',
            'seed' => 'Seed',
            'block' => 'Block',
            'status' => 'Status',
        ];
    }
}
