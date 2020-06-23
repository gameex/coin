<?php
/**
 * Created by PhpStorm.
 * User: op
 * Date: 2018-05-30
 * Time: 12:06
 */

namespace api\controllers;

use Yii;
use api\models\Coin;
use jinglan\walletapi\WalletRPC;
use api\models\MemberWallet;
use jinglan\bitcoin\Balance;

class HomeController extends ApibaseController
{
    public $modelClass = '';

    public function init(){
        parent::init();
    }

    public function actionMain(){
        $request = Yii::$app->request;

        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);

        $language = $request->post('language');
        $language = $language == 'en_us'?"en_us":'zh_cn';
        $select = $language == 'en_us' ? 'usd' : 'cny';

        $chain_network = $request->post('chain_network') ? $request->post('chain_network') : 'testnet';

        if ($chain_network == 'main_network') {
            $network_type = 0;// 主网
            $network_type2 = 1;
        }else{
            $network_type = 1;// 测试网
            $network_type2 = 0;
        }

        $temp = MemberWallet::findOne(['uid'=>$uinfo['id'],'coin_symbol'=>'ETH','network'=>$network_type2]);
        if($temp){
            $temp->network = $network_type;
            $temp->save();
        }

        $temp2 = MemberWallet::findOne(['uid'=>$uinfo['id'],'coin_symbol'=>'BTC','network'=>$network_type2]);
        if($temp2){
            $temp2->network = $network_type;
            $temp2->save();
        }

        $coins = Coin::find()->andWhere(['enable'=>1])->orWhere(['ram_status'=>1])->select('symbol,icon,usd,cny,ram_status,ram_token_addr,unit,ram_token_decimals')->asArray()->all();
        $coins = array_column($coins,NULL,'symbol');

        $list = MemberWallet::find()->where(['uid'=>$uinfo['id']])->andWhere(['in','coin_symbol',array_keys($coins)])->select('coin_symbol,addr,memo,seed,balance,status')->asArray()->all();
        if(empty($list)){
            $this->error_message('_No_Data_Query_');
        }else{
            G('begin');
            //2.查询货币类型资产
            foreach($list as $key => &$val){
                if(isset($_POST['chain_network']) && $_POST['chain_network'] == 'main_network' && $val['coin_symbol'] == 'TOKEN KKCC'){
                    unset($list[$key]);
                    continue;
                }
                if(isset($_POST['chain_network']) && $_POST['chain_network'] != 'main_network' && $val['coin_symbol'] == 'UVC Token'){
                    unset($list[$key]);
                    continue;
                }
                if($val['status'] == 0){
                    unset($list[$key]);
                    continue;
                }
                $val['unit'] = $coins[$val['coin_symbol']]['unit'];
                $val['icon'] = parent::get_user_avatar_url($coins[$val['coin_symbol']]['icon']);
                $exchange_rate = $coins[$val['coin_symbol']][$select];
                /*
                switch ($val['coin_symbol']){
                    case 'USDT':
                    case 'BTC': 
                        $btc_balance = new Balance();
                        $rpc_ret = $btc_balance->getbalance($val['addr']);

                        if($rpc_ret['code'] == 0){
                            $this->error_message($rpc_ret['data']);
                        }else{
                            $balance = $rpc_ret['data'];
                        }
                        $balance = $val['balance'] == 0 ? '0' : $val['balance'];
                        $money = $balance * $exchange_rate;
                        $this->update_balance($val['coin_symbol'], $val['addr'], $balance);
                        break;
                    case 'ETH':
                        $rpc_method = 'eth_getBalance';
                        $rpc_params = [$val['addr'], "latest"];
                        $rpc = new EthereumRPC($rpc_method, $rpc_params);
                        $rpc_ret = $rpc->do_rpc();
                        if($rpc_ret['code'] == 0){
                            $this->error_message($rpc_ret['data']);
                        }else{
                            $balance = hexdec($rpc_ret['data']) / 1000000000000000000;
                            $balance = $rpc->sctonum($balance);
                        }
                        $money = $balance * $exchange_rate;
                        $this->update_balance($val['coin_symbol'], $val['addr'], $balance);
                        break;
                    default:
                        if($coins[$val['coin_symbol']]['ram_status'] == 1){
                            //去查询代币余额
                            $rpc_method = 'eth_call';
                            $params = ['to'=>$coins[$val['coin_symbol']]['ram_token_addr'], 'data'=>"0x70a08231000000000000000000000000" .substr($val['addr'], 2)];
                            $rpc_params = [$params, "latest"];
                            $rpc = new EthereumRPC($rpc_method, $rpc_params);
                            $rpc_ret = $rpc->do_rpc();
                            if($rpc_ret['code'] == 0){
                                $this->error_message($rpc_ret['data']);
                                //$balance = '0.00';
                            }else{
                                $balance = hexdec($rpc_ret['data']) / pow(10,$coins[$val['coin_symbol']]['ram_token_decimals']);
                                $balance = $rpc->sctonum($balance);
                            }
                        }else{
                            $this->error_message('获取'.$val['coin_symbol'].'余额待开发');
                        }
                        $money = $balance * $exchange_rate;
                        $this->update_balance($val['coin_symbol'], $val['addr'], $balance);
                        break;
                }*/
                $proto = Yii::$app->config->info('WALLET_API_PROTOCAL');;
                $host = Yii::$app->config->info('WALLET_API_URL');;
                $port = Yii::$app->config->info('WALLET_API_PORT');;
                $_md5_key = Yii::$app->config->info('WALLET_API_KEY');;       
                $rpc = new WalletRPC($proto,$host,$port,$_md5_key );
                $rpc_ret = $rpc->account_info($val['seed']);
                if($rpc_ret['code'] == 0){
                    $this->error_message($rpc_ret['data']);
                }else{
                    $balance = $rpc_ret['data']['balance'];
                }

                $money = $balance * $exchange_rate;
                $this->update_balance($val['coin_symbol'], $val['addr'], $val['memo'], $balance);

                $val['balance'] = strval($balance);
                $val['money'] = sprintf("%0.2f", $money);
                $val['ram_status'] = $coins[$val['coin_symbol']]['ram_status'];
                $val['ram_token_addr'] = $coins[$val['coin_symbol']]['ram_token_addr'];
                $val['g_times'] = G('begin',$val['coin_symbol'],6).'s';
                //G($val['coin_symbol']);
                //p(G('begin',$val['coin_symbol'],6).'s');

            }
            $ret = ['list'=>array_values($list),'total_money'=>(string)array_sum(array_column(array_values($list),'money'))];
            $this->success_message($ret);
        }
    }
}