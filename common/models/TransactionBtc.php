<?php

namespace common\models;

use common\models\Coins;
use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%transaction_btc}}".
 *
 * @property string $id
 * @property int $member_id 用户ID
 * @property string $coin_symbol 货币标识
 * @property string $tx_hash 交易TxHash
 * @property string $from 交易发起方
 * @property string $input 交易输入组合json（包含使用的unspent块，txid，tx_output_n，value）
 * @property string $to 接收方
 * @property string $value_dec 10进制交易值
 * @property string $fee 手续费
 * @property string $data
 * @property string $raw
 * @property int $created_at
 * @property int $block 交易成功的块高度Block Height
 * @property string $tx_status 交易状态，prepare:准备，padding:等待确认，success:成功
 * @property string $rpc_response rpc返回
 * @property int $updated_at 交易确认时间
 */
class TransactionBtc extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%transaction_btc}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'created_at', 'block', 'updated_at'], 'integer'],
            [['input', 'data', 'raw'], 'string'],
            [['coin_symbol'], 'string', 'max' => 32],
            [['tx_hash', 'rpc_response'], 'string', 'max' => 70],
            [['from', 'to'], 'string', 'max' => 50],
            [['value_dec', 'fee'], 'string', 'max' => 50],
            [['tx_status'], 'string', 'max' => 10],
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
            'coin_symbol' => 'Coin Symbol',
            'tx_hash' => 'Tx Hash',
            'from' => 'From',
            'input' => 'Input',
            'to' => 'To',
            'value_dec' => 'Value Dec',
            'fee' => 'Fee',
            'data' => 'Data',
            'raw' => 'Raw',
            'created_at' => 'Created At',
            'block' => 'Block',
            'tx_status' => 'Tx Status',
            'rpc_response' => 'Rpc Response',
            'updated_at' => 'Updated At',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }

    public function getCoin(){
        return $this->hasOne(Coins::className(),['symbol' => 'coin_symbol']);
    }
}
