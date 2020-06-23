<?php
namespace api\controllers;

use api\models\MemberWallet;
use common\jinglan\Bank;
use api\models\BalanceLog;
use jinglan\ves\VesRPC;
use common\models\OtcCoinList;
use common\models\OtcOrder;
use common\models\OtcAppeal;
use common\helpers\StringHelper;
use common\helpers\FileHelper;
use yii\web\UploadedFile;
use yii\data\Pagination;
use Yii;
use common\jinglan\Trade;
use api\models\Coin;
use common\models\MemberProceeds;
use jinglan\sms\SMS;
use common\models\OtcMarket;

class OtcController extends ApibaseController{
    public $modelClass = '';

    public function init(){
        parent::init();
    }


    //获取币种列表
    public function actionCoinList(){
        $request = Yii::$app->request;
    
        $tablePrefix = Yii::$app->db->tablePrefix;
        $coins = (new \yii\db\Query())
            ->select('a.coin_id,a.coin_name,b.icon')
            ->from("{$tablePrefix}otc_coinlist AS a")
            ->leftJoin("{$tablePrefix}coins AS b",'a.coin_id = b.id')
            ->where(['a.status'=>1])
            ->orderBy('a.coin_id asc')
            ->All();    
        foreach($coins as &$item){
            $item['icon'] = parent::get_user_avatar_url($item['icon']);
        }   
        if ($coins) {
            $this->success_message($coins,'_Success_');
        }else{
            $this->error_message('_No_Data_Query_');
        }
    }
    //获取余额
    public function actionBalance(){
        $request = Yii::$app->request;

        $access_token = $request->post('access_token');
        $os = strtolower($request->post('os'));
        if (!empty($access_token)){
            $uinfo = $os == 'web' ? $this->memberToken($access_token) : $this->checkToken($access_token);
            $uid = $uinfo['id'];
        }else{
            $uid = 0;
        }

        Trade::balance_v2($uid);
    }
    //获取市场列表
    public function actionMarketList(){
        
        $request = Yii::$app->request;

        
        $side = intval($request->post('side'))== 1 ? 1 : 2;
        // 添加排序规则
        $order_by = $side==1?'a.price_usd':'a.price_usd desc';
        
        $coin_name = $request->post('coin_name')=='' ? 'BTC' : $request->post('coin_name');

        $tablePrefix = Yii::$app->db->tablePrefix;
        $markets = (new \yii\db\Query())
            ->select('a.id,a.uid,a.coin_name,a.min_num,a.max_num,a.price_usd,card_enable,alipay_enable,wechat_enable,b.nickname,b.head_portrait,a.order_count,a.deal_count,a.deal_amount')
            ->from("{$tablePrefix}otc_market AS a")
            ->leftJoin("{$tablePrefix}member AS b",'a.uid = b.id')
            ->where(['a.status'=>1,'a.side'=>$side,'coin_name'=>$coin_name])
            ->orderBy($order_by)
            ->All();    
        foreach($markets as $key => &$item){
            // if ($item['deal_amount'] >= $item['max_num']){
            //     unset($item[$key]);
            //     continue;
            // }
            // $item['max_num'] = $item['max_num'] - $item['deal_amount'];
            // if ($item['max_num'] < $item['min_num']){
            //     $item['max_num'] = $item['min_num'];
            // }
            $item['max_num'] = sprintf("%.2f", $item['max_num']);
            if ($item['order_count'] <= 0){
                $item['deal_rate'] = '0';
            }else{
                $item['deal_rate'] = $item['deal_count'] / $item['order_count'] * 100;
            }
            $item['deal_rate'] = sprintf("%.2f", $item['deal_rate']);
            $item['deal_rate'] = $item['deal_rate'] ?? '0.00';
            $item['head_portrait'] = parent::get_user_avatar_url($item['head_portrait']);
        }   
        if ($markets) {
            $this->success_message($markets,'_Success_');
        }else{
            $this->error_message('_No_Data_Query_');
        }
    
    }
    //发布市场订单  商家认证  检测他是否有该币种的广告了  发布的时候 ---  银行卡支付宝微信 检测是否有收款信息  如果发布的是卖单 检测卖家的银行额度
    public function actionPublishInfo(){
    
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);
        $var = Yii::$app->config->info('OTC_MERCHANTS');
        if($var == 1){
            if ($uinfo['otc_merchant'] != 1){
                $this->error_message('_Please_complete_the_merchant_certification_first_');
            }
            
        }
        $uid = $uinfo['id'];
        
        $side = intval($request->post('side'))== 1 ? 1 : 2;
        
        $coin_name = $request->post('coin_name');

        $coin = Coin::find()->select("id")->where(['symbol'=>$coin_name,/*'ram_status'=>1*/])->asArray()->all();

        if(empty($coin)){
   
            $this->error_message('_Otc_Coin_Error_');
       
        }else{
            
            $coin_id = $coin[0]['id'];
            
        }

        $otc_coin = OtcCoinList::find()->where(['coin_id'=>$coin_id])->asArray()->one();
        if ($otc_coin['status'] != 1){
            $this->error_message('_The_current_currency_is_temporarily_disabled_');
        }

        
        $min_num = intval(floatval($request->post('min_num'))*100)/100;

        $max_num =intval(floatval($request->post('max_num'))*100)/100;

        if ($min_num < $otc_coin['limit_amount']){
            //$this->error_message('_Exceed_Minimum_Transaction_Limits_');
            $this->error_message('该币种最小发布数量为'.$min_num);

        }

        if($max_num<=$min_num){
            
            $this->error_message('_Otc_Amount_Error_');
            
        }
        
        $price_usd =intval(floatval($request->post('price_usd'))*100)/100;

