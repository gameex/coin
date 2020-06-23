<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/22
 * Time: 12:17
 */

namespace common\jinglan;

use api\models\Member;
use api\models\BalanceLog;
use api\models\ExchangeCoins;
use api\models\MemberWallet;
use common\models\Coins;
use Think\Exception;
use Yii;
use jinglan\ves\VesRPC;
use api\models\Coin;
use api\models\CoinChoose;
use Denpa\Bitcoin\Client as BitcoinClient;
use Denpa\Bitcoin\Omnicore as OmnicoreClient;

use common\jinglan\Reward;

class Trade extends Jinglan
{
    const limit_amount = 0.0001;

    public static function market_v3($uid=0)
    {
        $coins = Coin::find()->where(['enable' => 1])->select('symbol,icon,unit,ram_token_decimals,usd,cny')->orderBy('listorder DESC')->asArray()->all();
        $coins = array_column($coins, NULL, 'symbol');
        $coin_keys = array_keys($coins);

        $market = ExchangeCoins::find()->where(['enable' => 1])->andWhere(['in', 'stock', $coin_keys])->andWhere(['in', 'money', $coin_keys])->select('stock,money,limit_amount as min_amount,taker_fee,maker_fee')->orderBy('listorder DESC')->asArray()->all();
        if ($uid) {
            $coin_choose = CoinChoose::find()->where(['uid' => $uid, 'status' => 1])->select('stock,money')->asArray()->all();
            if (!empty($coin_choose)) {
                array_walk($coin_choose, function ($val, $key) use (&$coin_choose) {
                    $coin_choose[$key] = $val['stock'] . $val['money'];
                    unset($val['stock']);
                    unset($val['money']);
                });
            }
        } else {
            $coin_choose = [];
        }
        $market_money = array_column($market,'money');
        $market_money = array_unique($market_money);
        // p($coins);exit();

        $ret = [];
        foreach ($market_money as $x){
            $list = [];
            foreach ($market as $k => $y){
                if ($y['money'] == $x){
                    $temp2 = array(
                        'name' => $y['stock'].$y['money'],
                        'stock' => $y['stock'],
                        'money' => $y['money'],
                        'min_amount' => $y['min_amount'],
                        'taker_fee' => $y['taker_fee'],
                        'maker_fee' => $y['maker_fee'],
                        'stock_icon' => Jinglan::get_user_avatar_url($coins[$y['stock']]['icon']),
                    );
                    $temp2['status'] = in_array($temp2['name'], $coin_choose) ? 1 : 0;
                    // 新增汇率字段
                    $temp2['exchange_rate_usd'] = round((float)$coins[$y['money']]['usd'], 5);
                    $temp2['exchange_rate_cny'] = round((float)$coins[$y['money']]['cny'], 5);
                    array_push($list,$temp2);
                }
            }
            $temp = ['main'=>$x, 'main_icon'=>Jinglan::get_user_avatar_url($coins[$x]['icon']),'list'=>$list];
            array_push($ret, $temp);
        }
        if(!empty($ret)){
            // Jinglan::success_message($ret);
            $language = Yii::$app->request->post('language') == 'en_us'?'en_us':'zh_cn';
            Jinglan::do_aes(json_encode(['code'=>200,'data'=>$ret,'usd_to_cny'=>Jinglan::usd_to_cny(),'message'=>Yii::t($language,'_Submission_Success_')]));
        }else{
            Jinglan::error_message('_No_Data_Query_');
        }
    }

