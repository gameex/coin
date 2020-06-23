<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/17
 * Time: 11:14
 */


namespace common\models;


use yii\db\ActiveRecord;

class OtcMarket extends ActiveRecord{


    public static function tableName(){
        return "{{%otc_market}}";
    }

    public function rules(){
        return [
            
        ];
    }


    public function attributeLabels(){
        return [
            'id'                    =>      '主键ID',
            'uid'               =>      '用户ID',
            'side'             =>      '1 卖家 2 买家',
            'coin_id'          =>      '币种ID',
            'coin_name'          =>      '币种名字',
            'min_num'          =>      '最小限额',
            'max_num'          =>      '最大限额',
            'price_usd'          =>      '价格USD',
            'note'          =>      '备注',
            'card_enable'          =>      '银行卡支持',
            'wechat_enable'          =>      '支付宝支持',
            'alipay_enable'          =>      '微信支持',
            'deal_count'          =>      '成交次数',
            'deal_rate'          =>      '成交率',
            'publish_time'          =>      '发布时间',
            'status'          =>      '状态',

        ];
    }

}