        if($price_usd <= 0){
            $this->error_message('_Otc_Price_Error_');
        }
        $alipay_enable =  intval($request->post('alipay_enable'))== 1 ? 1 : 0;
        $wechat_enable =  intval($request->post('wechat_enable'))== 1 ? 1 : 0;
        $card_enable = intval($request->post('card_enable'))== 1 ? 1 : 0;
        if ($side == 1){
            $pay_info = MemberProceeds::find()->where(['member_id'=>$uid,'is_delete'=>0])->select('proceeds_type')->asArray()->all();
            $pay_info = array_column($pay_info,'proceeds_type');

            if ($alipay_enable && !in_array('alipay', $pay_info)){
                $this->error_message('_The_corresponding_collection_method_has_not_been_added_yet_');
            }

            if ($wechat_enable && !in_array('wxpay', $pay_info)){
                $this->error_message('_The_corresponding_collection_method_has_not_been_added_yet_');
            }

            if ($card_enable && !in_array('bank', $pay_info)){
                $this->error_message('_The_corresponding_collection_method_has_not_been_added_yet_');
            }
        }


        if ($alipay_enable + $wechat_enable + $card_enable == 0){
            $this->error_message('_Please_choose_at_least_one_method_of_collection_');
        }

        $note = $request->post('note');

        //查询是否有该币种地址，没有提示去创建地址
        $bank_coin = MemberWallet::find()->where(['uid'=>$uid,'coin_symbol'=>'_'.$coin_name.'_','status'=>1,'network'=>0])->select('addr')->asArray()->one();
        if (empty($bank_coin)){
           // $this->error_message('_The_Bank_Account_Of_The_Currency_Type_Is_Not_Generated_');
        }

        //查询该用户已发布的有效广告
        $tablePrefix = Yii::$app->db->tablePrefix;

        $otc_count = (new \yii\db\Query())
            ->from("{$tablePrefix}otc_market ")
            ->where(['uid'=>$uid,'status'=>1,'side'=>$side,'coin_id'=>$coin_id])
            ->count();
        if($otc_count >= $otc_coin['max_register_num']){
            $this->error_message('_The_release_limit_has_been_exceeded_');
        }
        if ($side == 1){//发布的卖单，检查用户余额
            $_POST['chain_network'] = 'main_network';
            $_POST['return_way'] = 'array';
            $balance_all = Trade::balance_v2($uid);// 成功返回数据，失败返回false
            if ($balance_all){
                $balance_all = array_column($balance_all[0], NULL, 'name');
                if($balance_all[$coin_name]['available'] < $max_num){
                    $this->error_message('_Total_user_available_assets_are_insufficient_');
                }
            }
        }
    
        Yii::$app->db->createCommand()->insert("{$tablePrefix}otc_market", [    
            'side' => $side,
            'uid' => $uid,    
            'coin_name' => $coin_name,   
            'coin_id' => $coin_id,      
            'min_num' => $min_num,  
            'max_num' => $max_num,
            'price_usd' => $price_usd,  
            'alipay_enable' => $alipay_enable,  
            'wechat_enable' => $wechat_enable,                  
            'card_enable' => $card_enable, 
            'note' => $note,    
            'status' => 1,                                              
           ])->execute();
            
        $id = Yii::$app->db->getLastInsertID();   
        
