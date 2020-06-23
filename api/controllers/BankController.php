<?php 
/*
* name: xiaocai
* date: 2018-8-27 12:00
*/
namespace api\controllers;

use Yii;
use api\models\Coin;
use jinglan\ethereum\EthereumRPC;
use api\models\MemberWallet;
use api\models\Transaction;
use jinglan\bitcoin\Unspent;
use jinglan\bitcoin\Balance;
use common\models\TransactionBtc;
use Denpa\Bitcoin\Client as BitcoinClient;
use Denpa\Bitcoin\Omnicore as OmnicoreClient;
use yii\data\Pagination;
use common\jinglan\WithdrawCash;
use common\models\WithdrawApply;

class BankController extends ApibaseController
{
    public $modelClass = '';

    // 银行充值最小金额限制
    protected $limit_amount = 0.0001;// ETH
    protected $limit_amount_btc = 0.0001;// BTC

    // 银行转出最低额度
    protected $limit_trun_out = 0.0001;

    public function init(){
        parent::init();
    }

    // 充值前准备(ETH)
    public function actionPrepare()
    {
        $request      = Yii::$app->request;
        $access_token = $request->post('access_token');
        $uinfo        = $this->checkToken($access_token);

        // 获取参数信息
        $coin_symbol  = $request->post('coin_symbol');
        $coin_addr  = $request->post('wallet_addr');
        $main_network = $request->post('main_network')=='main_network' ? 'main_network' : 'testnet';
        $language     = $request->post('language')=='en_us' ? "en_us" : "zh_cn";
        $select       = $language == 'en_us' ? 'usd' : 'cny';
        $this->check_empty($coin_symbol, '_Currency_Sign_Not_Empty_');
        $coin_symbol  = strtoupper($coin_symbol);
        $this->check_empty($coin_addr, '_MoneyAddress_Not_Empty_');

        // 判断货币类型是否存在
        $map = [
            'symbol' => $coin_symbol,
        ];
        $coins = Coin::find()->where($map)->one();
        if(empty($coins)){
            $this->error_message('_MoneyType_Wrong_');
        }

        // 
        $ret = [];
        switch ($coin_symbol){
            case 'BTC':
                $this->error_message('_BTC_Recharge_Service_To_Be_Developed_');
                break;
            case 'ETH':
                //1.先获取余额和汇率
                $rpc_method = 'eth_getBalance';
                $rpc_params = [$coin_addr, "latest"];
                $rpc = new EthereumRPC($rpc_method, $rpc_params);
                $rpc_ret = $rpc->do_rpc();
                if($rpc_ret['code'] == 0){
                    $this->error_message($rpc_ret['data']);
                }else{
                    $balance = hexdec($rpc_ret['data']) / pow(10,$coins['ram_token_decimals']);
                    $ret['balance'] = strval($rpc->sctonum($balance));
                }
                $ret['exchange_rate'] = $coins[$select];

                //2.Gas Limit
                $gas_limit = 21000;
                $ret['gas'] = '0x'.dechex($gas_limit);

                //3.gasPrice
                $rpc_method = 'eth_gasPrice';
                $rpc_params = [];
                $rpc = new EthereumRPC($rpc_method, $rpc_params);
                $rpc_ret = $rpc->do_rpc();
                if($rpc_ret['code'] == 0){
                   $this->error_message($rpc_ret['data']);
                }
                $gas_price = $rpc_ret['data'];
                $gas_price = "0x3B9ACA00";
                $ret['gas_price'] = $gas_price;
                $gwei = hexdec($gas_price);
                $eth = $rpc->sctonum($gwei * $gas_limit / pow(10,$coins['ram_token_decimals']));

                $low = $rpc->sctonum($eth / 100);
                $high = (string)$rpc->sctonum($eth * 100);
                $current = $rpc->sctonum($eth);

                $ret['low'] =$low;
                $ret['high'] = $high;
                $ret['current'] = $current;

                //4.nonce
                $rpc_method = 'eth_getTransactionCount';
                $rpc_params = [$coin_addr, "latest"];
                $rpc = new EthereumRPC($rpc_method, $rpc_params);
                $rpc_ret = $rpc->do_rpc();
                if($rpc_ret['code'] == 0){
                    $this->error_message($rpc_ret['data']);
                }
                $ret['nonce'] = $rpc_ret['data'];
                break;
            default:
                //1.先获取余额和汇率
                $rpc_method = 'eth_call';
                $params = ['to'=>$coins['ram_token_addr'], 'data'=>"0x70a08231000000000000000000000000" .substr($coin_addr, 2)];
                $rpc_params = [$params, "latest"];
                $rpc = new EthereumRPC($rpc_method, $rpc_params);
                $rpc_ret = $rpc->do_rpc();
                if($rpc_ret['code'] == 0){
                    $this->error_message($rpc_ret['data']);
                }else{
                    $balance = hexdec($rpc_ret['data']) / pow(10,$coins['ram_token_decimals']);
                    $ret['balance'] = strval($rpc->sctonum($balance));
                }
                $ret['exchange_rate'] = $coins[$select];

                //2.Gas Limit
                $gas_limit = 336000;
                $ret['gas'] = '0x'.dechex($gas_limit);

                //3.gasPrice
                // $rpc_method = 'eth_gasPrice';
                // $rpc_params = [];
                // $rpc = new EthereumRPC($rpc_method, $rpc_params);
                // $rpc_ret = $rpc->do_rpc();
                // if($rpc_ret['code'] == 0){
                // $this->error_message($rpc_ret['data']);
                // }
                // $gas_price = $rpc_ret['data'];
                $gas_price = "0x98bca5a00";
                $ret['gas_price'] = $gas_price;
                $gwei = hexdec($gas_price);
                $eth = $rpc->sctonum($gwei * $gas_limit / pow(10,$coins['ram_token_decimals']));

                $low = (string)$rpc->sctonum($eth / 100);
                $high = (string)$rpc->sctonum($eth * 100);
                $current = (string)$rpc->sctonum($eth);

                $ret['low'] =$low;
                $ret['high'] = $high;
                $ret['current'] = $current;

                //4.nonce
                $rpc_method = 'eth_getTransactionCount';
                $rpc_params = [$coin_addr, "latest"];
                $rpc = new EthereumRPC($rpc_method, $rpc_params);
                $rpc_ret = $rpc->do_rpc();
                if($rpc_ret['code'] == 0){
                    $this->error_message($rpc_ret['data']);
                }
                $ret['nonce'] = $rpc_ret['data'];
                break;
        }
        $ret['coin_symbol'] = $coin_symbol;
        $ret['coin_unit'] = $coins->unit;

        // 获取该用户线上银行对应的地址
        $map = [
            'uid' => (int)$uinfo['id'],
            'coin_symbol' => '_ETH_',
        ];
        $member_wallet = MemberWallet::find()->select('addr')->where($map)->one();
        if (!$member_wallet) {
            $this->error_message('_User_Bank_Wallet_Address_Does_Not_Exist_');
        }
        $ret['limit_amount'] = $this->limit_amount;
        $ret['bank_addr'] = $member_wallet->addr;
        $this->success_message($ret);

    }

