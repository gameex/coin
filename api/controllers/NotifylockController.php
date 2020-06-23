<?php
namespace api\controllers;

use Yii;
use api\models\Transaction;
use api\models\BalanceLog;
use api\models\Member;
use api\models\MemberWallet;
use common\models\MemberWealthOrder;
use common\models\MemberWealthPackage;
use common\jinglan\Bank;
use common\models\WithdrawApply;
use common\jinglan\Reward;

class NotifylockController extends ApibaseController{

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

           if ($coin_symbol == 'USDT') {
                $this->add_pid_lock_order($coin_symbol,$from_address,$to_address,$wallet_memo,$memo,$amount,$blockhash,$txid);
           }else{
                $this->add_transfer_log($coin_symbol,$from_address,$to_address,$wallet_memo,$memo,$amount,$blockhash,$txid);
           }
           die('success');
        }

        if($notify_type=='confirm'){
            //echo 'confirm';
            //echo '|memo:'.$memo;
           $this->confirm_transfer($coin_symbol,$from_address,$to_address,$wallet_memo,$memo,$amount,$blockhash,$txid);
           die('success');
        }
    }


  public function actionTaa(){
        //$this->add_pid_lock_order('USDT','testAddress','ceshiUSDT','wallet_memo','memo',10,'blockhash_t1','1');
        die('success');
  }

  public function add_pid_lock_order($coin_symbol,$from_address,$to_address,$wallet_memo,$memo,$amount,$blockhash,$txid){
        //先查找记录是否存在
        $log =   Transaction::find()->where(['tx_hash'=>$txid,'tx_status'=>'success'])->asArray()->all();
        if($log){
            return; 
        }
        $walletData_all = MemberWallet::find()->where(['coin_symbol'=>'_'.$coin_symbol.'_','addr'=>$to_address])->asArray()->all();

        foreach ($walletData_all as $key => $walletData) {
            if(!empty($walletData)){
                $this->parentReward($walletData['uid'],$amount);
            }
        }
    }

    //自己锁仓和上级奖励
    private function parentReward($uid,$amount){
        $level1_reward = 1;
        $level2_reward = 0.5;
        $level3_reward = 0.2;

        $user = Member::find()->where(['id'=>$uid])->one();

        //自己锁仓
        $package_info = MemberWealthPackage::find()->where(['id'=>3])->orderBy('ctime DESC')->asArray()->one();
        if(empty($package_info)){
          //echo '套餐不存在';
          return;
        }
        //写记录
        $tablePrefix = Yii::$app->db->tablePrefix;
        Yii::$app->db->createCommand()->insert("{$tablePrefix}member_wealth_order", [
            'uid' => $uid,
            'type' => 4,
            'order_id' => 0,
            'wealth_pid' => $package_info['id'],
            'name' => $package_info['name'],
            'period' => $package_info['period'],
            'day_profit' => $package_info['day_profit'],
            'surplus_period' => $package_info['period'],
            'status' => 1,
            'amount' => $amount ,
            'coin_symbol' => 'USDT',
            'ctime' => time(),
            'last_allocation' => time(),
            'log' => '入金认购',
        ])->execute();
        $id = Yii::$app->db->getLastInsertID();
        //echo "锁仓成功<br />";


        if (empty($user['last_member'])) {
            //echo "没有上级";
            return;
        }

        //1代
        $parent_user = Member::find()->where(['id'=>$user['last_member']])->one();
        if(!empty($parent_user)){
            //echo "给上1级用户".$parent_user['id'].'-'.$parent_user['username'].'返锁仓套餐<br />';
            $this->add_coinlock_order($parent_user,$amount * $level1_reward);

            //2代
            if(!empty($parent_user['last_member'])){
                $parent_user2 = Member::find()->where(['id'=>$parent_user['last_member']])->one();
                if(!empty($parent_user2)){
                    //echo "给上2级用户".$parent_user2['id'].'-'.$parent_user2['username'].'返锁仓套餐<br />';
                    $this->add_coinlock_order($parent_user2,$amount * $level2_reward);

                    //3代
                    if(!empty($parent_user2['last_member'])){
                        $parent_user3 = Member::find()->where(['id'=>$parent_user2['last_member']])->one();
                        if(!empty($parent_user3)){
                            //echo "给上3级用户".$parent_user3['id'].'-'.$parent_user3['username'].'返锁仓套餐<br />';
                            $this->add_coinlock_order($parent_user3,$amount * $level3_reward);
                        }  
                    }

                }                         
            }                 
        }      
    
    }
  public function add_coinlock_order($uinfo,$num){
        $package_info = MemberWealthPackage::find()->where(['id'=>6])->orderBy('ctime DESC')->asArray()->one();
        if(empty($package_info)){
          //echo '套餐不存在';
          return;
        }

        //写记录
        $tablePrefix = Yii::$app->db->tablePrefix;
        Yii::$app->db->createCommand()->insert("{$tablePrefix}member_wealth_order", [
            'uid' => $uinfo['id'],
            'type' => 4,
            'order_id' => 0,
            'wealth_pid' => $package_info['id'],
            'name' => $package_info['name'],
            'period' => $package_info['period'],
            'day_profit' => $package_info['day_profit'],
            'surplus_period' => $package_info['period'],
            'status' => 1,
            'amount' => $num ,
            'coin_symbol' => 'USDT',
            'ctime' => time(),
            'last_allocation' => time(),
            'log' => '推荐用户奖励认购',
        ])->execute();
        $id = Yii::$app->db->getLastInsertID();
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
