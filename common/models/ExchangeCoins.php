<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/12
 * Time: 19:02
 */


namespace api\models;


use yii\db\ActiveRecord;

class ExchangeCoins extends ActiveRecord{

    public static function tableName(){
        return "{{%exchange_coins}}";
    }



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['limit_amount', 'taker_fee','maker_fee','stock_coin_id','money_coin_id','stock','money'], 'required'],
            [['enable'],'integer'],
            [['limit_amount','taker_fee','maker_fee'],'double']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stock_coin_id' => '交易币种ID',
            'stock' => '交易币种简称',
            'money_coin_id' => '结算币种ID',
            'money' => '结算币种简称',
            'limit_amount' => '发布最低值',
            'taker_fee' => 'Taker费率',
            'maker_fee' => 'Maker费率',
            'enable' => '状态',
            'listorder' => '排序',
        ];
    }

}