    // 银行充值(ETH)
    public function actionSend()
    {
        $request      = Yii::$app->request;
        $access_token = $request->post('access_token');
        $uinfo        = $this->checkToken($access_token);

        // 获取参数
        $coin_symbol   = $request->post('coin_symbol');
        $coin_addr     = $request->post('wallet_addr');
        $to            = $request->post('to');
        $value_dec     = $request->post('value');
        $gas_price_hex = $request->post('gas_price');
        $gas_hex       = $request->post('gas');
        $nonce_hex     = $request->post('nonce');
        $data          = $request->post('data');
        $raw           = $request->post('raw');
        $language      = $request->post('language')=='en_us' ? 'en_us' : 'zh_cn';
        $select        = $language == 'en_us' ? 'usd' : 'cny';
        $chain_network = $request->post('chain_network') ? $request->post('chain_network') : 'testnet';
        $this->check_empty($coin_symbol, '_Currency_Sign_Not_Empty_');
        $this->check_empty($coin_addr, '_MoneyAddress_Not_Empty_');
        $this->check_empty($to, '_Receiving_Address_Not_Empty_');
        $this->check_empty($value_dec, '_The_Amount_Of_Recharge_Can_Not_Be_Empty_');
        $this->check_empty($gas_price_hex, '_GasPrice_Not_Empty_');
        $this->check_empty($gas_hex, '_Gas_Not_Empty_');
        $this->check_empty($nonce_hex, '_Nonce_Not_Empty_');
        $this->check_empty($raw, '_Transaction_Data_Not_Empty_');

        if ($chain_network == 'main_network') {
            $network_type = 0;// 主网
        }else{
            $network_type = 1;// 测试网
        }

        // 判断货币类型是否存在
        $map = [
            'symbol' => $coin_symbol,
        ];
        $coins = Coin::find()->where($map)->one();
        if(empty($coins)){
            $this->error_message('_MoneyType_Wrong_');
        }

        // 判断余额是否充足
        if($coin_symbol == 'ETH'){
            $rpc_method = 'eth_getBalance';
            $rpc_params = [$coin_addr, "latest"];
            $rpc = new EthereumRPC($rpc_method, $rpc_params);
            $rpc_ret = $rpc->do_rpc();
            if($rpc_ret['code'] == 0){
                $this->error_message($rpc_ret['data']);
            }else{
                $balance = $rpc->sctonum(hexdec($rpc_ret['data']) / pow(10,$coins['ram_token_decimals']));
            }
        }else{
            $rpc_method = 'eth_call';
            $params = ['to'=>$coins['ram_token_addr'], 'data'=>"0x70a08231000000000000000000000000" .substr($coin_addr, 2)];
            $rpc_params = [$params, "latest"];
            $rpc = new EthereumRPC($rpc_method, $rpc_params);
            $rpc_ret = $rpc->do_rpc();
            if($rpc_ret['code'] == 0){
                $this->error_message($rpc_ret['data']);
            }else{
                $balance = $rpc->sctonum(hexdec($rpc_ret['data']) / pow(10,$coins['ram_token_decimals']));
            }
        }
        
        //手续费
        $commission = $rpc->sctonum(hexdec($gas_price_hex) * hexdec($gas_hex) / pow(10,$coins['ram_token_decimals']));
        

        if((double)$commission + $value_dec > $balance){
            $this->error_message('_TranVal_HandlFee_Exceed_Balance_');
        }

        $value_hex = '0x' . $rpc->bc_dechex($value_dec * pow(10, $coins['ram_token_decimals']));
        
        $gas_price_dec = $rpc->sctonum(hexdec($gas_price_hex) / pow(10,$coins['ram_token_decimals']));
        $gas_dec = $rpc->sctonum(hexdec($gas_hex) / pow(10,$coins['ram_token_decimals']));
        $nonce_dec = hexdec($nonce_hex);

        //交易先写入db
        $tx_model = new Transaction;
        $tx_model->type = 2;//1:钱包转账交易 2:存入银行 3:取出银行 4:场外交易
        $tx_model->member_id = $uinfo['id'];
        $tx_model->coin_symbol = $coin_symbol;
        $tx_model->from = $coin_addr;
        $tx_model->to = $to;
        $tx_model->value_hex = $value_hex;
        $tx_model->value_dec = strval($value_dec);
        $tx_model->gas_hex = $gas_hex;
        $tx_model->gas_dec = $gas_dec;
        $tx_model->gas_price_hex = $gas_price_hex;
        $tx_model->gas_price_dec = $gas_price_dec;
        $tx_model->nonce_hex = $nonce_hex;
        $tx_model->nonce_dec = strval($nonce_dec);
        $tx_model->data = $data;
        $tx_model->raw = $raw;
        $tx_model->tx_status = 'prepare';
        $tx_model->network = $network_type;
        if($tx_model->save()){
            //写入成功，开始请求节点
            switch ($coin_symbol) {
                case 'BTC':
                    $this->error_message('_BTC_Tran_To_Developed_');
                    break;
                default:
                    $rpc_method = 'eth_sendRawTransaction';
                    $rpc_params = [$raw];
                    $rpc = new EthereumRPC($rpc_method, $rpc_params);
                    $rpc_ret = $rpc->do_rpc();
                    if($rpc_ret['code'] == 0){
                        //节点请求失败，更改db中状态
                        $tx_model->tx_status = 'fail';
                        $tx_model->rpc_response = $rpc_ret['data'];
                        $tx_model->save();
                        $this->error_message('_Recharge_failure_');
                    }else{
                        $tx_model->tx_hash = $rpc_ret['data'];
                        $tx_model->tx_status = 'pending';
                        $tx_model->save();
                        $this->success_message();
                    }
                    break;
            }
        }else{
            // $this->error_message(json_encode($tx_model->getFirstErrors()));
            $this->error_message('_Trading_Prepares_Failure_');
        }

    }

