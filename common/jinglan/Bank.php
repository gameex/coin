<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/28
 * Time: 10:09
 */

namespace common\jinglan;

use api\models\Transaction;
use common\models\TransactionBtc;
use api\models\BalanceLog;
use common\models\WithdrawApply;

class Bank extends Jinglan
{
    public static $network_type;

    public static function getNetwork(){
        // $chain_network = empty($_POST['chain_network']) ? 'testnet' : $_POST['chain_network'];

        // if ($chain_network == 'main_network') {
        //     $network_type = 0;// 主网
        // }else{
        //     $network_type = 1;// 测试网
        // }
        return 0;
    }
    //查询用户币种banlance_log余额
    public static function getBalance($uid,$coin_symbol='ETH'){
        if (empty($uid)) {
            return 0.0;
        }
        //$bank_last_balance = BalanceLog::find()->where(['member_id'=>$uid,'coin_symbol'=>$coin_symbol,'addr'=>$addr,'network'=>self::getNetwork()])->orderBy('id desc')->one();
        $bank_last_balance = BalanceLog::find()->where(['member_id'=>$uid,'coin_symbol'=>$coin_symbol,'network'=>0])->orderBy('id desc')->one();   

        if ($bank_last_balance){
            return $bank_last_balance->balance  + self::getWealth_balance($uid,$coin_symbol);
        }else{
            return 0.0  + self::getWealth_balance($uid,$coin_symbol);
        }
    }

    //查询用户 银行可用余额
    public static function getAvailableBalance($uid,$coin_symbol='ETH'){
        if (empty($uid)) {
            return 0.0;
        }

        return self::getBalance($uid,$coin_symbol) - self::getOTC_freeze($uid,$coin_symbol) + self::getWealth_balance($uid,$coin_symbol) - self::getWithdraw_freeze($uid,$coin_symbol);
    }

    //查询用户 OTC场外交易冻结
    public static function getOTC_freeze($uid,$coin_symbol='ETH'){
        if (empty($uid)) { 
            return 0.0;
        }
        
        $tablePrefix = \Yii::$app->db->tablePrefix;

        $amount = (new \yii\db\Query())
            ->from("{$tablePrefix}otc_order ")
            //->where(['and','status > 1',['or',['side'=>2,'seller_uid'=>$uid,'coin_name'=>$coin_symbol], ['side'=>1,'buyer_uid'=>$uid,'coin_name'=>$coin_symbol]]])
            ->where(['and','status > 1',['side'=>2,'seller_uid'=>$uid,'coin_name'=>$coin_symbol]])
            // ->orWhere(['and','status > 1',['side'=>1,'buyer_uid'=>$uid,'coin_name'=>$coin_symbol]])
            ->sum('amount');

        //查询商户保证金冻结数值
        $merchant_freeze =   (new \yii\db\Query())
            ->from("{$tablePrefix}otc_merchants_freeze")
            ->where(['uid'=>$uid,'coin_symbol'=>$coin_symbol])
            ->sum('amount');   

        $amount = $amount +  $merchant_freeze;

        return empty($amount) ? 0.0 : $amount;
    }

    //查询用户理财余额
    public static function getWealth_balance($uid,$coin_symbol='ETH'){
        if (empty($uid)) { 
            return 0.0;
        }
        
        $tablePrefix = \Yii::$app->db->tablePrefix;

        //查询商户保证金冻结数值
        $amount =   (new \yii\db\Query())
            ->from("{$tablePrefix}member_wealth_balance")
            ->where(['uid'=>$uid,'coin_symbol'=>$coin_symbol])
            ->sum('amount');   

        $amount = $amount;

        return empty($amount) ? 0.0 : $amount;
    }


    //查询用户 提现冻结
    public static function getWithdraw_freeze($uid,$coin_symbol='ETH'){
        if (empty($uid)) {
            return 0.0;
        }
        $tablePrefix = \Yii::$app->db->tablePrefix;
        $amount = (new \yii\db\Query())
            ->from("{$tablePrefix}withdraw_apply")
            ->where(['member_id'=>$uid,'coin_symbol'=>$coin_symbol,'chain_network'=>self::getNetwork()])
            ->andWhere(['or','status=1','status=5'])
            ->select('sum(value_dec) as value_dec, sum(current) as current,sum(withdraw_fee) as withdraw_fee')
            ->one();
        if (empty($amount)){
            return 0.0;
        }else{
            return $amount['value_dec'] + $amount['current'] + $amount['withdraw_fee'];
        }

        //$value = WithdrawApply::find()->where(['member_id'=>$uid,'coin_symbol'=>$coin_symbol,'chain_network'=>self::getNetwork(),'status'=>1])->sum('cast(value_dec as decimal(10,10))');
        //$value = WithdrawApply::find()->where(['member_id'=>$uid,'coin_symbol'=>$coin_symbol,'chain_network'=>self::getNetwork(),'status'=>1])->sum('value_dec');

//        if ($coin_symbol == 'ETH'){
//            $value = Transaction::find()->where(['type'=>3,'coin_symbol'=>$coin_symbol,'from'=>$addr,'tx_status'=>'pending'])->sum('cast(value_dec as decimal(10,10))');
//        }else{
//            $value = TransactionBtc::find()->where(['type'=>3,'coin_symbol'=>$coin_symbol,'from'=>$addr,'tx_status'=>'pending'])->sum('cast(value_dec as decimal(10,10))');
//        }
//        if ($value){
//            return $value;
//        }else{
//            return 0.0;
//        }
    }

    //查询用户 充值冻结
    public static function getTop_up_freeze($uid,$coin_symbol='ETH'){
        if (empty($uid)) {
            return 0.0;
        }
        if ($coin_symbol == 'ETH'){
            $value = Transaction::find()->where(['type'=>2,'coin_symbol'=>$coin_symbol,'member_id'=>$uid,'tx_status'=>'pending','network'=>self::getNetwork()])->sum('cast(value_dec as decimal(10,10))');

        }else{
            $value = TransactionBtc::find()->where(['type'=>2,'coin_symbol'=>$coin_symbol,'member_id'=>$uid,'tx_status'=>'pending','network'=>self::getNetwork()])->sum('cast(value_dec as decimal(10,10))');
        }
        if ($value){
            return $value;
        }else{
            return 0.0;
        }
    }
}