    public static function market_v2($uid=0){
        $coins = Coin::find()->where(['enable'=>1])->select('symbol,icon,unit,ram_token_decimals')->orderBy('listorder DESC')->asArray()->all();
        $coins = array_column($coins,NULL,'symbol');
        $coin_keys = array_keys($coins);

        $market = ExchangeCoins::find()->where(['enable'=>1])->andWhere(['in','stock',$coin_keys])->andWhere(['in','money',$coin_keys])->select('stock,money,limit_amount as min_amount,taker_fee,maker_fee')->orderBy('listorder DESC')->asArray()->all();
        if($uid){
            $coin_choose = CoinChoose::find()->where(['uid'=>$uid,'status'=>1])->select('stock,money')->asArray()->all();
            if (!empty($coin_choose)){
                array_walk($coin_choose, function ($val,$key) use(&$coin_choose){
                    $coin_choose[$key] = $val['stock'].$val['money'];
                    unset($val['stock']);
                    unset($val['money']);
                });
            }
        }else{
            $coin_choose = [];
        }

        $ret = [];
        foreach ($coins as $x){
            $list = [];
            foreach ($market as $k => $y){
                if ($y['money'] == $x['symbol']){
                    $temp2 = array(
                        'name' => $y['stock'].$y['money'],
                        'stock' => $y['stock'],
                        'money' => $y['money'],
                        'min_amount' => $y['min_amount'],
                        'taker_fee' => $y['taker_fee'],
                        'maker_fee' => $y['maker_fee'],
                        'stock_icon' => Jinglan::get_user_avatar_url($coins[$y['stock']]['icon']),
                    );
                    $temp2['status'] = in_array($temp2['name'], $coin_choose) ? 1 : 0;
                    array_push($list,$temp2);
                }
            }
            $temp = ['main'=>$x['symbol'], 'main_icon'=>Jinglan::get_user_avatar_url($coins[$x['symbol']]['icon']),'list'=>$list];
            array_push($ret, $temp);
        }
        if(!empty($ret)){
            // Jinglan::success_message($ret);
            $language = Yii::$app->request->post('language') == 'en_us'?'en_us':'zh_cn';
            Jinglan::do_aes(json_encode(['code'=>200,'data'=>$ret,'usd_to_cny'=>Jinglan::usd_to_cny(),'message'=>Yii::t($language,'_Submission_Success_')]));
        }else{
            Jinglan::error_message('_No_Data_Query_');
        }
    }

