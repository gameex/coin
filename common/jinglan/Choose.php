<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/22
 * Time: 12:17
 */

namespace common\jinglan;

use Think\Exception;
use Yii;
use jinglan\ves\VesRPC;
use api\models\Coin;
use api\models\CoinChoose;
use Denpa\Bitcoin\Client as BitcoinClient;
use Denpa\Bitcoin\Omnicore as OmnicoreClient;

class Choose extends Jinglan
{
    const limit_amount = 0.0001;

    public static function find($uid)
    {
        //查询viabtc exchange server的货币类型

        $rpc_method = 'asset.list';
        $rpc_params = [];
        $rpc = new VesRPC();
        $rpc_ret = $rpc->do_rpc($rpc_method, $rpc_params);

        if($rpc_ret['code'] == 0){
            Jinglan::error_message($rpc_ret['data']);
        }else{
            $assets = $rpc_ret['data'];
        }

        $coins = array_column($assets,'name');

        $rpc_ret = $rpc->do_rpc('market.list', $rpc_params);
        if($rpc_ret['code'] == 0){
            Jinglan::error_message($rpc_ret['data']);
        }else{
            $market = $rpc_ret['data'];
        }

        $db_coins = Coin::find()->where(['in','symbol',$coins])->select('symbol,icon,unit,ram_token_decimals')->asArray()->all();
        $db_coins = array_column($db_coins,NULL,'symbol');
        $ret = [];
        foreach ($coins as $x){
            $list = [];
            foreach ($market as $k => &$y){
                if($y['money'] == $x){
                    unset($y['fee_prec']);
                    unset($y['stock_prec']);
                    unset($y['money_prec']);
                    $y['stock_icon'] = Jinglan::get_user_avatar_url($db_coins[$y['stock']]['icon']);
                    $choose = CoinChoose::findOne(['money'=>$y['money'],'stock'=>$y['stock'],'status'=>1,'uid'=>$uid]);
                    if (!empty($choose)) {
                        $y['status'] = $choose['status'];
                        array_push($ret,$y);
                        unset($market[$k]);
                    }
                }
            }
        }
        if(!empty($ret)){
            $language = Yii::$app->request->post('language') == 'en_us'?'en_us':'zh_cn';
            Jinglan::do_aes(json_encode(['code'=>200,'data'=>$ret,'usd_to_cny'=>6.39,'message'=>Yii::t($language,'_Submission_Success_')]));
        }else{
            Jinglan::error_message('_No_Data_Query_');
        }
    }

    
}