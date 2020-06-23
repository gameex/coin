<?php
/**
 * Created by PhpStorm.
 * User: op
 * Date: 2018-05-30
 * Time: 19:45
 */

namespace api\controllers;

use common\models\TransactionBtc as Transaction;
use jinglan\bitcoin\Unspent;
use Yii;
use Denpa\Bitcoin\Client as BitcoinClient;
use api\models\Coin;
use jinglan\bitcoin\Balance;

class TransactionbtcController extends ApibaseController
{
    public $modelClass = '';

    public function init(){
        parent::init();
    }

    public function actionPrepare()
    {//1000000000000000000
        $request = Yii::$app->request;

        $language = $request->post('language');
        $select = $language == 'en_us' ? 'usd' : 'cny';

        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);
        $coin_symbol = strtoupper($request->post('coin_symbol'));
        $this->check_empty($coin_symbol,'_Address_Type_Not_Empty_');

        $coin_addr = $request->post('wallet_addr');

        $this->check_empty($coin_addr,'_MoneyAddress_Not_Empty_');

        $coins = Coin::find()->select("id,symbol,".$select)->where(['symbol'=>$coin_symbol, 'enable'=>1])->asArray()->one();

        if(empty($coins)){
            $this->error_message('_MoneyType_Wrong_');
        }

        $ret = array();

        switch ($coin_symbol){
            case 'BTC':
                //100000000
                $ret['coin_symbol'] = $coin_symbol;
                $ret['coin_unit'] = 'BTC';

                //3.unspent_outputs
                $btc_unspent = new Unspent();
                $rst = $btc_unspent->do_curl($coin_addr);
                if($rst['code'] == 0){
                    $this->error_message($rst['data']);
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
                $ret['balance'] = $balance;
                $ret['exchange_rate'] = $coins[$select];

                //2.交易手续费建议值 0.0001BTC
                $current = 0.0001;
                $ret['low'] = $btc_balance->sctonum($current / 10);
                $ret['high'] = strval($current * 100);
                $ret['current'] = strval($current);

                array_multisort(array_column($unspent_outputs,'value'),SORT_ASC,$unspent_outputs);
                $ret['unspent'] = $unspent_outputs;
                break;
            case 'ETH':
                $this->error_message('_Request_ETH_Interface_');
                break;
            default:
                //$this->error_message('获取'.$coins['symbol'].'余额待开发');
                break;
        }
        $this->success_message($ret);
    }

    //C-8-2.发送交易信息
    public function actionSend(){
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
        $tx_model = new Transaction;
        $tx_model->type = 1;//1:钱包转账交易 2:存入银行 3:取出银行 4:场外交易
        $tx_model->member_id = $uinfo['id'];
        $tx_model->coin_symbol = $coin_symbol;
        $tx_model->from = $coin_addr;
        $tx_model->input = $input;
        $tx_model->to = $to;
        $tx_model->value_dec = $value_dec;
        $tx_model->fee = $fee;
        $tx_model->data = $data;
        $tx_model->raw = $raw;
        $tx_model->tx_status = 'prepare';
        $tx_model->network = $network_type;
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
                case 'ETH':

                    break;
            }
        }else{
            $this->error_message(json_encode($tx_model->getFirstErrors()));
            $this->error_message('_Trading_Prepares_Failure_');
        }
    }
}