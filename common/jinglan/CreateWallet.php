<?php 
/*
* name: xiaocai
* date: 2018-8-24 11:40
*/
namespace common\jinglan;

use Yii;
use api\models\Coin;
use api\models\MemberWallet;
use jinglan\walletapi\WalletRPC;
use Denpa\Bitcoin\Client as BitcoinClient;
use Denpa\Bitcoin\Omnicore as OmnicoreClient;

class CreateWallet extends Jinglan
{
    public static function create_v2($uid=0)
    {
        $request = Yii::$app->request;
        if (empty($uid) || $uid == 0) {
            Jinglan::error_message('_Please_Check_If_The_User_ID_Is_Correct_');
        }
        $uinfo['id'] = (int)$uid;

        // 获取所需参数
        // $chain_network = $request->post('chain_network') ? $request->post('chain_network') : 'testnet';
        // $coin_symbol   = $request->post('coin_symbol');
        $chain_network = empty($_POST['chain_network']) ? 'testnet' : $_POST['chain_network'];
        $coin_symbol   = $_POST['coin_symbol'];
        $generate   = $_POST['generate'];
        Jinglan::check_empty($coin_symbol, '_The_address_type_can_not_be_empty');
        if ($chain_network == 'main_network') {
            $network_type = 0;// 主网
        }else{
            $network_type = 1;// 测试网
        }
        // 判断该币种类型是否存在
        $coin = Coin::find()->where(['symbol' => $coin_symbol])->asArray()->one();
        if (!$coin) {
            Jinglan::error_message('该币种类型不存在！');
        }
        if($coin['recharge_enable']!='1'){
            Jinglan::error_message('该币种暂停充值');
        }
        // 判断是否已经生成该币种类型的地址
        $member_wallet = MemberWallet::find()->where(['uid' => intval($uinfo['id'])])->andWhere(['coin_symbol' => '_'.$coin_symbol.'_'])->andWhere(['network' => $network_type])->one();
        if ($member_wallet) {
            $ret_data['addr'] =$member_wallet->addr;
            $ret_data['memo'] =$member_wallet->memo;
            Jinglan::success_message($ret_data);            
        }else{
            if($generate!='1'){
                $ret_data['addr'] ='-';
                $ret_data['memo'] ='';            
                Jinglan::success_message($ret_data);                  
            }      
        }

        // 开始生成地址
        $memberWallets = new MemberWallet();

        $proto = Yii::$app->config->info('WALLET_API_PROTOCAL');;
        $host = Yii::$app->config->info('WALLET_API_URL');;
        $port = Yii::$app->config->info('WALLET_API_PORT');;
        $_md5_key = Yii::$app->config->info('WALLET_API_KEY');;       
        $rpc = new WalletRPC($proto,$host,$port,$_md5_key );     
        $rpc_ret = $rpc->account_create($uinfo['id'],$coin_symbol);
        if($rpc_ret['code'] == 0){
            // 生成失败
            // var_dump($rpc_ret);  var_dump($uinfo['id']);var_dump($coin_symbol);die();
            //Jinglan::error_message('_Failed_to_generate_ETH_address_Please_try_again_later_');
            Jinglan::error_message($rpc_ret['data']);
        }else{
            // 生成成功
            $ret_address = $rpc_ret['data']['address'];
            $ret_memo = $rpc_ret['data']['memo'];
            $ret_seed = $rpc_ret['data']['seed'];
            $memberWallets->uid = (int)$uinfo['id'];
            // 判断是否为代币，存储代币或者ETH标识
            $memberWallets->coin_symbol = '_'.$coin_symbol.'_';
            $memberWallets->balance = '0';
            $memberWallets->addr = $ret_address;
            $memberWallets->memo = $ret_memo;
            $memberWallets->seed = $ret_seed;
            $memberWallets->block = 0;
            $memberWallets->status = 1;
            $memberWallets->network = $network_type;

            /*插入数据前再判断是否生成该类型的地址防止重复生成*/
            $has_this_wallet = MemberWallet::find()->where(['uid' => intval($uinfo['id'])])->andWhere(['coin_symbol' => $memberWallets->coin_symbol])->andWhere(['network' => $network_type])->one();
            if ($has_this_wallet) {
                $ret_data['addr'] =$has_this_wallet->addr;
                $ret_data['memo'] =$has_this_wallet->memo;
                Jinglan::success_message($ret_data);
            }
            /*结束判断*/

            if ($memberWallets->save()) {
                $session_return_way = isset($_POST['return_way']) ? $_POST['return_way'] : '';
                // 判断返回类型
                if ($session_return_way == 'array') {
                    return $ret_address;
                }else{
                    $ret_data['addr'] = $ret_address;
                    $ret_data['memo'] = $ret_memo;              
                    Jinglan::success_message($ret_data);
                }
            }else{
                Jinglan::error_message('Save Error#member_wallet');
            }
        }


    }


    // 生成地址[货币标识，密码]
    public static function generate_addr($symbol, $pwd='')
    {
        $_POST['chain_network'] = 'main_network';
        // 返回数据类型
        $result['code'] = 0;
        $result['data'] = '';
        $result['message'] = '';

        // 默认密码123456
        $pwd = $pwd=='' ? '123456' : $pwd;
        switch ($symbol) {
            case 'ETH':
                $map1 = 'personal_newAccount';
                $map2 = [(string)$pwd];
                $rpc = new EthereumRPC($map1, $map2);
                $rpc_ret = $rpc->do_rpc();
                if($rpc_ret['code'] == 0){
                    // 生成失败
                    $result['message'] = $rpc_ret['data'];
                }else{
                    // 生成成功
                    $result['code'] = 1;
                    $result['data'] = [
                        'symbol' => 'ETH',
                        'addr'   => $rpc_ret['data'],
                        'pwd'    => $pwd,
                    ];
                }
                break;

            case 'BTC':
                $bitcoind = new BitcoinClient();
                $req = $bitcoind->request('getnewaddress',[(string)$pwd, "legacy"]);
                if($req['code'] == 0){
                    // 生成失败
                    $result['message'] = $req['data'];
                }else{
                    // 生成成功
                    $result['code'] = 1;
                    $result['data'] = [
                        'symbol'       => 'BTC',
                        'addr'         => $req['data']->get(),
                        'pwd'          => $pwd,
                    ];
                }
                break;

            case 'USDT':
                $omnicore = new OmnicoreClient();
                $req = $omnicore->request('getaccountaddress',[(string)$pwd]);
                if($req['code'] == 0){
                    // 生成失败
                    $result['message'] = $req['data'];
                }else{
                    // 生成成功
                    $result['code'] = 1;
                    $result['data'] = [
                        'symbol' => 'USDT',
                        'addr'   => $req['data']->get(),
                        'pwd'    => $pwd,
                    ];
                }
                break;
            
            default:
                $result['message'] = '不支持生成该币种类型地址！';
                break;
        }

        return $result;
    }
}
