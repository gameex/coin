<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%coins}}".
 *
 * @property string $id
 * @property string $symbol 英文简称
 * @property string $coin_name 货币名称
 * @property string $icon
 * @property int $enable 0 不启用 1启用
 * @property int $parent_id 0 没有父级
 * @property int $listorder 排序
 * @property int $check_rate 1:需要查询汇率 0:不需要
 * @property string $usd 兑换美元汇率
 * @property string $cny 兑换人民币汇率
 * @property int $exchange_rate_updated_at 汇率更新时间
 * @property int $ram_status 1:衍生代币 0:正常币种
 * @property string $unit 货币单位
 * @property string $ram_token_addr 衍生代币token地址
 */
class Coin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%coins}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['symbol', 'coin_name'], 'required'],
            [['enable', 'parent_id', 'listorder', 'check_rate', 'exchange_rate_updated_at', 'ram_status'], 'integer'],
            [['usd', 'cny'], 'number'],
            [['symbol', 'coin_name'], 'string', 'max' => 100],
            [['icon'], 'string', 'max' => 255],
            [['unit'], 'string', 'max' => 32],
            [['ram_token_addr'], 'string', 'max' => 60],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'symbol' => 'Symbol',
            'coin_name' => 'Coin Name',
            'icon' => 'Icon',
            'enable' => 'Enable',
            'parent_id' => 'Parent ID',
            'listorder' => 'Listorder',
            'check_rate' => 'Check Rate',
            'usd' => 'Usd',
            'cny' => 'Cny',
            'exchange_rate_updated_at' => 'Exchange Rate Updated At',
            'ram_status' => 'Ram Status',
            'unit' => 'Unit',
            'ram_token_addr' => 'Ram Token Addr',
            'recharge_enable' => 'recharge enbale status',
            'withdraw_enable' => 'withdraw enable status',
        ];
    }
}
