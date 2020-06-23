<?php
namespace api\controllers;

use Yii;
use api\models\Transaction;
use api\models\BalanceLog;
use api\models\MemberWallet;
use common\jinglan\Bank;
use common\models\WithdrawApply;
use common\jinglan\Reward;

class NotifyController extends ApibaseController{

    public $modelClass = '';

    protected $_md5_key = '';

    public function init(){
        parent::init();
        $this->_md5_key = Yii::$app->config->info('WALLET_API_KEY');
    }

    public function actionNotify(){
        $params = Yii::$app->request->post();

        //var_dump($params);

        if(!isset($params['sign'])){
            die('sign not set');
        }

        $sign = $params['sign'];
        
        unset($params['chain_network']);

        $params_final = $this->filterPara($params);

        $sign_str =  $this->buildRequestMysign($params_final);

        if ($sign_str !== $sign ) {
            die('sign error');   
        }

        $notify_type = $params['notify_type'];
        $from_address= $params['from_address'];
        $to_address= $params['to_address'];
        $amount = $params['amount'];
        $blockhash = '';
        $txid = $params['tx_id']; //链上交易ID
        $coin_symbol = $params['coin_symbol']; 
        if(isset($params['wallet_memo'])){
            $wallet_memo = $params['wallet_memo'];
        }else{
            $wallet_memo = '';
        }

        $memo = $params['transaction_id'];

        if($notify_type=='payment'){

           $this->add_transfer_log($coin_symbol,$from_address,$to_address,$wallet_memo,$memo,$amount,$blockhash,$txid);
           die('success');
        }

        if($notify_type=='confirm'){
            //echo 'confirm';
            //echo '|memo:'.$memo;
           $this->confirm_transfer($coin_symbol,$from_address,$to_address,$wallet_memo,$memo,$amount,$blockhash,$txid);
           die('success');
        }
    }

  public function add_transfer_log($coin_symbol,$from_address,$to_address,$wallet_memo,$memo,$amount,$blockhash,$txid){
        //先查找记录是否存在
        $log =   Transaction::find()->where(['tx_hash'=>$txid,'tx_status'=>'success'])->asArray()->all();
        if($log){
            return; 
        }

        $walletData_all = MemberWallet::find()->where(['coin_symbol'=>'_'.$coin_symbol.'_','addr'=>$to_address,'memo'=>$wallet_memo])->asArray()->all();

        foreach ($walletData_all as $key => $walletData) {
            if(!empty($walletData))
                
                //var_dump($walletData);

                $transaction0 = Yii::$app->db->beginTransaction();
                // 数据库存储记录
                $transaction = new Transaction;
                $transaction->type          = 2;
                $transaction->member_id     = $walletData['uid'];
                $transaction->coin_symbol   = $coin_symbol;
                $transaction->from          = $from_address;
                $transaction->to            = $to_address;
                $transaction->value_hex     = '0x';
                $transaction->value_dec     = (string)$amount;
                $transaction->gas_hex       = '0x';
                $transaction->gas_dec       = '0';
                $transaction->gas_price_hex = '0x';
                $transaction->gas_price_dec = '0';
                $transaction->nonce_hex     = '0x0';
                $transaction->nonce_dec     = '0';
                $transaction->tx_hash     = $txid;
                $transaction->tx_status     = 'success';
                $transaction->network       = 0;
                if (!$transaction->save()) {
                    //var_dump($transaction);
                    $transaction0->rollBack();
                    die('Save Error#transaction');
                }
                $transaction_no = $transaction->attributes['id'];
                //加币
                $bank_balance2 = Bank::getBalance($walletData['uid'],$coin_symbol);
                $balance_model3 = new BalanceLog();
                $balance_model3->type = 1;//1:充值，10:取出
                $balance_model3->member_id = $walletData['uid'];
                $balance_model3->coin_symbol = $coin_symbol;
                $balance_model3->addr = $to_address;
                $balance_model3->change = (double)$amount;
                $balance_model3->balance = $bank_balance2 + (double)$amount;
                $balance_model3->fee = 0.0;
                $balance_model3->detial_type = 'chain';
                $balance_model3->network = 0;

                if(!$balance_model3->save(false)){
                    $transaction0->rollBack();
                    die('add balace failed');
                }

                Reward::recharge($walletData['uid'],$coin_symbol,(double)$amount);

                $transaction0->commit();
        }    
    }

    public function confirm_transfer($coin_symbol,$from_address,$to_address,$wallet_memo,$memo,$amount,$blockhash,$txid){

        $logs = Transaction::find()->where(['tx_hash'=>$memo,'tx_status'=>'pending'])->all();
        //echo '|logs:'.count($logs);
        foreach ($logs as  $value) {
            $value->tx_hash = $txid;
            $value->tx_status = 'success';
            $value->save();
        }
        $logs2 = WithdrawApply::find()->where(['tx_hash'=>$memo,'status'=>5])->all();
        //echo '|logs:'.count($logs);
        foreach ($logs2 as  $value) {
            $value->status = 2;
            $value->save();
            Reward::withdraw($value->member_id,$value->coin_symbol,$value->value_dec);
        }        
    }

    /**
     * 除去数组中的空值和签名参数
     * @param $para 签名参数组
     * return 去掉空值与签名参数后的新签名参数组
     */
    public function paraFilter($para) {
        $para_filter = array();
        foreach ($para as $key => $val) {
            if($key == "sign" )continue;
            else    $para_filter[$key] = $para[$key];
        }
        return $para_filter;
    }
    /**
     * 对数组排序
     * @param $para 排序前的数组
     * return 排序后的数组
     */
    public function argSort($para) {
        ksort($para);
        reset($para);
        return $para;
    }
    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    public function createLinkstring($para) {
        $arg  = "";
        foreach ($para as $key => $val) {
            $arg.=$key."=".$val."&";
        }
        //去掉最后一个&字符
        $arg = substr($arg,0,strlen($arg)-1);
        //如果存在转义字符，那么去掉转义
        if(get_magic_quotes_gpc()){
            $arg = stripslashes($arg);
        }
        return $arg;
    }
    /**
     * 生成md5签名字符串
     * @param $prestr 需要签名的字符串
     * @param $key 私钥
     * return 签名结果
     */
    public function md5Sign($prestr, $key) {
        $prestr = $prestr . $key;
        return md5($prestr);
    }

    public function filterPara($para_temp){
        $para_filter = $this->paraFilter($para_temp);//除去待签名参数数组中的空值和签名参数
        return $this->argSort($para_filter);//对待签名参数数组排序
    }
    /**
     * 生成签名结果
     * @param $para_sort 已排序要签名的数组
     * @return string 签名结果字符串
     */
    public function buildRequestMysign($para_sort) {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = $this->createLinkstring($para_sort);
        $mysign = "";
        $mysign = $this->md5Sign($prestr, $this->_md5_key);

        return $mysign;
    }
    /**
     * 生成要发送的参数数组
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数数组
     */
    public function buildRequestPara($para_temp) {
        $para_sort = $this->filterPara($para_temp);//对待签名参数进行过滤
        $para_sort['sign'] = $this->buildRequestMysign($para_sort);//生成签名结果，并与签名方式加入请求提交参数组中
        return $para_sort;
    }    


}