    public static function market()
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
                    array_push($list,$y);
                    unset($market[$k]);
                }
            }
            $temp = ['main'=>$x, 'main_icon'=>Jinglan::get_user_avatar_url($db_coins[$x]['icon']),'list'=>$list];
            array_push($ret, $temp);
        }
        if(!empty($ret)){
            // Jinglan::success_message($ret);
            $language = Yii::$app->request->post('language') == 'en_us'?'en_us':'zh_cn';
            Jinglan::do_aes(json_encode(['code'=>200,'data'=>$ret,'usd_to_cny'=>Jinglan::usd_to_cny(),'message'=>Yii::t($language,'_Submission_Success_')]));
        }else{
            Jinglan::error_message('_No_Data_Query_');
        }
    }

    public static function login($uid)
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
                    $choose = CoinChoose::findOne(['stock'=>$y['stock'],'money'=>$y['money'],'uid'=>$uid]);
                    $y['status'] = $choose['status'];
                    array_push($list,$y);
                    unset($market[$k]);
                    // p($y);exit;

                }
            }
            $temp = ['main'=>$x, 'main_icon'=>Jinglan::get_user_avatar_url($db_coins[$x]['icon']),'list'=>$list];
            array_push($ret, $temp);
            // p($temp);exit;

        }
        // p($ret);exit;
        if(!empty($ret)){
            // Jinglan::success_message($ret);
            $language = Yii::$app->request->post('language') == 'en_us'?'en_us':'zh_cn';
            Jinglan::do_aes(json_encode(['code'=>200,'data'=>$ret,'usd_to_cny'=>Jinglan::usd_to_cny(),'message'=>Yii::t($language,'_Submission_Success_')]));
        }else{
            Jinglan::error_message('_No_Data_Query_');
        }
    }

    public static function balance_v2($uid=0){
        $request = Yii::$app->request;
        $chain_network = empty($_POST['chain_network']) ? 'testnet' : $_POST['chain_network'];

        if ($chain_network == 'main_network') {
            $network_type = 0;// 主网
        }else{
            $network_type = 1;// 测试网
        }

        $asset_type = $request->post('asset_type');
        if (empty($asset_type)){
            $where = ['enable'=>1];
            $asset_type_array = [];
        }else{
            $asset_type_array = explode('|', $asset_type);
            $where = ['enable'=>1, 'symbol'=>$asset_type_array];
        }

        $coins = Coin::find()->where($where)->select('symbol,icon,ram_status,ram_token_addr,unit,ram_token_decimals,limit_amount,usd,cny,recharge_enable,withdraw_enable')->orderBy('listorder DESC')->asArray()->all();
        $coins = array_column($coins,NULL,'symbol');
        $bank_coins_symbol = array_keys($coins);

        $market = ExchangeCoins::find()->where(['enable'=>1])->andWhere(['in','stock',$bank_coins_symbol])->andWhere(['in','money',$bank_coins_symbol])->select('stock,money')->orderBy('listorder DESC')->asArray()->all();
        array_walk($market, function ($val,$key) use(&$market){
            $market[$key] = $val['stock'].$val['money'];
        });

        array_walk($bank_coins_symbol,function ($val,$key) use(&$bank_coins_symbol){$bank_coins_symbol[$key] = '_'.$val.'_';});
        $total_recharge_usd = 0;
        $total_order_usd = 0;
        $total_withdraw_usd = 0;
        if($uid){
            $model = Member::findOne(['id'=>$uid]);
            $model->last_time = time();
            $total_recharge_usd  = $model->total_recharge_usd;
            $total_order_usd  = $model->total_order_usd;
            $total_withdraw_usd  = $model->total_withdraw_usd;
            $model->save(false);
            //1.查询银行币种
            $bank_coins = MemberWallet::find()->where(['uid'=>$uid,'status'=>1,'network'=>$network_type,'coin_symbol'=>$bank_coins_symbol])->select('coin_symbol,addr,memo')->asArray()->all();
            $bank_coins = array_column($bank_coins,NULL,'coin_symbol');
            //2.查交易所余额
            $rpc_method = 'balance.query';
            $rpc_params = [intval($uid)];
            if ($asset_type_array){
                $rpc_params = array_merge($rpc_params, $asset_type_array);
            }
            $rpc = new VesRPC();
            $rpc_ret = $rpc->do_rpc($rpc_method, $rpc_params);

            if ($rpc_ret['code'] == 0) {
                Jinglan::error_message($rpc_ret['data']);
            } else {
                $assets = $rpc_ret['data'];
            }
        }else{
            $assets = [];
            $bank_coins = [];
        }
        $ret = [];
        $assets_keys = array_keys($assets);
        foreach ($coins as $x){
            // $addr = empty($bank_coins) ? '' : $bank_coins['_'.$x['symbol'].'_']['addr'];
            $addr = in_array('_'.$x['symbol'].'_', array_keys($bank_coins)) ? $bank_coins['_'.$x['symbol'].'_']['addr'] : '';
            $memo = in_array('_'.$x['symbol'].'_', array_keys($bank_coins)) ? $bank_coins['_'.$x['symbol'].'_']['memo'] : '';
            //$exchange_available = empty($assets) ? 0 : (string)Jinglan::sctonum($assets[$x['symbol']]['available']);
            $exchange_available = empty($assets) ? 0 : (in_array($x['symbol'], $assets_keys) ? (string)Jinglan::sctonum($assets[$x['symbol']]['available']) : 0);
            $exchange_available_rel = $exchange_available;
            //$exchange_freeze = empty($assets) ? 0 : (string)Jinglan::sctonum($assets[$x['symbol']]['freeze']);
            $exchange_freeze = empty($assets) ? 0 : (in_array($x['symbol'], $assets_keys) ? (string)Jinglan::sctonum($assets[$x['symbol']]['freeze']) : 0);
            $oct_freeze = (string)Jinglan::sctonum(Bank::getOTC_freeze($uid,$x['symbol']));

            $withdraw_freeze = (string)Jinglan::sctonum(Bank::getWithdraw_freeze($uid,$x['symbol']));
            $top_up_freeze = (string)Jinglan::sctonum(Bank::getTop_up_freeze($addr,$x['symbol']));
            $bank_balance = Bank::getBalance($uid,$x['symbol']);
            $bank_balance_rel = $bank_balance;
            if ($bank_balance - $oct_freeze - $withdraw_freeze < 0){
                $exchange_available = (string)Jinglan::sctonum($exchange_available + $bank_balance - $oct_freeze - $withdraw_freeze);
                $bank_balance = '0';
            }else{
                $bank_balance = (string)Jinglan::sctonum($bank_balance - $oct_freeze - $withdraw_freeze);
            }

            $available = (string)Jinglan::sctonum($exchange_available + $bank_balance);
            $total_amount = (string)Jinglan::sctonum($exchange_available + $exchange_freeze + $oct_freeze + $withdraw_freeze + $top_up_freeze + $bank_balance);
            $total_freeze = (string)Jinglan::sctonum($exchange_freeze + $oct_freeze + $withdraw_freeze + $top_up_freeze);

            switch ($x['symbol']){
                case 'USDT':
                    $money = $available;
                    break;
                default:
                    if (in_array($x['symbol'].'USDT', $market)){
                        $rpc = new VesRPC();
                        $rpc_ret = $rpc->do_rpc("market.last", [$x['symbol'].'USDT']);
                        if ($rpc_ret['code'] == 0) {
                            $money = 0;
                        }else{
                            $money = strval($available * $rpc_ret['data']);
                        }
                    }else{
                        $money = 0;
                    }
                    break;
            }

            $temp = array(
                'addr' => $addr,
                'name' => $x['symbol'],
                'icon' => Jinglan::get_user_avatar_url($x['icon']),
                'unit' => $x['unit'],
                'exchange_available' => $exchange_available,
                'exchange_available_rel' => $exchange_available_rel,
                'exchange_freeze' => $exchange_freeze,
                'oct_freeze' => $oct_freeze,
                'withdraw_freeze' => $withdraw_freeze,
                'top_up_freeze' => $top_up_freeze,
                'bank_balance' => $bank_balance,
                'bank_balance_rel' => $bank_balance_rel,
                'limit_amount' => $x['limit_amount'],
                'available' => $available,
                'total_amount' => $total_amount,
                'withdraw_freeze' => $withdraw_freeze,
                'total_freeze' => $total_freeze,
                'money' => intval($money*100)/100,
                // 新增汇率字段
                'exchange_rate_usd' => (float)$coins[$x['symbol']]['usd'],
                'exchange_rate_cny' => (float)$coins[$x['symbol']]['cny'],
                //新增充值、提现控制
                'recharge_enable' => $coins[$x['symbol']]['recharge_enable'],
                'withdraw_enable' => $coins[$x['symbol']]['withdraw_enable'], 
                //新增钱包memo字段
                'memo' => $memo,
            );
            array_push($ret, $temp);
        }

        $total_money = intval((string)array_sum(array_column(array_values($ret),'money'))*100)/100;
        $total_change = $total_money +  $total_withdraw_usd -  (float)$total_recharge_usd ;
        $total_recharge = (float)$total_recharge_usd;
        $ret = ['list'=>array_values($ret),'total_money'=>$total_money,'total_recharge'=>$total_recharge,'total_change'=>$total_change];
        // $session = Yii::$app->session;
        // $session->open();
        // $session_return_way = $session->get('return_way');
        // $session->close();
        $session_return_way = isset($_POST['return_way']) ? $_POST['return_way'] : '';
        // 判断返回类型
        if ($session_return_way == 'array') {
            return array_values($ret);
        }else{
            Jinglan::success_message($ret);
        }
    }

    public static function order($uid){
        $request = Yii::$app->request;
        $market = $request->post('market');
        $side = $request->post('side');
        $amount = $request->post('amount');
        $pride = $request->post('pride');
        // $taker_fee_rate = $request->post('taker_fee_rate');
        // $maker_fee_rate = $request->post('maker_fee_rate');
        // $source = $request->post('source');

        Jinglan::check_empty($market, '_The_Market_Can_Not_Be_Empty_');
        Jinglan::check_empty($side, '_The_Side_Can_Not_Be_Empty_');
        Jinglan::check_empty($amount, '_The_Amount_Can_Not_Be_Empty_');
        Jinglan::check_empty($pride, '_The_Pride_Can_Not_Be_Empty_');
        //Jinglan::check_empty($taker_fee_rate, '_The_Taker_Fee_Can_Not_Be_Empty_');
        //Jinglan::check_empty($maker_fee_rate, '_The_Maker_Fee_Can_Not_Be_Empty_');

        $var = Yii::$app->config->info('OTC_MERCHANTS');
        $uinfo = Member::findOne(['id'=>$uid]);
        if($var == 1){
           // if ($uinfo['otc_merchant'] != 1){
            //    Jinglan::error_message('_Please_complete_the_merchant_certification_first_');
            //}
        }

        if(!in_array($side,[1,2])){
            Jinglan::error_message('_The_Side_Error_');
        }
        $rpc_method = 'market.list';
        $rpc_params = [];
        $rpc = new VesRPC();
        $rpc_ret = $rpc->do_rpc($rpc_method, $rpc_params);

        if($rpc_ret['code'] == 0){
            Jinglan::error_message($rpc_ret['data']);
        }else{
            $markets = $rpc_ret['data'];
        }

        $chain_network = $request->post('chain_network') ? $request->post('chain_network') : 'testnet';

            if ($chain_network == 'main_network') {
                $network_type = 0;// 主网
            }else{
                $network_type = 1;// 测试网
            }

        $markets = array_column($markets,NULL,'name');
        $db_markets = ExchangeCoins::find()->where(['enable'=>1])->select('stock,money,limit_amount as min_amount,taker_fee,maker_fee')->orderBy('listorder DESC')->asArray()->all();
        $db_market = [];
        foreach ($db_markets as $x){
            if($x['stock'].$x['money'] == $market){
                $db_market = $x;
            }
        }
        if (empty($db_market)){
            Jinglan::error_message('_The_Market_Error_');
        }

        if(!in_array($market, array_keys($markets))){
            Jinglan::error_message('_The_Market_Error_');
        }
        if($amount < $db_market['min_amount']){
            Jinglan::error_message('_Exceed_Minimum_Transaction_Limits_');
        }
        if($amount < $markets[$market]['min_amount']){
            Jinglan::error_message('_Exceed_Minimum_Transaction_Limits_');
        }
        if ($pride <= 0){
            Jinglan::error_message('_Price_Invalid_');
        }
//        if($taker_fee_rate >= 1 || $taker_fee_rate < 0){
//            Jinglan::error_message('_Taker_Fee_Rate_Invalid_');
//        }
//        if($maker_fee_rate >= 1 || $maker_fee_rate < 0){
//            Jinglan::error_message('_Maker_Fee_Rate_Invalid_');
//        }
//        $taker_fee_rate = '0.000001';
//        $maker_fee_rate = '0.000001';
        $source = strtolower($request->post('os'));
        $taker_fee_rate = $db_market['taker_fee'];// >= 0.0001 ? $db_market['taker_fee'] : '0.000001';
        $maker_fee_rate = $db_market['maker_fee'];// >= 0.0001 ? $db_market['maker_fee'] : '0.000001';



        //1.查询交易所是否有余额，若有且足够，直接操作交易所，不够，从银行转入，
        $rpc_method = 'balance.query';
        $rpc_params = [intval($uid)];
        $rpc_ret = $rpc->do_rpc($rpc_method, $rpc_params);

        if ($rpc_ret['code'] == 0) {
            Jinglan::error_message($rpc_ret['data']);
        } else {
            $assets = $rpc_ret['data'];
        }

        $asset_param = $side == 1 ? 'stock' : 'money';
        $bank_symbol = $markets[$market][$asset_param];

        $need_available = $side==1 ? $amount : $amount*$pride;
        if ($side == 2){//对调
            //$need_fee = $amount * $pride * $db_market['maker_fee'];
            $need_fee = 0;
        }else{
            //$need_fee = $amount * $db_market['taker_fee'];
            $need_fee = 0;
        }

        $limit_rate = Coin::find()->where(['symbol'=>$bank_symbol])->select('sell_limit')->asArray()->one();
        if (empty($limit_rate)) {
            $limit_rate = 1;
        }else{
            $limit_rate = $limit_rate["sell_limit"]/100;
        }

        if($assets[$bank_symbol]['available'] >= ($need_available + $need_fee)){//交易所 有余额，够本次交易
                if (($assets[$bank_symbol]['available']* $limit_rate) < $amount){//银行可用余额+交易所可用余额<提交值
                    //Jinglan::error_message("今日可卖出数量不足");
                }
        }else{
            //1.0 查银行地址
            

            //1.查询银行币种
            $bank_coin = MemberWallet::find()->where(['uid'=>$uid,'coin_symbol'=>'_'.$bank_symbol.'_','status'=>1,'network'=>$network_type])->select('addr')->orderBy('id desc')->asArray()->one();
            $bank_coin_addr = $bank_coin['addr'];
            $bank_available_balance = Bank::getAvailableBalance($uid,$bank_symbol);

            if ($bank_available_balance + $assets[$bank_symbol]['available'] < ($need_available + $need_fee)){//银行可用余额+交易所可用余额<提交值
                Jinglan::error_message("_Total_available_is_not_enough_");
            }else{//银行余额够，从银行转入此次差价到交易所

                if (($bank_available_balance + $assets[$bank_symbol]['available']* $limit_rate) < $amount){//银行可用余额+交易所可用余额<提交值
                    //Jinglan::error_message("今日可卖出数量不足");
                }

                $bank_balance = Bank::getBalance($uid,$bank_symbol);
                $lack = ($need_available + $need_fee) - $assets[$bank_symbol]['available'];
                $transaction = Yii::$app->db->beginTransaction();
                try{
                    $chain_network = $request->post('chain_network') ? $request->post('chain_network') : 'testnet';

                    if ($chain_network == 'main_network') {
                        $network_type = 0;// 主网
                    }else{
                        $network_type = 1;// 测试网
                    }
                    $balance_model = new BalanceLog();
                    $balance_model->type = 10;//1:充值，10:取出
                    $balance_model->member_id = $uid;
                    $balance_model->coin_symbol = $bank_symbol;
                    $balance_model->addr = $bank_coin_addr;
                    $balance_model->change = -$lack;
                    $balance_model->balance = $bank_balance - $lack;
                    $balance_model->fee = 0.0;
                    $balance_model->detial_type = 'exchange';
                    $balance_model->network = $network_type;

                    if(!$balance_model->save(false)){
                        Jinglan::error_message('_Save_error_');
                    }
                    //更新交易所余额
                    $rpc_ret = $rpc->do_rpc('balance.update', [intval($uid),$bank_symbol,"deposit",$balance_model->attributes['id'],strval($lack),['id'=>$balance_model->attributes['id']]]);
                    if ($rpc_ret['code'] == 0) {
                        $transaction->rollBack();
                        Jinglan::error_message($rpc_ret['data']);
                    } else {//更新成功
                        $transaction->commit();
                    }
                }catch (\Exception $e){
                    $transaction->rollBack();
                    Jinglan::error_message($e->getMessage());
                }
            }
        }
        //1.end

        //send
        $rpc_method = 'order.put_limit';
        $rpc_params = [intval($uid), $market, intval($side), $amount, $pride, $taker_fee_rate, $maker_fee_rate, $source];
        $rpc_ret = $rpc->do_rpc($rpc_method, $rpc_params);
        if($rpc_ret['code'] == 0){
            Jinglan::error_message($rpc_ret['data']);
        }else{
            $order_ret = $rpc_ret['data'];
        }
        if (is_array($order_ret)){
            Reward::order(intval($uid),$bank_symbol,(double)$need_available);
            Jinglan::success_message();
        }else{
            Jinglan::error_message();
        }
    }

    public static function cancelOrder($uid)
    {
        $request = Yii::$app->request;

        // 获取参数
        $market   = $request->post('market');
        $order_id = $request->post('order_id');

        // 检测参数
        Jinglan::check_empty($market, '_The_Market_Can_Not_Be_Empty_');
        Jinglan::check_empty($order_id, '_Order_ID_Cannot_Be_Empty_');

        // 服务端发起请求
        $rpc_method = 'order.cancel';
        $rpc_params = [intval($uid), $market, intval($order_id)];
        $rpc = new VesRPC();
        $rpc_ret = $rpc->do_rpc($rpc_method, $rpc_params);

        if($rpc_ret['code'] == 0){
            Jinglan::error_message($rpc_ret['data']);
        }else{
            $result = $rpc_ret['data'];
        }

        if (is_array($result)){
            Jinglan::success_message();
        }else{
            Jinglan::error_message();
        }
    }
}