    // 充值前准备(BTC)
    public function actionPrepareBtc()
    {
        $request      = Yii::$app->request;
        $access_token = $request->post('access_token');
        $uinfo        = $this->checkToken($access_token);

        // 获取参数信息
        $coin_symbol  = $request->post('coin_symbol');
        $coin_addr  = $request->post('wallet_addr');
        $main_network = $request->post('main_network')=='main_network' ? 'main_network' : 'testnet';
        $language     = $request->post('language')=='en_us' ? "en_us" : "zh_cn";
        $select       = $language == 'en_us' ? 'usd' : 'cny';
        $this->check_empty($coin_symbol, '_Currency_Sign_Not_Empty_');
        $coin_symbol  = strtoupper($coin_symbol);
        $this->check_empty($coin_addr, '_MoneyAddress_Not_Empty_');

        // 判断货币类型是否存在
        $map = [
            'symbol' => $coin_symbol,
        ];
        $coins = Coin::find()->where($map)->one();
        if(empty($coins)){
            $this->error_message('_MoneyType_Wrong_');
        }

        $ret = [];

        switch ($coin_symbol){
            case 'BTC':
                $ret['coin_symbol'] = $coin_symbol;
                $ret['coin_unit'] = 'BTC';

                //3.unspent_outputs
                $btc_unspent = new Unspent();
                $rst = $btc_unspent->do_curl($coin_addr);
                if($rst['code'] == 0){
                    if ($rst['data'] == 'Invalid Bitcoin Address') {
                        $this->error_message('_Invalid_Bitcoin_Address_');
                    }else{
                        $this->error_message($rst['data']);
                    }
                }else{
                    $unspent_outputs = array();
                    $btc_balance = new Balance();
                    if($rst['data'] == 0){//该BTC地址还没有余额
                        //$this->error_message('该BTC地址还没有余额，无法交易');
                        $balance = 0;

                    }else{
                        foreach($rst['data'] as $x){
                            $temp['txid'] = $x['tx_hash_big_endian'];
                            $temp['tx_output_n'] = $x['tx_output_n'];
                            $temp['value'] = $x['value'];
                            $temp['value_dec'] = $x['value'] / 100000000;

                            array_push($unspent_outputs, $temp);
                        }
                        //1.查余额
                        $balance = $btc_balance->calc($unspent_outputs);
                    }
                }

                array_multisort(array_column($unspent_outputs,'value'),SORT_ASC,$unspent_outputs);
                $ret['unspent'] = $unspent_outputs;

                $ret['balance'] = $balance;
                $ret['exchange_rate'] = $coins[$select];

                //2.交易手续费建议值 0.0001BTC
                $current = 0.0001;
                $ret['low'] = $btc_balance->sctonum($current / 10);
                $ret['high'] = strval($current * 100);
                $ret['current'] = strval($current);

                // 获取该用户线上银行对应的地址
                $map = [
                    'uid' => (int)$uinfo['id'],
                    'coin_symbol' => '_BTC_',
                ];
                $member_wallet = MemberWallet::find()->select('addr')->where($map)->one();
                if (!$member_wallet) {
                    $this->error_message('_User_Bank_Wallet_Address_Does_Not_Exist_');
                }
                $ret['limit_amount'] = $this->limit_amount_btc;
                $ret['bank_addr'] = $member_wallet->addr;
                break;
            case 'USDT':
                $ret['coin_symbol'] = $coin_symbol;
                $ret['coin_unit'] = 'USDT';

                //3.unspent_outputs
                $btc_unspent = new Unspent();
                $rst = $btc_unspent->do_curl($coin_addr);
                if($rst['code'] == 0){
                    if ($rst['data'] == 'Invalid Bitcoin Address') {
                        $this->error_message('_Invalid_Bitcoin_Address_');
                    }else{
                        $this->error_message($rst['data']);
                    }
                }else{
                    $unspent_outputs = array();
                    $btc_balance = new Balance();
                    if($rst['data'] == 0){//该BTC地址还没有余额
                        //$this->error_message('该BTC地址还没有余额，无法交易');
                        $balance = 0;

                    }else{
                        foreach($rst['data'] as $x){
                            $temp['txid'] = $x['tx_hash_big_endian'];
                            $temp['tx_output_n'] = $x['tx_output_n'];
                            $temp['value'] = $x['value'];
                            $temp['value_dec'] = $x['value'] / 100000000;

                            array_push($unspent_outputs, $temp);
                        }
                        //1.查余额
                        $balance = $btc_balance->calc($unspent_outputs);
                    }
                }

                array_multisort(array_column($unspent_outputs,'value'),SORT_ASC,$unspent_outputs);
                $ret['unspent'] = $unspent_outputs;

                $ret['balance'] = $balance;
                $ret['exchange_rate'] = $coins[$select];

                //2.交易手续费建议值 0.0001BTC
                $current = 0.0001;
                $ret['low'] = $btc_balance->sctonum($current / 10);
                $ret['high'] = strval($current * 100);
                $ret['current'] = strval($current);

                // 获取该用户线上银行对应的地址
                $map = [
                    'uid' => (int)$uinfo['id'],
                    'coin_symbol' => '_USDT_',
                ];
                $member_wallet = MemberWallet::find()->select('addr')->where($map)->one();
                if (!$member_wallet) {
                    $this->error_message('_User_Bank_Wallet_Address_Does_Not_Exist_');
                }
                $ret['limit_amount'] = $this->limit_amount_btc;
                $ret['bank_addr'] = $member_wallet->addr;
                break;
            case 'ETH':
                $this->error_message('_The_Currency_Recharge_Service_Has_Not_Yet_Been_Developed_');
                break;
            default:
                $this->error_message('_The_Currency_Recharge_Service_Has_Not_Yet_Been_Developed_');
                break;
        }
        $this->success_message($ret);

    }