        if ($id) {
            $this->success_message($id,'_Success_');
        }else{
            $this->error_message('_No_Data_Query_');
        }
        
    }
    
    //下单   一个用户只能同时有一个主动订单在交易中   如果是买币，要判断商家的余额是否足，如果够的话 那就冻结对应余额 不够的话，提示对方订单已过期，然后把对方的广告下架
    public function actionOrder(){
        $request = Yii::$app->request;

        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);
        $uid = $uinfo['id'];
        // $var = Yii::$app->config->info('OTC_MERCHANTS');
        // 修改为下单时候需要实名认证而不需要商家认证
        $var = Yii::$app->config->info('MEMBER_VERIFIED');
        if($var == 1){
            if ($uinfo['verified_status'] != 1){
                $this->error_message('_Users_Are_Not_Authenticated_By_Real_Names_');
            }
        }
        
        $side = intval($request->post('side'))== 1 ? 1 : 2; 
  
        $side2 = $side== 1 ? 2 : 1; 
                
        $uid2= intval($request->post('uid2'));  
        
        if($side==1){
            $seller_uid = $uid;
            $buyer_uid = $uid2;     
        }else{
            $seller_uid = $uid2;
            $buyer_uid = $uid;              
        }
        $uinfo2 = $this->getUserInfoById($uid2);
        
        if( $seller_uid == $buyer_uid)  {
                
            $this->error_message('_Otc_Object_Error_');
        
        }
        
        $coin_name = $request->post('coin_name');

        $coin = Coin::find()->select("id")->where(['symbol'=>$coin_name,/*'ram_status'=>1*/])->asArray()->all();
        
        if(empty($coin)){
   
            $this->error_message('_Otc_Coin_Error_');
       
        }else{
            
            $coin_id = $coin[0]['id'];
            
        }

        $otc_coin = OtcCoinList::find()->where(['coin_id'=>$coin_id])->asArray()->one();
        if ($otc_coin['status'] != 1){
            $this->error_message('_The_current_currency_is_temporarily_disabled_');
        }
                    
        $amount = intval(floatval($request->post('amount'))*100)/100;

        if($amount<=0){
            
            $this->error_message('_Otc_Amount_Error_');
            
        }

        //查询是否有该币种地址，没有提示去创建地址
        $bank_coin = MemberWallet::find()->where(['uid'=>$uid,'coin_symbol'=>'_'.$coin_name.'_','status'=>1,'network'=>0])->select('addr')->asArray()->one();
        if (empty($bank_coin)){
            //$this->error_message('_The_Bank_Account_Of_The_Currency_Type_Is_Not_Generated_');
        }

        //查询该用户是否有未完成的订单
        $tablePrefix = Yii::$app->db->tablePrefix;

        $otc_order = (new \yii\db\Query())
            ->from("{$tablePrefix}otc_order ")
            ->where(['and','status > 1',['or','seller_uid = '.$uid, 'buyer_uid = '.$uid]]);

        if ($otc_order->count() > 0){
            $set_cancel_time = 30*60;// 默认30分钟未付款则取消
            $otc_order = $otc_order->one();
            if (time()-strtotime($otc_order['order_time']) > $set_cancel_time) {
                $otc_order = OtcOrder::find()->where(['and','status > 1',['or','seller_uid = '.$uid, 'buyer_uid = '.$uid]])->one();
                $otc_order->status = 0;
                $otc_order->save();
            }else{
                $this->error_message('_There_are_still_outstanding_orders_');
            }
        }

        $market_id = intval($request->post('market_id'));
    
        $markets = (new \yii\db\Query())
            ->select('id,min_num,max_num,price_usd,card_enable,wechat_enable,alipay_enable,deal_amount')
            ->from("{$tablePrefix}otc_market ")
            ->where(['status'=>1,'side'=>$side2,'coin_name'=>$coin_name,'uid'=>$uid2,'id'=>$market_id])
            ->All();
            
        if($markets){
            
            // if($amount<$markets[0]['min_num']||$amount>($markets[0]['max_num'] - $markets[0]['deal_amount'])){
            if($amount<$markets[0]['min_num']||$amount>($markets[0]['max_num'])){
                $this->error_message('_Otc_Amount_Error_');
                
            }
            
            $price_usd = $markets[0]['price_usd'];
                
            $total_price_usd = intval(floatval($price_usd * $amount*100))/100 ;
            
            //$total_price_cny = $markets[0]['min_num'];
             
        }else{
            
            $this->error_message('_Otc_Market_Error_');
            
        }

        if ($side == 2){//购买，判断商家的余额是否充足
            $_POST['chain_network'] = 'main_network';
            $_POST['return_way'] = 'array';
            $balance_all = Trade::balance_v2($uid2);// 成功返回数据，失败返回false
            if ($balance_all){
                $balance_all = array_column($balance_all[0], NULL, 'name');
                if($balance_all[$coin_name]['available'] < $amount){//商家余额不够，下架广告
                    $update = Yii::$app->db->createCommand()->update("{$tablePrefix}otc_market",
                        array(
                            'status' => 0,
                        ),
                        "id=".$market_id
                    )->execute();
                    $this->error_message('_Information_has_been_lost_');
                }
            }else{
                $this->error_message('_Failed_To_Get_Bank_Account_Balance_');
            }
        }
        if ($side == 1){//下卖单，判断自己的余额是否充足
            $_POST['chain_network'] = 'main_network';
            $_POST['return_way'] = 'array';
            $balance_all = Trade::balance_v2($uid);// 成功返回数据，失败返回false
            if ($balance_all){
                $balance_all = array_column($balance_all[0], NULL, 'name');
                if($balance_all[$coin_name]['available'] < $amount){//自己余额不够，不能下单
                    $this->error_message('_Total_available_is_not_enough_');
                }
            }else{
                $this->error_message('_Failed_To_Get_Bank_Account_Balance_');
            }
            //查自己是否有对应的收款方式
            $pay_info = MemberProceeds::find()->where(['member_id'=>$uid,'is_delete'=>0])->select('proceeds_type')->asArray()->all();
            $pay_info = array_column($pay_info,'proceeds_type');
            if (empty($pay_info)){
                $this->error_message('_The_corresponding_collection_method_has_not_been_added_yet_');
            }
            $alipay_enable = $markets[0]['alipay_enable'];
            $wechat_enable = $markets[0]['wechat_enable'];
            $card_enable = $markets[0]['card_enable'];

            $awc = $alipay_enable+ $wechat_enable+$card_enable;
            switch ($awc){
                case 3:
                    break;
                case 2:
                    if($alipay_enable && $wechat_enable){
                        if(!in_array('alipay', $pay_info) && !in_array('wxpay', $pay_info)){
                            $this->error_message('_The_corresponding_collection_method_has_not_been_added_yet_');
                        }
                    }
                    if($wechat_enable && $card_enable){
                        if(!in_array('wxpay', $pay_info) && !in_array('bank', $pay_info)){
                            $this->error_message('_The_corresponding_collection_method_has_not_been_added_yet_');
                        }
                    }
                    if($alipay_enable && $wechat_enable){
                        if(!in_array('alipay', $pay_info) && !in_array('bank', $pay_info)){
                            $this->error_message('_The_corresponding_collection_method_has_not_been_added_yet_');
                        }
                    }
                    break;
                case 1:
                    if ($alipay_enable){
                        if(!in_array('alipay', $pay_info)){
                            $this->error_message('_The_corresponding_collection_method_has_not_been_added_yet_');
                        }
                    }
                    if ($wechat_enable){
                        if(!in_array('wxpay', $pay_info)){
                            $this->error_message('_The_corresponding_collection_method_has_not_been_added_yet_');
                        }
                    }
                    if ($card_enable){
                        if(!in_array('bank', $pay_info)){
                            $this->error_message('_The_corresponding_collection_method_has_not_been_added_yet_');
                        }
                    }
                    break;
            }

//            if ($alipay_enable && !in_array('alipay', $pay_info)){
//                $this->error_message('_The_corresponding_collection_method_has_not_been_added_yet_');
//            }
//
//            if ($wechat_enable && !in_array('wxpay', $pay_info)){
//                $this->error_message('_The_corresponding_collection_method_has_not_been_added_yet_');
//            }
//
//            if ($card_enable && !in_array('bank', $pay_info)){
//                $this->error_message('_The_corresponding_collection_method_has_not_been_added_yet_');
//            }
        }
    
        Yii::$app->db->createCommand()->insert("{$tablePrefix}otc_order", [ 
            'market_id' => $market_id,   
            'side' => $side,
            'seller_uid' => $seller_uid,    
            'buyer_uid' => $buyer_uid,   
            'other_uid' => $uid2,       
            'coin_id' => $coin_id,      
            'coin_name' => $coin_name,
            'amount' => $amount,  
            'price_usd' => $price_usd,  
            'total_price_usd' => $total_price_usd,                  
           //'total_price_cny' => $total_price_cny, 
            'order_time' => date("Y-m-d H:i:s",time()),     
            'status' => 2,                                              
           ])->execute();
            
        $id = Yii::$app->db->getLastInsertID();   

        // 下单成功，更新该广告下单次数order_count
        $update_otc_market = OtcMarket::find()->where(['id' => $market_id])->one();
        if ($update_otc_market) {
            $update_otc_market->order_count += 1;
            $update_otc_market->save();
        }
        
        if ($id) {
            //给广告发布者发送短信
            $_POST['sms_type'] = 'otc';
            //$sms = new SMS($uinfo2['mobile_phone'], strval(rand(100000,999999)));
            //$send_result = $sms->send();
            $this->success_message($id,'_Success_');
        }else{
            $this->error_message('_No_Data_Query_');
        }       
    }   
    
    
    //付款
    public function actionPay(){

        $request = Yii::$app->request;

        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);
        $uid = $uinfo['id'];
        
        $order_id = $request->post('order_id');
 
        $tablePrefix = Yii::$app->db->tablePrefix;
    
        $orders = (new \yii\db\Query())
            ->select('id')
            ->from("{$tablePrefix}otc_order ")
            ->where(['status'=>2,'buyer_uid'=>$uid,'id'=>$order_id])
            ->All();
                 
        if (!$orders) {
            $this->error_message('_No_Data_Query_');
        }
        
        $update = Yii::$app->db->createCommand()->update("{$tablePrefix}otc_order", 
          array(
            'pay_time' => date("Y-m-d H:i:s",time()), 
            'status' => 3,  
          ),
          "id=".$order_id
        )->execute();       
        $data['order_id'] = $order_id;  
        $data['status'] = 3;          
        if ($update) {
            $this->success_message($data,'_Success_');
        }else{
            $this->error_message('_No_Data_Query_');
        }       
    }
    
    //成交放币   交易成功后 冻结的钱要转账
    public function actionDeal(){

        $request = Yii::$app->request;

        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);
        $uid = $uinfo['id'];
        
        $order_id = $request->post('order_id');
 
        $tablePrefix = Yii::$app->db->tablePrefix;
    
        $orders = (new \yii\db\Query())
            ->select('*')
            ->from("{$tablePrefix}otc_order ")
            ->where(['seller_uid'=>$uid,'id'=>$order_id])
            ->one();
                 
        if (!$orders) {
            $this->error_message('_No_Data_Query_');
        }
        switch ($orders['status']){
            case 0:
                $this->error_message('_Order_cancelled_');
                break;
            case 1:
                $this->error_message('_The_order_has_been_completed_');
                break;
            case 2:
                $this->error_message('_The_order_has_not_been_paid_');
                break;
        }

        // 存储该订单对应的市场id（otc_market）以及成交数量
        $otc_market_id      = $orders['market_id'];
        $transaction_amount = $orders['amount'];

        $transaction = Yii::$app->db->beginTransaction();
        try{
            $update = Yii::$app->db->createCommand()->update("{$tablePrefix}otc_order",
                array(
                    'deal_time' => date("Y-m-d H:i:s",time()),
                    'status' => 1,
                ),
                "id=".$order_id
            )->execute();
            if ($update){//订单状态更新成功，开始执行转币
                try{
                    //1.先查余额
                    $_POST['chain_network'] = 'main_network';
                    $_POST['return_way'] = 'array';
                    $network_type = 0;// 主网
                    $balance_all = Trade::balance_v2($uid);// 成功返回数据，失败返回false
                    if ($balance_all){
                        $balance_all = array_column($balance_all[0], NULL, 'name');
                        $balance = $balance_all[$orders['coin_name']];
                        if($balance['available'] + $balance['oct_freeze'] < $orders['amount']){//卖家余额不够，出现此情况就是之前处理有问题，继续执行，扣成负值
                           /* $open=fopen("otc_deal_bug.txt","a+" );
                            fwrite($open,"\r\n########  BUG  ###########\r\n");
                            fwrite($open,json_encode($_POST)."\r\n");
                            fwrite($open,json_encode($orders)."\r\n");
                            fwrite($open,json_encode($balance)."\r\n");
                            fwrite($open,"\r\n########  BUG  ###########\r\n");
                            fclose($open);*/
                        }
                        //检测余额
                        if ($balance['bank_balance'] >= $orders['amount']){//银行余额足够

                        }else{//银行不够，从交易所转出差价到银行
                            $transaction2 = Yii::$app->db->beginTransaction();
                            try{
                                $lack = $orders['amount'] - $balance['bank_balance'];
                                $bank_balance = Bank::getBalance($uid,$balance['name']);

                                $balance_model = new BalanceLog();
                                $balance_model->type = 1;//1:充值，10:取出
                                $balance_model->member_id = $uid;
                                $balance_model->coin_symbol = $balance['name'];
                                $balance_model->addr = $balance['addr'];
                                $balance_model->change = $lack;
                                $balance_model->balance = $bank_balance + $lack;
                                $balance_model->fee = 0.0;
                                $balance_model->detial_type = 'exchange';
                                $balance_model->network = $network_type;

                                if(!$balance_model->save(false)){
                                    $transaction->rollBack();
                                    $this->error_message('_Try_Again_Later_');
                                }
                                //更新交易所余额
                                $rpc = new VesRPC();
                                $rpc_ret = $rpc->do_rpc('balance.update', [intval($uid),$balance['name'],"trade",$balance_model->attributes['id'],strval(-(float)$lack),['id'=>$balance_model->attributes['id']]]);
                                if ($rpc_ret['code'] == 0) {
                                    $transaction2->rollBack();
                                    $this->error_message($rpc_ret['data']);
                                } else {//更新成功
                                    $transaction2->commit();
                                }
                            }catch (\Exception $e){
                                $transaction->rollBack();
                                $this->error_message($e->getMessage());
                            }
                        }
                        //再查余额
                        $bank_balance = Bank::getBalance($uid,$balance['name']);
                        //卖家扣币
                        $balance_model = new BalanceLog();
                        $balance_model->type = 10;//1:充值，10:取出
                        $balance_model->member_id = $uid;
                        $balance_model->coin_symbol = $orders['coin_name'];
                        $balance_model->addr = $balance['addr'];
                        $balance_model->change = -$orders['amount'];
                        $balance_model->balance = $bank_balance - $orders['amount'];
                        $balance_model->fee = 0.0;
                        $balance_model->detial_type = 'otc';
                        $balance_model->network = $network_type;

                        if(!$balance_model->save(false)){
                            $transaction->rollBack();
                            $this->error_message('_Buckling_failure_');
                        }
                        //买家加币
                        //1.查询银行币种
                        $buyer_uid = $orders['buyer_uid'];
                        $bank_coin = MemberWallet::find()->where(['uid'=>$buyer_uid,'coin_symbol'=>'_'.$orders['coin_name'].'_','status'=>1,'network'=>$network_type])->select('addr')->asArray()->one();
                        $bank_coin_addr = $bank_coin['addr'];
                        $bank_balance2 = Bank::getBalance($buyer_uid,$orders['coin_name']);

                        $balance_model = new BalanceLog();
                        $balance_model->type = 1;//1:充值，10:取出
                        $balance_model->member_id = $buyer_uid;
                        $balance_model->coin_symbol = $orders['coin_name'];
                        $balance_model->addr = $bank_coin_addr;
                        $balance_model->change = $orders['amount'];
                        $balance_model->balance = $bank_balance2 + $orders['amount'];
                        $balance_model->fee = 0.0;
                        $balance_model->detial_type = 'otc';
                        $balance_model->network = $network_type;

                        if(!$balance_model->save(false)){
                            $transaction->rollBack();
                            $this->error_message('_Buyer_payment_failed_');
                        }
                    }else{
                        $transaction->rollBack();
                        $this->error_message('_Failed_To_Get_Bank_Account_Balance_');
                    }
                }catch (\Exception $e){
                    $transaction->rollBack();
                    $this->error_message($e->getMessage());
                }
            }else{
                $transaction->rollBack();
                $this->error_message('_Try_Again_Later_');
            }
        }catch (\Exception $e){
            $transaction->rollBack();
            $this->error_message($e->getMessage());
        }
        $transaction->commit();

        $data['order_id'] = $order_id;          
        $data['status'] = 1;                     
        if ($update) {
            /***************更新市场订单成交次数、成交率、已成交数额***************/
            $market = OtcMarket::find()->where(['id' => $otc_market_id])->one();
            if ($market) {
                $market->deal_count = $market->deal_count+1;
                $market->deal_rate = $market->deal_count / $market->order_count;
                $market->deal_amount = $market->deal_amount+$transaction_amount;
                // if ($market->deal_amount+$transaction_amount >= $market->max_num){
                //     $market->status = 2;
                // }
                $market->save();
            }
            /***************结束更新***************/
            $this->success_message($data,'_Success_');
        }else{
            $this->error_message('_No_Data_Query_');
        }       
    }
    
    //订单详情
    public function actionOrderInfo(){

        $request = Yii::$app->request;

        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);
        $uid = $uinfo['id'];
        
        $order_id = $request->post('order_id');
 
        $tablePrefix = Yii::$app->db->tablePrefix;
    
        $orders = (new \yii\db\Query())
            ->select('id,status,seller_uid,buyer_uid,price_usd,amount,coin_name,total_price_usd,order_time,pay_time,deal_time')
            ->from("{$tablePrefix}otc_order ")
            ->where(['and', "id=$order_id", ['or', "seller_uid={$uid}", "buyer_uid={$uid}"]])   
            ->All();
                 
        if (!$orders) {
            $this->error_message('_No_Data_Query_');
        }       

  
        $seller_info = (new \yii\db\Query())
            ->select('username,nickname,head_portrait,real_name,mobile_phone')
            ->from("{$tablePrefix}member AS a")
            ->leftJoin("{$tablePrefix}member_verified AS b",'a.id = b.uid')            
            ->where(['a.id'=>$orders[0]['seller_uid']])
            ->All();
            
        if($seller_info){
            $data['seller']['username'] = $seller_info[0]['username'];
            $data['seller']['nickname'] = $seller_info[0]['nickname'];
            $data['seller']['head_portrait'] =  parent::get_user_avatar_url($seller_info[0]['head_portrait']);  
            $data['seller']['real_name'] = $seller_info[0]['real_name'];
            $data['seller']['mobile_phone'] = $seller_info[0]['mobile_phone'];    
        }else{
        
            $this->error_message('_No_Data_Query_');
        
        }         

        $buyer_info =(new \yii\db\Query())
            ->select('username,nickname,head_portrait,real_name,mobile_phone')
            ->from("{$tablePrefix}member AS a")
            ->leftJoin("{$tablePrefix}member_verified AS b",'a.id = b.uid')   
            ->where(['a.id'=>$orders[0]['buyer_uid']])
            ->All();
            
        if($buyer_info){
            $data['buyer']['username'] = $buyer_info[0]['username'];
            $data['buyer']['nickname'] = $buyer_info[0]['nickname'];
            $data['buyer']['head_portrait'] = parent::get_user_avatar_url($buyer_info[0]['head_portrait']); 
            $data['buyer']['real_name'] = $buyer_info[0]['real_name'];
            $data['buyer']['mobile_phone'] = $buyer_info[0]['mobile_phone'];                  
        }else{       
            $this->error_message('_No_Data_Query_');      
        }

        $order_ids = array_column($orders,'id');
        $appeals = OtcAppeal::find()->where(['in','order_id',$order_ids])->select('order_id,status')->asArray()->all();
        if (!empty($appeals)){
            $appeals = array_column($appeals,'status','order_id');
            $appeal_order_ids = array_keys($appeals);
        }else{
            $appeals = [];
            $appeal_order_ids = [];
        }
               
        $data['order']['order_id'] =  $orders[0]['id'];
        $data['order']['side'] =  $orders[0]['seller_uid']== $uid ? 1:2;
        $data['order']['coin_name'] =$orders[0]['coin_name'];
        $data['order']['price_usd'] = $orders[0]['price_usd']; 
        $data['order']['amount'] = $orders[0]['amount'];   
        $data['order']['total_price_usd'] = $orders[0]['total_price_usd']; 
        $data['order']['order_time'] = $orders[0]['order_time'];
        $data['order']['pay_time'] = $orders[0]['pay_time'];       
        $data['order']['deal_time'] = $orders[0]['deal_time'];
        if (in_array($orders[0]['id'],$order_ids) && in_array($orders[0]['id'], $appeal_order_ids)){
                $data['order']['status'] = $appeals[$orders[0]['id']] == 1 ? 11 : 12;
            }else{
                $data['order']['status'] = $orders[0]['status']; 
            }   
        // $data['order']['status'] = $orders[0]['status'];
        
        $data_cards = MemberProceeds::find()->select('proceeds_type,bank_name,account,qrcode,username')->with('proceeds')->where(['member_id' => $orders[0]['seller_uid'], 'is_delete' => 0])->asArray()->all();
        array_walk($data_cards, function ($val,$key) use (&$data_cards){
            $data_cards[$key]['proceeds']['icon'] = parent::get_user_avatar_url($val['proceeds']['icon']);
            if (!empty($data_cards[$key]['qrcode'])){
                $data_cards[$key]['qrcode'] = parent::get_user_avatar_url($val['qrcode']);
            }else{
                $data_cards[$key]['qrcode'] = '';
            }
        });

        $data['seller_cards'] = $data_cards;
        if ($orders) {
            $this->success_message($data,'_Success_');
        }else{
            $this->error_message('_No_Data_Query_');
        }       
    }
    
    //历史订单
    public function actionOrderHistory(){
        $request = Yii::$app->request;

        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);
        $uid = $uinfo['id'];

        // 新增订单状态分类【0：已取消，1：已完成，2未付款，3已付款待确认，11申诉中，12申诉已处理】
        $order_status = $request->post('type');
        $order_status = $order_status == 1 ? 1 : $order_status;// 默认返回已经完成
        
 
        $tablePrefix = Yii::$app->db->tablePrefix;
    
        $orders = (new \yii\db\Query())
            ->select('a.id,a.status,a.seller_uid,a.buyer_uid,a.price_usd,a.amount,a.coin_name,a.amount,a.total_price_usd,order_time,pay_time,deal_time,b.id uid,b.nickname,b.username,b.head_portrait')
            ->from("{$tablePrefix}otc_order  AS a")
            ->leftJoin("{$tablePrefix}member AS b",'a.other_uid = b.id')            
            ->where(['and', "1=1", ['or', "seller_uid={$uid}", "buyer_uid={$uid}"]])
            ->orderBy('a.id desc')                                  
            ->All();
        if (!$orders) {
            $this->error_message('_No_Data_Query_');
        }
        $order_ids = array_column($orders,'id');
        $appeals = OtcAppeal::find()->where(['in','order_id',$order_ids])->select('order_id,status')->asArray()->all();
        if (!empty($appeals)){
            $appeals = array_column($appeals,'status','order_id');
            $appeal_order_ids = array_keys($appeals);
        }else{
            $appeals = [];
            $appeal_order_ids = [];
        }
        foreach($orders as $key =>$val){
            $data[$key]['order_id'] =  $val['id'];
            $data[$key]['side'] =  $val['seller_uid']== $uid ? 1:2;
            $data[$key]['coin_name'] = $val['coin_name'];
            $data[$key]['other_uid'] = $val['uid'];                         
            $data[$key]['other_nickname'] = $val['nickname'];                           
            $data[$key]['other_head_portrait']  =  parent::get_user_avatar_url( $val['head_portrait']);                         
            $data[$key]['price_usd'] = $val['price_usd']; 
            $data[$key]['amount'] = $val['amount'];   
            $data[$key]['total_price_usd'] = $val['total_price_usd']; 
            $data[$key]['order_time'] = $val['order_time'];

            if (in_array($val['id'],$order_ids) && in_array($val['id'], $appeal_order_ids)){
                $data[$key]['status'] = $appeals[$val['id']] == 1 ? 11 : 12;
            }else{
                $data[$key]['status'] = $val['status']; 
            }
            // 筛选数据
            if ($data[$key]['status'] != intval($order_status)) {
                unset($data[$key]);
            }                   
        }
        $this->success_message(array_values($data),'_Success_');
    }   
    
    
    //买家取消订单
    public function actionCancelOrder(){

        $request = Yii::$app->request;

        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);
        $uid = $uinfo['id'];
        
        $order_id = $request->post('order_id');
 
        $tablePrefix = Yii::$app->db->tablePrefix;
    
        $orders = (new \yii\db\Query())
            ->select('id,status')
            ->from("{$tablePrefix}otc_order ")
            ->where(['and', "status>1", ['and', "buyer_uid={$uid}", "id={$order_id}"]])     
            ->All();
                 
        if (!$orders) {
            $this->error_message('_No_Data_Query_');
        }

        if ($orders[0]['status'] >= 3){
            $this->error_message('_Payment_completion_cannot_be_cancelled_');
        }
        
        $update = Yii::$app->db->createCommand()->update("{$tablePrefix}otc_order", 
          array(
            'deal_time' => date("Y-m-d H:i:s",time()), 
            'status' => 0,  
          ),
          "id=".$order_id
        )->execute();       
        $data['order_id'] = $order_id;          
         $data['status'] = 0;                     
        if ($update) {
            $this->success_message($data,'_Success_');
        }else{
            $this->error_message('_No_Data_Query_');
        }       
    }
    //我的发布
    public function actionMyMarket(){
        
        $request = Yii::$app->request;

        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);
        $uid = $uinfo['id'];

        $tablePrefix = Yii::$app->db->tablePrefix;
        $markets = (new \yii\db\Query())
            ->select('a.id,a.side,a.coin_name,a.coin_id,b.icon,a.min_num,a.max_num,a.price_usd,card_enable,alipay_enable,wechat_enable,a.order_count,a.deal_count,a.deal_amount,a.status')
            ->from("{$tablePrefix}otc_market AS a")
            ->leftJoin("{$tablePrefix}coins AS b",'a.coin_id = b.id')
            ->where(['uid'=>$uid])
            ->andWhere(['in', 'status', [1,2]])
            ->orderBy('a.id desc')
            ->All();    
        foreach($markets as &$item){
            $item['max_num'] = $item['max_num'] - $item['deal_amount'];
            if ($item['max_num'] < $item['min_num']){
                $item['max_num'] = $item['min_num'];
            }
            $item['max_num'] = sprintf("%.2f", $item['max_num']);
            if ($item['order_count'] <= 0){
                $item['deal_rate'] = '0';
            }else{
                $item['deal_rate'] = $item['deal_count'] / $item['order_count'] * 100;
            }
            $item['deal_rate'] = sprintf("%.2f",$item['deal_rate']);
            $item['icon'] = parent::get_user_avatar_url($item['icon']);
        }   
        if ($markets) {
            $this->success_message($markets,' ');
        }else{
            $this->error_message('_No_Data_Query_');
        }       
        
    }
    //启用发布  判断余额是否够  删除发布
    public function actionEnableMarket(){
        
        $request = Yii::$app->request;

        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);
        $uid = $uinfo['id'];

        $market_id = intval($request->post('market_id'));
    
        $tablePrefix = Yii::$app->db->tablePrefix;
        $markets = (new \yii\db\Query())
            ->from("{$tablePrefix}otc_market")
            ->where(['status'=>2,'uid'=>$uid,'id'=>$market_id])         
            ->orderBy('id desc')
            ->All();

        if (!$markets) {
            $this->error_message('_No_Data_Query_');
        }
        if($markets[0]['deal_amount'] >= $markets[0]['max_num']){
            $this->error_message('_Information_has_been_lost_');
        }

        //查询该用户已发布的有效广告
        $otc_coin = OtcCoinList::find()->where(['coin_id'=>$markets[0]['coin_id']])->asArray()->one();
        if ($otc_coin['status'] != 1){
            $this->error_message('_The_current_currency_is_temporarily_disabled_');
        }

        $otc_count = (new \yii\db\Query())
            ->from("{$tablePrefix}otc_market ")
            ->where(['uid'=>$uid,'status'=>1,'side'=>$markets[0]['side'],'coin_id'=>$markets[0]['coin_id']])
            ->count();
        if($otc_count >= $otc_coin['max_register_num']){
            $this->error_message('_The_release_limit_has_been_exceeded_');
        }
        if ($markets[0]['side'] == 1){//发布的卖单，检查用户余额
            $_POST['chain_network'] = 'main_network';
            $_POST['return_way'] = 'array';
            $balance_all = Trade::balance_v2($uid);// 成功返回数据，失败返回false
            if ($balance_all){
                $balance_all = array_column($balance_all[0], NULL, 'name');
                if($balance_all[$markets[0]['coin_name']]['available'] < $markets[0]['max_num']){
                    $this->error_message('_Total_user_available_assets_are_insufficient_');
                }
            }
        }

        $update = Yii::$app->db->createCommand()->update("{$tablePrefix}otc_market", 
          array(
            'publish_time' => date("Y-m-d H:i:s",time()), 
            'status' => 1,  
          ),
          "id=".$markets[0]['id']
        )->execute();       
        $data['market_id'] = $markets[0]['id'];     
        $data['status'] = 1;          
        if ($update) {
            $this->success_message($data,'_Success_');
        }else{
            $this->error_message('_No_Data_Query_');
        }                       
    }
    //撤销发布
    public function actionDisableMarket(){
        
        $request = Yii::$app->request;

        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);
        $uid = $uinfo['id'];

        $market_id = intval($request->post('market_id'));
    
        $tablePrefix = Yii::$app->db->tablePrefix;
        $markets = (new \yii\db\Query())
            ->select('id')
            ->from("{$tablePrefix}otc_market")
            ->where(['uid'=>$uid])
            ->where(['status'=>1,'uid'=>$uid,'id'=>$market_id])             
            ->orderBy('id desc')
            ->All();    

        if (!$markets) {
            $this->error_message('_No_Data_Query_');
        }        
        $update = Yii::$app->db->createCommand()->update("{$tablePrefix}otc_market", 
          array(
            'status' => 2,  
          ),
          "id=".$markets[0]['id']
        )->execute();       
        $data['market_id'] = $markets[0]['id'];     
        $data['status'] = 2;          
        if ($update) {
            $this->success_message($data,'_Success_');
        }else{
            $this->error_message('_No_Data_Query_');
        }               
        
    }

    // 订单申诉
    public function actionAppeal(){
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);
        $uid = $uinfo['id'];
        $image = $request->post('image');
        $describe = $request->post('describe');
        $order_id = $request->post('order_id');
        $this->check_empty($image, '图片不能为空!');
        $this->check_empty($describe, '描述不能为空!');
        $this->check_empty($order_id, '订单ID不能为空!');
        $this->check_img($image);
        $res = OtcOrder::find()->where(['id'=>$order_id])->andWhere(['in','status',[1,2,3]])->asArray()->one();
        if(empty($res)){
            $this->error_message('达不到申诉条件!');
        }
        $div = OtcAppeal::find()->where(['uid'=>$uid,'order_id'=>$order_id,'status'=>1])->asArray()->one();
        if(!empty($div)){
            $this->error_message('已提交申诉,请勿重复!');
        }
        if($this->save_info($uid,$image,$describe,$order_id)){
            $this->success_message();
        }else{
            $this->error_message('_Save_Failure_Try_Again_');
        }
    }   

    // 提交申诉
    private function save_info($uid,$url,$describe,$order_id){
        $model = new OtcAppeal();
        $model->uid = $uid;
        $model->image = $url;
        $model->describe = $describe;
        $model->order_id  = $order_id;
        $model->status = 1;
        $model->created_at = date('Y-m-d H:i:s');
        return $model->save();
    }   

    /**
     * 检查图片是否存在
     */
    private function check_img($url){
        $file = Yii::getAlias("@rootPath/web") . $url;
        if(!file_exists($file)){
            $this->error_message('_Picture_Not_Exist_Reupload_');
        }
    }

    /**
     * 上传图片
     */
    public function actionImage(){
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);
        $uid = $uinfo['id'];
        // $this->check_submit($uid);
        // $res = OtcOrder::find()->where(['id'=>$order_id])->asArray()->one();
        // if(empty($res)){
        //     $this->error_message('暂无该订单!');
        // }
        $file = $_FILES['image'];
        $data = $this->upload($file, 'image');
        $this->success_message($data);
    }

    /**
     * @param $file
     * @param $name
     * @return array
     */
    private function upload($file,$name){
        $type = 'imagesUpload';
        $uploadConfig = Yii::$app->params[$type];
        $stateMap = Yii::$app->params['uploadState'];
        $file_size = $file['size'];
        $file_name = $file['name'];
        $file_exc = StringHelper::clipping($file_name);
        if ($file_size > $uploadConfig['maxSize']){
            $message = $stateMap['ERROR_SIZE_EXCEED'];
            $this->error_message($message);
        } else if (!$this->checkType($file_exc, $type)){
            $message = $stateMap['ERROR_TYPE_NOT_ALLOWED'];
            $this->error_message($message);
        } else {
            if (!($path = $this->getPath($type))) {
                $message = '_Folder_Creation_Failed__IsOpen_Attachment_Write_Permission_';
                $this->error_message($message);
            }
            $filePath = $path . $uploadConfig['prefix'] . StringHelper::random(10) . $file_exc;
            $uploadFile = UploadedFile::getInstanceByName($name);
            if ($uploadFile->saveAs(Yii::getAlias("@attachment/") . $filePath)) {
                $data = [
                    'urlPath' => Yii::getAlias("@attachurl/") . $filePath,
                ];
                return $data;
            } else {
                $message = '_File_Move_Error_';
                $this->error_message($message);
            }
        }
    }

    /**
     * 文件类型检测
     *
     * @param $ext
     * @param $type
     * @return bool
     */
    private function checkType($ext, $type)
    {
        if(empty(Yii::$app->params[$type]['maxExc']))
        {
            return true;
        }
        return in_array($ext, Yii::$app->params[$type]['maxExc']);
    }
    
    /**
     * 获取文件路径
     *
     * @param $type
     * @return string
     */
    public function getPath($type)
    {
        // 文件路径
        $file_path = Yii::$app->params[$type]['path'];
        // 子路径
        $sub_name = Yii::$app->params[$type]['subName'];
        $path = $file_path . date($sub_name,time()) . "/";
        $add_path = Yii::getAlias("@attachment/") . $path;
        // 创建路径
        FileHelper::mkdirs($add_path);
        return $path;
    }

    public function actionAppealInfo(){
        $request = Yii::$app->request;
        $language = $request->post('language');
        $language =  $language == 'en_us' ? 'en_us' : 'zh_cn';
        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);
        $uid = $uinfo['id'];
        $id = $request->post('id');
        if (empty($id)) {
            $models = OtcAppeal::find()->select('id,order_id,describe,image,status,created_at')->where(['uid'=>$uid])->orderBy('created_at DESC');
            $data = $this->actionCheckPage($models);
            if(empty($data)){
                $this->error_message('暂无数据');
            }else{
                $status_msg = [
                        Yii::t($language,'已删除'),
                        Yii::t($language,'已提交,正在解决中'),
                        Yii::t($language,'已解决'),
                        Yii::t($language,'已解决'),
                    ];
                foreach($data as $k => $v){
                    $data[$k]['status_msg'] = $status_msg[$data[$k]['status']];
                    $data[$k]['image'] = $this->get_user_avatar_url($data[$k]['image']);

                }
                $this->success_message($data);
            }
        }else{
            $data = OtcAppeal::find()->select('id,order_id,describe,image,status,created_at')->where(['uid'=>$uid,'id'=>$id])->orderBy('created_at DESC')->asArray()->one();
            // $data = $this->actionCheckPage($models);
            if(empty($data)){
                $this->error_message('暂无数据');
            }else{
                $status_msg = [
                        Yii::t($language,'已删除'),
                        Yii::t($language,'已提交,正在解决中'),
                        Yii::t($language,'已解决'),
                        Yii::t($language,'已解决'),
                    ];
                $data['status_msg'] = $status_msg[$data['status']];
                $data['image'] = $this->get_user_avatar_url($data['image']);

                $this->success_message($data);
            }
        }
    }

    // 获取及验证页码参数
    private function actionCheckPage($models){
        $request = Yii::$app->request;
        $count = $models->count();
        if($request->isPost){
            $limit_begin = $request->post('limit_begin');
            $limit_num = $request->post('limit_num');
            $limit_num = empty($limit_num)?intval($count):intval($limit_num);
        }
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' =>$limit_num]);
        $pages->setPage($limit_begin-1);
        $maxPage = $pages->getPageCount();
        $data = $models->offset($limit_begin)->limit($pages->limit)->asArray()->all();
        return $data;
    }

    // 删除发布订单
    public function actionDelMarket()
    {
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);

        $market_id = $request->post('market_id');
        $this->check_empty($market_id, '订单id不能为空!');
        $market_id = intval($market_id);

        $market = OtcMarket::find()
            ->where(['uid' => $uinfo['id']])
            ->andWhere(['id' => $market_id])
            ->andWhere(['status' => 2])// 撤销的才可以删除
            ->one();

        if ($market) {
            // 执行删除【修改status状态为0】
            $market->status = 0;
            if ($market->save()) {
                $this->success_message('', '_Delete_Success_');
            }else{
                $this->error_message('_Delete_Failure_');
            }
        }else{
            // 无该记录
            $this->error_message('_No_Data_Query_');
        }
    }
}
