<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "jl_transaction".
 *
 * @property int $id
 * @property int $member_id 用户ID
 * @property string $coin_symbol 货币标识
 * @property string $tx_hash 交易TxHash
 * @property string $from 交易发起方
 * @property string $to 接收方
 * @property string $value_hex 16进制交易值，单位wei
 * @property string $value_dec 10进制交易值，单位eth
 * @property string $gas_hex
 * @property string $gas_dec
 * @property string $gas_price_hex
 * @property string $gas_price_dec
 * @property string $nonce_hex
 * @property string $nonce_dec
 * @property string $data
 * @property string $raw
 * @property int $created_at
 * @property string $tx_status 交易状态，prepare:准备，pending:等待确认，success:成功
 * @property string $rpc_response rpc返回
 * @property int $block 交易成功的块高度Block Height
 * @property int $updated_at 交易确认时间
 */
class Transaction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jl_transaction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'coin_symbol', 'from', 'to', 'value_hex', 'value_dec', 'gas_hex', 'gas_dec', 'gas_price_hex', 'gas_price_dec', 'nonce_hex', 'nonce_dec'], 'required'],
            [['member_id', 'created_at', 'block', 'updated_at'], 'integer'],
            [['data', 'raw'], 'string'],
            [['coin_symbol'], 'string', 'max' => 8],
            [['tx_hash', 'rpc_response'], 'string', 'max' => 70],
            [['from', 'to', 'value_hex'], 'string', 'max' => 50],
            [['value_dec', 'gas_hex', 'gas_dec', 'gas_price_hex', 'gas_price_dec', 'nonce_hex', 'nonce_dec'], 'string', 'max' => 50],
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
            'to' => 'To',
            'value_hex' => 'Value Hex',
            'value_dec' => 'Value Dec',
            'gas_hex' => 'Gas Hex',
            'gas_dec' => 'Gas Dec',
            'gas_price_hex' => 'Gas Price Hex',
            'gas_price_dec' => 'Gas Price Dec',
            'nonce_hex' => 'Nonce Hex',
            'nonce_dec' => 'Nonce Dec',
            'data' => 'Data',
            'raw' => 'Raw',
            'created_at' => 'Created At',
            'tx_status' => 'Tx Status',
            'rpc_response' => 'Rpc Response',
            'block' => 'Block',
            'updated_at' => 'Updated At',
        ];
    }
}