    // 银行充值(BTC)
    public function actionSendBtc()
    {
        $request = Yii::$app->request;
        $language = $request->post('language');
        $select = $language == 'en_us' ? 'usd' : 'cny';

        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);
        $coin_symbol = strtoupper($request->post('coin_symbol'));
        $this->check_empty($coin_symbol,'_Address_Type_Not_Empty_');

        $coin_addr = $request->post('wallet_addr');

        $this->check_empty($coin_addr,'_MoneyAddress_Not_Empty_');

        $to = $request->post('to');
        $value_dec = $request->post('value');
        $input = $request->post('input');
        $fee = $request->post('fee');
        $data = $request->post('data');
        $raw = $request->post('raw');

        $this->check_empty($to,'_Receiving_Address_Not_Empty_');
        $this->check_empty($value_dec,'_Transaction_Not_Empty_');
        $this->check_empty($input,'_Input_Not_Empty_');
        $this->check_empty($fee,'_HandlingFee_Not_Empty_');
        $this->check_empty($raw,'_Transaction_Data_Not_Empty_');

        $coins = Coin::find()->select("id,symbol,".$select)->where(['symbol'=>$coin_symbol, 'enable'=>1])->asArray()->one();

        if(empty($coins)){
            $this->error_message('_MoneyType_Wrong_');
        }

