<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "jl_coins".
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
class Coins extends \yii\db\ActiveRecord
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
            [['symbol', 'coin_name','icon','unit','ram_status','unit','limit_amount','ram_token_decimals','withdraw_fee'], 'required'],
            [['enable', 'parent_id', 'listorder', 'check_rate', 'exchange_rate_updated_at', 'ram_status','recharge_enable','withdraw_enable'], 'integer'],
            [['usd', 'cny','limit_amount','withdraw_fee'], 'number'],
            [['symbol', 'coin_name'], 'string', 'max' => 100],
            [['icon'], 'string', 'max' => 255],
            [['unit'], 'string', 'max' => 8],
            [['ram_token_addr'], 'string', 'max' => 60],
            [['coin_text'], 'string', 'max' => 100],
            [['sell_limit'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'symbol' => '币种',
            'coin_name' => '名称',
            'coin_text' => '简介',
            'icon' => '代币图片',
            'enable' => '状态',
            'parent_id' => '父级币种',
            'listorder' => '排序',
            'usd' => '兑换美元汇率',
            'cny' => '兑换人民币汇率',
            'exchange_rate_updated_at' => '汇率更新时间',
            'check_rate' => 'Check Rate',
            'ram_status' => '衍生货币',
            'unit' => '单位',
            'ram_token_decimals' => '小数位数',
            'ram_token_addr' => '衍生合约地址',
            'limit_amount'   => '最低提现数量',
            'withdraw_fee'   => '提现手续费',
            'sell_limit'   => '单日最高卖出比例(%)',
            'recharge_enable'   => '充值开启状态',
            'withdraw_enable'   => '提现开启状态',
        ];
    }

    public function getParent(){
        return $this->hasOne(self::className(), ['id' => 'parent_id']);
    }

    public static function getParentId(){
        $data = self::find()->where(['parent_id' => 0])->asArray()->all();
        $id_list = [0 => '无父级'];
        if(!empty($data)) {
            $id_list = array_merge($id_list, ArrayHelper::map($data, 'id', 'symbol'));
        }
        return $id_list;
    }

    public function beforeSave($insert)
    {
        if($this->isNewRecord)
        {
            $this->exchange_rate_updated_at = time();
        }

        return parent::beforeSave($insert);
    }
}
