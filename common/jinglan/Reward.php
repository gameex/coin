<?php

namespace common\jinglan;

use Yii;
use jinglan\ves\VesRPC;
use api\models\Member;
use api\models\BalanceLog;


class Reward extends Jinglan
{

    //获取交易对等值实时USD价格
    public static function coin_usd_price($coin_symbol){
        if($coin_symbol=="USDT"){
            return 1;
        }
        $rpc = new VesRPC();
        $rpc_method = 'market.list';
        $rpc_params = [];        
        $rpc_ret = $rpc->do_rpc($rpc_method, $rpc_params);
        if($rpc_ret['code'] == 0){
             return 0;
        }else{
            $market_list = $rpc_ret['data'];
        }
        if (is_array($market_list)){
            $price_array = array();
            foreach ($market_list as $key => &$value) {
                $rpc_method2 = 'market.status_today';
                $rpc_params2 = [$value['name']];        
                $rpc_ret2 = $rpc->do_rpc($rpc_method2, $rpc_params2);
                if($rpc_ret2['code'] == 1){
                    $price_array[$value['name']] = $rpc_ret2['data']['last'];
                }            
            }
            //var_dump($price_array);
            //获取当前币种价格
            //先判断是否存在USDT交易对
            if(isset($price_array[$coin_symbol."USDT"])){
                return $price_array[$coin_symbol."USDT"];
            }
            //遍历其他交易对
            foreach ($price_array as $key => $value) {
                if(strlen($key)>4){
                    if(substr($key, strlen($key)-4) == "USDT"){
                        $coin = substr($key, 0, strlen($key)-4);
                        if(isset($price_array[$coin_symbol.$coin])){
                            return $price_array[$coin_symbol.$coin] *$value;
                        }
                    }
                }
            }
            return 0;
        }else{
            return 0;
        }

    }

    //充值
    public static function recharge($uid,$coin_symbol,$amount){
        $usd_price = self::coin_usd_price($coin_symbol) * $amount;
        $user_info = Member::find()->where(['id'=>$uid])->one();
        if(!empty($user_info)){
            $user_info['total_recharge_usd'] = intval(($user_info['total_recharge_usd']+ $usd_price)*100)/100;
            $user_info->save();            
        }          
    }


    //下单记录
    public static function order($uid,$coin_symbol,$amount){
        $usd_price = self::coin_usd_price($coin_symbol) * $amount;
        $user_info = Member::find()->where(['id'=>$uid])->one();
        $parent_id = 0;
        $total_order_usd = 0; 
        if(!empty($user_info)){
            $parent_id = $user_info['last_member'];
            $total_order_usd = $user_info['total_order_usd'];
            $user_info['total_order_usd'] = intval(($user_info['total_order_usd']+ $usd_price)*100)/100;
            $user_info->save();            
        }
        if($parent_id==0){
            return;
        }
        //上级释放邀请奖励
        $status = Yii::$app->config->info('INVITE_REG_REWARD_STATUS');
        $level1_reward = Yii::$app->config->info('INVITE_REG_LEVEL1_REWARD');
        $level2_reward = Yii::$app->config->info('INVITE_REG_LEVEL2_REWARD');
        $level3_reward = Yii::$app->config->info('INVITE_REG_LEVEL3_REWARD');
        $per_usd_frozen_value =  Yii::$app->config->info('INVITE_REWARD_RELEASE_RATE'); 
        $reward_coin_symbol =  Yii::$app->config->info('PLATFORM_COIN_SYMBOL'); 
        //1代
        $parent_user = Member::find()->where(['id'=>$parent_id])->one();
        if(!empty($parent_user)){
            if($status == 1){
                if($total_order_usd * $per_usd_frozen_value < $level1_reward ){
                    $release_amount  = min($usd_price*$per_usd_frozen_value,$level1_reward - $total_order_usd * $per_usd_frozen_value);
                    $parent_user['freeze_rewards'] =  $parent_user['freeze_rewards'] -  $release_amount;
                    $parent_user['frozen_rewards'] =  $parent_user['frozen_rewards'] + $release_amount;
                    self::addBalance($parent_user['id'],$reward_coin_symbol,$release_amount);
                }
            }
            $parent_user->save();       
            //2代
            if(!empty($parent_user['last_member'])){
                $parent_user2 = Member::find()->where(['id'=>$parent_user['last_member']])->one();
                if(!empty($parent_user2)){
                    if($status == 1){
                        if($total_order_usd * $per_usd_frozen_value < $level2_reward ){
                            $release_amount  = min($usd_price*$per_usd_frozen_value,$level2_reward - $total_order_usd * $per_usd_frozen_value);
                            $parent_user2['freeze_rewards'] =  $parent_user2['freeze_rewards'] -  $release_amount;
                            $parent_user2['frozen_rewards'] =  $parent_user2['frozen_rewards'] + $release_amount;
                            self::addBalance($parent_user2['id'],$reward_coin_symbol,$release_amount);

                        }                                            
                    }
                    $parent_user2->save();  
                    //3代
                    if(!empty($parent_user2['last_member'])){
                        $parent_user3 = Member::find()->where(['id'=>$parent_user2['last_member']])->one();
                        if(!empty($parent_user3)){
                            if($status == 1){
                                if($total_order_usd * $per_usd_frozen_value < $level3_reward ){
                                    $release_amount  = min($usd_price*$per_usd_frozen_value,$level3_reward - $total_order_usd * $per_usd_frozen_value);
                                    $parent_user3['freeze_rewards'] =  $parent_user3['freeze_rewards'] -  $release_amount;
                                    $parent_user3['frozen_rewards'] =  $parent_user3['frozen_rewards'] + $release_amount;
                                    self::addBalance($parent_user3['id'],$reward_coin_symbol,$release_amount);
                                }                                  
                            }
                            $parent_user3->save();            
                        }  
                    }

                }                         
            }                 
        }      
    
    }

    //加币
    public static function addBalance($uid,$coin_symbol,$amount){

        $bank_balance = Bank::getBalance($uid,$coin_symbol);
        $balance_model = new BalanceLog();
        $balance_model->type = 1;//1:充值，10:取出
        $balance_model->member_id = $uid;
        $balance_model->coin_symbol = $coin_symbol;
        $balance_model->addr = "";
        $balance_model->change = (double)$amount;
        $balance_model->balance = $bank_balance + (double)$amount;
        $balance_model->fee = 0.0;
        $balance_model->detial_type = 'release_invite_reward';
        $balance_model->network = 0;
        $balance_model->save(false);           
    }

    //提现记录
    public static function withdraw($uid,$coin_symbol,$amount){
        $usd_price = self::coin_usd_price($coin_symbol) * $amount;
        $user_info = Member::find()->where(['id'=>$uid])->one();
        if(!empty($user_info)){
            $user_info['total_withdraw_usd'] = intval(($user_info['total_withdraw_usd']+ $usd_price)*100)/100;
            $user_info->save();            
        }  
    }
}