        //3.unspent_outputs
        $btc_unspent = new Unspent();
        $rst = $btc_unspent->do_curl($coin_addr);
        if($rst['code'] == 0){
            $this->error_message($rst['data']);
        }else{
            if($rst['data'] == 0){
                $this->error_message('_BTC_Address_No_Balance_Not_Traded_');
            }

            $unspent_outputs = array();
            foreach($rst['data'] as $x){
                $temp['txid'] = $x['tx_hash_big_endian'];
                $temp['tx_output_n'] = $x['tx_output_n'];
                $temp['value'] = $x['value'];
                $temp['value_dec'] = $x['value'] / 100000000;

                array_push($unspent_outputs, $temp);
            }
        }

        //1.查余额
        $btc_balance = new Balance();
        $balance = $btc_balance->calc($unspent_outputs);

        if($fee + $value_dec > $balance){
            $this->error_message('_TranVal_HandlFee_Exceed_Balance_');
        }
        //decoderawtransaction begin 查询txid是否已存在

        //decoderawtransaction end
        $chain_network = $request->post('chain_network') ? $request->post('chain_network') : 'testnet';

        if ($chain_network == 'main_network') {
            $network_type = 0;// 主网
        }else{
            $network_type = 1;// 测试网
        }

        //交易先写入db
        $tx_model              = new TransactionBtc;
        $tx_model->type        = 2;//1:钱包转账交易 2:存入银行 3:取出银行 4:场外交易
        $tx_model->member_id   = $uinfo['id'];
        $tx_model->coin_symbol = $coin_symbol;
        $tx_model->from        = $coin_addr;
        $tx_model->input       = $input;
        $tx_model->to          = $to;
        $tx_model->value_dec   = $value_dec;
        $tx_model->fee         = $fee;
        $tx_model->data        = $data;
        $tx_model->raw         = $raw;
        $tx_model->tx_status   = 'prepare';
        $tx_model->network     = $network_type;
        if($tx_model->save()){
            //写入成功，开始请求节点
            switch ($coin_symbol) {
                case 'BTC':
                    $bitcoind = new BitcoinClient();
                    $req = $bitcoind->request('sendrawtransaction',[$raw]);
                    if($req['code'] == 0){
                        //节点请求失败，更改db中状态
                        $tx_model->tx_status = 'fail';
                        $tx_model->rpc_response = $req['data'];
                        $tx_model->save();
                        $this->error_message($req['data']);
                    }else{
                        if($req['data']->get()){
                            $tx_model->tx_hash = $req['data']->get();
                            $tx_model->tx_status = 'pending';
                            $tx_model->save();
                            $this->success_message();
                        }else{
                            $this->error_message('_BTC_Address_Ver_Failure_');
                        }
                    }
                    break;
                case 'USDT':
                    $bitcoind = new BitcoinClient();
                    $req = $bitcoind->request('sendrawtransaction',[$raw]);
                    if($req['code'] == 0){
                        //节点请求失败，更改db中状态
                        $tx_model->tx_status = 'fail';
                        $tx_model->rpc_response = $req['data'];
                        $tx_model->save();
                        $this->error_message($req['data']);
                    }else{
                        if($req['data']->get()){
                            $tx_model->tx_hash = $req['data']->get();
                            $tx_model->tx_status = 'pending';
                            $tx_model->save();
                            $this->success_message();
                        }else{
                            $this->error_message('_BTC_Address_Ver_Failure_');
                        }
                    }
                    break;
                case 'ETH':
                    $this->error_message('_ETH_Recharge_Is_Not_Supported_Temporarily_');
                    break;
                default :
                    $this->error_message('_The_Currency_Recharge_Service_Has_Not_Yet_Been_Developed_');
                    break;
            }
        }else{
            $this->error_message(json_encode($tx_model->getFirstErrors()));
            $this->error_message('_Trading_Prepares_Failure_');
        }
    }

    // 提现前准备
    public function actionWithdrawPrepare()
    {
        $request      = Yii::$app->request;
        $access_token = $request->post('access_token');
        $os = strtolower($request->post('os'));
        if (!empty($access_token)){
            if (in_array($os, ['ios','android'])){
                $uinfo = $this->checkToken($access_token);
            }else{
                $uinfo = $this->memberToken($access_token);
            }
            $uid = $uinfo['id'];
        }else{
            $uid = 0;
        }
        
        WithdrawCash::withdrawPrepare($uid);
    }
    // 转出（提现）申请(银行=>KK钱包)
    public function actionTurnOut()
    {
        $request      = Yii::$app->request;
        $access_token = $request->post('access_token');
        $uinfo        = $this->checkToken($access_token);

        WithdrawCash::turnOut($uinfo['id']);
    }

    // 交易所财务日志
    public function actionRechargeDetails()
    {
        $request      = Yii::$app->request;
        $access_token = $request->post('access_token');
        $uinfo        = $this->checkToken($access_token);

        WithdrawCash::financialLog($uinfo['id']);
    }

    // 提现申请记录
    public function actionApplyLog()
    {
        $request      = Yii::$app->request;
        $access_token = $request->post('access_token');
        $uinfo        = $this->checkToken($access_token);

        WithdrawCash::applyLog($uinfo['id']);
    }

    // 删除提现申请记录
    public function actionRevokeApply()
    {
        $request      = Yii::$app->request;
        $access_token = $request->post('access_token');
        $uinfo        = $this->checkToken($access_token);

        WithdrawCash::revokeApply($uinfo['id']);
    }
}