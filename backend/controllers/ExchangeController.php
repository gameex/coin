<?php
/**
 * name: xiaocai
 * date: 2018-9-7
 */
namespace backend\controllers;

use api\models\ExchangeCoins;
use Denpa\Bitcoin\Omnicore as OmnicoreClient;
use Yii;
use linslin\yii2\curl;
use jinglan\ves\VesRPC;
use api\models\Member;
use api\models\MemberVerified;
use common\models\WithdrawApply;
use common\models\Coins;
use yii\data\Pagination;
use api\models\Coin;
use api\models\MemberWallet;
use common\jinglan\Trade;
use jinglan\walletapi\WalletRPC;
use api\models\Transaction;
use api\models\BalanceLog;
use common\models\TransactionBtc;
use Denpa\Bitcoin\Client as BitcoinClient;
use common\jinglan\Bank;
use common\models\SysAddr;
use common\jinglan\Jinglan;

class ExchangeController extends MController
{
    // 银行转出最低额度
    protected $limit_trun_out = 0.0001;

    // 提现状态设置
    protected $withdraw_status = [
        1 => '<span style="color:#FFC157" title="待审核"><i class="fa fa-spinner fa-pulse fa-fw"></i>待审核</span>',
        2 => '<span style="color:#28A745" title="通过"><i class="fa fa-check fa-fw"></i>已通过</span>',
        3 => '<span style="color:#DC3545" title="拒绝"><i class="fa fa-close fa-fw"></i>已拒绝</span>',
        4 => '<span style="color:#DC3545" title="提现失败"><i class="fa fa-warning fa-fw"></i>提现失败</span>',
        5 => '<span style="color:#DC3545" title="链上待确认"><i class="fa fa-spinner fa-fw"></i>链上待确认</span>',
        6 => '<span style="color:#DC3545" title="操作处理中"><i class="fa fa-spinner fa-fw"></i>操作处理中</span>',
    ];
    // 成交记录
    public function actionMarket()
    {
        // 获取筛选条件参数
        $request = Yii::$app->request;
        $market = $request->get('market','');
        if (empty($market)){
            $market = ExchangeCoins::getMarketName();
            $market = $market[0];
        }
        $last_id = $request->get('last_id') ?: 1;
        $limit   = $request->get('limit') ?: 10;
        // 返回参数
        $code = 0;
        $msg  = null;
        $data = null;

        $rpc = new VesRPC();
        $map1 = 'market.deals';
        $map2 = [$market, (int)$limit, (int)$last_id];
        $rpc_ret = $rpc->do_rpc($map1, $map2);
        if ($rpc_ret['code'] == 0) {
            $msg  = $rpc_ret['data'];
        }else{
            $code = 1;
            $data = $rpc_ret['data'];
        }

        $result = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
            'market' => $market,
            'limit' => $limit,
        ];
        return $this->render('market',$result);
    }

    // 订单统计
    public function actionOrderBook()
    {
        // 获取筛选条件参数
        $request = Yii::$app->request;
        $market = $request->get('market','');
        if (empty($market)){
            $market = ExchangeCoins::getMarketName();
            $market = $market[0];
        }
        $side    = $request->get('side') ?: 1;
        $offset  = $request->get('offset') ?: 0;
        $limit   = $request->get('limit') ?: 20;
        // 返回参数
        $code = 0;
        $msg  = null;
        $data = null;
        $pages = null;
        if (isset($_GET['page'])){
            $offset = ($_GET['page'] - 1) * $limit;
        }

        $rpc = new VesRPC();
        $map1 = 'order.book';
        $map2 = [$market, (int)$side, (int)$offset, (int)$limit];
        $rpc_ret = $rpc->do_rpc($map1, $map2);
        if ($rpc_ret['code'] == 0) {
            $msg  = $rpc_ret['data'];
            $pages  = new Pagination(['totalCount' =>0, 'pageSize' =>$limit]);
        } else {
            $code = 1;
            $data = $rpc_ret['data'];

            $pages  = new Pagination(['totalCount' =>$data['total'], 'pageSize' =>$limit]);
        }
 
        if (!empty($data['orders'])) {
            // 获取用户信息
            $members = Member::find()->select(['id', 'nickname'])->asArray()->all();
            foreach ($members as $key => $value) {
                $members[$value['id']] = $value['nickname'];
                unset($members[$key]);
            }
            foreach ($data['orders'] as $key => $value) {
                $data['orders'][$key]['user'] = $value['user'].' / '.$members[$value['user']];
            }
        }else{

        }

        $result = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
            'pagination' => $pages,
            'market' => $market,
            'side' => $side,
            'offset' => $offset,
            'limit' => $limit,
        ];
        return $this->render('order-book',$result);
    }

    public function actionFindname($uid)
    {
        $member_verified = MemberVerified::find()->select(['real_name'])->where(['uid'=>$uid])->andWhere(['=','status',2])->asArray()->one();
        if (empty($member_verified["real_name"])) {
            $member_verified["real_name"] = '未实名';
        }
        return $member_verified["real_name"];
    }


    // 提现申请列表
    public function actionWithdraw()
    {
        // 筛选条件
        $request  = Yii::$app->request;
        $key_type = $request->get('key_type');
        // $keyword  = $request->get('keyword');
        $starttime  = $request->get('starttime')?: '';
        $endtime  = $request->get('endtime')?: '';
        $starttime  = strtotime($starttime);
        $endtime  = strtotime($endtime);

        $query      = WithdrawApply::find()->select(['jl_withdraw_apply.*', 'jl_member.nickname']);

        $where[]='and';
        if (!empty($starttime)) {
            $where[]=array('>','jl_withdraw_apply.created_at',$starttime);
        }
        if (!empty($endtime)) {
            $where[]=array('<','jl_withdraw_apply.created_at',$endtime);
        }
        if (!empty($key_type)) {
            $where[]=array('=','jl_withdraw_apply.coin_symbol',$key_type);
        }
        if (!empty($id)) {
            $rows = (new \yii\db\Query())
                ->select(['id'])
                ->from('jl_member')
                ->where(['last_member' => $id])
                ->all();
            $rows = array_column($rows, 'id');
            $where[]=array('in','jl_withdraw_apply.member_id',$rows);
        }

        
        $query->where($where);
        $count= $query->count();

        $pagination = new Pagination(['totalCount' => $count]);
        $pagination->setPageSize(15);
        $withdraw_apply = $query->leftJoin('jl_member', 'jl_member.id = jl_withdraw_apply.member_id')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy('created_at DESC')
            ->asArray()
            ->all();
        foreach($withdraw_apply as $key => &$val){
                $val['truename'] = $this->actionFindname($withdraw_apply[$key]["member_id"]);
        }
        $all_num = WithdrawApply::find()->where($where)->select(['sum(jl_withdraw_apply.value_dec) as all_num'])
            ->leftJoin('jl_member', 'jl_withdraw_apply.member_id = jl_member.id')
            ->asArray()
            ->one();
        if (empty($all_num)) {
            $all_num["all_num"] = 0;
        }

        //币种列表
        $where2['enable']=1;
        $symbol = Coins::find()->select(['id','symbol'])->where($where2)->asArray()->all();
        $symbol_list = array();
        foreach ($symbol as $value) {
           $symbol_list[$value['id']] = $value['symbol'];
        }


        $result = [
            'apply'           => $withdraw_apply,
            'withdraw_status' => $this->withdraw_status,
            'symbol_list' => $symbol_list,
            'all_num' => $all_num["all_num"],
            'pagination'      => $pagination,
        ];
        return $this->render('withdraw',$result);
    }
    
    // 删除申请单
    public function actionDelApply()
    {
        $request = Yii::$app->request;
        $id      = $request->get('id');
        if (empty($id)) {
            return $this->message("参数传递错误！",$this->redirect(['withdraw']),'error');
        }

        // 查询申请单信息
        $withdraw_apply = WithdrawApply::findOne(intval($id));
        if (!$withdraw_apply) {
            return $this->message("查询不到该申请单信息！",$this->redirect(['withdraw']),'error');
        }

        if ($withdraw_apply->delete()) {
            return $this->message("删除申请单成功！",$this->redirect(['withdraw']),'success');
        }else{
            return $this->message("删除申请单失败！",$this->redirect(['withdraw']),'error');
        }
    }

    // 提现审核【通过】
    public function actionApplyYes()
    {
        $request = Yii::$app->request;
        $id      = $request->get('id');
        if (empty($id)) {
            return $this->message("参数传递错误！",$this->redirect(['withdraw']),'error');
        }

        // 查询申请单信息
        $withdraw_apply = WithdrawApply::findOne(intval($id));
        if (!$withdraw_apply) {
            return $this->message("查询不到该申请单信息！",$this->redirect(['withdraw']),'error');
        }
        if($withdraw_apply->status != 1){
            return $this->message("处理中，请勿重复提交",$this->redirect(['withdraw']),'error');
        }
        if($withdraw_apply->type == 1){
            $this->turnOut_type1($withdraw_apply);
        }else{
            // 一、给用户提现
            //$result = $this->turnOut($withdraw_apply);
            //12-05 15:30 CC 先修改处理中状态，防止重复提交
            $withdraw_apply->status        = 6; //操作处理中
            $withdraw_apply->save();
            $result = $this->turnOutCC_v2($withdraw_apply);
            if ($result['code'] == 0) {//操作过程失败，重置为待审核
                $withdraw_apply->status        = 1;
//                $withdraw_apply->error_message = $result['msg'];
//                $withdraw_apply->updated_at    = time();
                $withdraw_apply->save();
                return $this->message($result['msg'],$this->redirect(['withdraw']),'error');
            }
            // 二、修改申请单状态
            if ($result['data']['tx_hash'] == 'withdraw_to_internal_member'){
                $update_apply_status = 2;
            }else{
                $update_apply_status = 5;
            }
            $withdraw_apply->pay_user_type = $result['data']['pay_user_type'];// 支付人id
            $withdraw_apply->pay_from    = $result['data']['pay_addr'];// 支付人地址
            $withdraw_apply->status      = $update_apply_status;
            $withdraw_apply->tx_hash = $result['data']['tx_hash'];
            $withdraw_apply->updated_at  = time();
            if ($withdraw_apply->save()) {
                return $this->message("提现成功！",$this->redirect(['withdraw']),'success');
            }else{
                return $this->message("修改申请单状态失败！",$this->redirect(['withdraw']),'error');
            }
        }
    }

    // 提现审核【拒绝】
    public function actionApplyNo()
    {
        // 获取申请单信息
        $request = Yii::$app->request;
        $id      = $request->get('id');


        // 修改审核状态
        $withdraw_apply = WithdrawApply::findOne(intval($id));
        if (!$withdraw_apply) {
            return $this->message("查询不到该申请单信息！",$this->redirect(['withdraw']),'error');
        }
        $withdraw_apply->status        = 3;
        $withdraw_apply->error_message = '申请未通过，拒绝本次提现！';
        $withdraw_apply->updated_at    = time();
        if ($withdraw_apply->save()) {
            return $this->message("拒绝提现申请成功！",$this->redirect(['withdraw']),'success');
        }else{
            return $this->message("拒绝过程失败！",$this->redirect(['withdraw']),'error');
        }
    }


    public function turnOut_type1($withdraw_apply){
        $uid = $withdraw_apply->member_id;
        $coin_name = $withdraw_apply->coin_symbol;
        $value_dec = $withdraw_apply->value_dec;
        $fee = $withdraw_apply->withdraw_fee;
        //1.先查余额
        $_POST['chain_network'] = 'main_network';
        $_POST['return_way'] = 'array';
        $network_type = 0;// 主网
        $balance_all = Trade::balance_v2($uid);// 成功返回数据，失败返回false
        $transaction = Yii::$app->db->beginTransaction();
        if ($balance_all){
            $balance_all = array_column($balance_all[0], NULL, 'name');
            $balance = $balance_all[$coin_name];
            $addr = $balance['addr'];
            if(empty($addr)){
                return $this->message("用户该币种类型的银行账户未生成！",$this->redirect(['withdraw']),'error');
            }
            if($balance['available'] + $balance['withdraw_freeze'] < $value_dec){//卖家余额不够，出现此情况就是之前处理有问题，继续执行，扣成负值

            }
            //检测余额
            if ((float)$balance['exchange_available'] > 0){
                $transaction2 = Yii::$app->db->beginTransaction();
                try{
                    $lack = $value_dec - $balance['bank_balance'];
                    $bank_balance = Bank::getBalance($uid,$balance['name']);

                    $balance_model = new BalanceLog();
                    $balance_model->type = 1;//1:充值，10:取出
                    $balance_model->member_id = $uid;
                    $balance_model->coin_symbol = $balance['name'];
                    $balance_model->addr = $balance['addr'];
                    $balance_model->change      = (float)$balance['exchange_available_rel'];
                    $balance_model->balance     = (float)$balance['exchange_available_rel'] + $bank_balance;
                    $balance_model->fee = 0.0;
                    $balance_model->detial_type = 'exchange';
                    $balance_model->network = $network_type;

                    if(!$balance_model->save(false)){
                        $transaction->rollBack();
                        return $this->message("_Try_Again_Later_",$this->redirect(['withdraw']),'error');
                    }
                    //更新交易所余额
                    $rpc = new VesRPC();
                    $rpc_ret = $rpc->do_rpc('balance.update', [intval($uid),$balance['name'],"trade",$balance_model->attributes['id'],strval(-(float)$balance['exchange_available_rel']),['id'=>$balance_model->attributes['id']]]);
                    if ($rpc_ret['code'] == 0) {
                        $transaction2->rollBack();
                        return $this->message($rpc_ret['data'],$this->redirect(['withdraw']),'error');
                    } else {//更新成功
                        $transaction2->commit();
                    }
                }catch (\Exception $e){
                    $transaction2->rollBack();
                    return $this->message($e->getMessage(),$this->redirect(['withdraw']),'error');
                }
            }
            //再查余额
            $bank_balance = Bank::getBalance($uid,$coin_name);
            //卖家扣币
            $balance_model = new BalanceLog();
            $balance_model->type = 10;//1:充值，10:取出
            $balance_model->member_id = $uid;
            $balance_model->coin_symbol = $coin_name;
            $balance_model->addr = $addr;
            $balance_model->change = -$value_dec;
            $balance_model->balance = $bank_balance - $value_dec - (float)$fee;
            $balance_model->fee = -$fee;
            $balance_model->detial_type = 'withdraw';
            $balance_model->network = $network_type;

            if(!$balance_model->save(false)){
                $transaction->rollBack();
                return $this->message('_Buckling_failure_',$this->redirect(['withdraw']),'error');
            }
        }else{
            $transaction->rollBack();
            return $this->message('_Failed_To_Get_Bank_Account_Balance_',$this->redirect(['withdraw']),'error');
        }
        $transaction->commit();
        $withdraw_apply->status      = 2;
        $withdraw_apply->updated_at  = time();
        if ($withdraw_apply->save()) {//p('ssss');
            return $this->message("提现成功！",$this->redirect(['withdraw']),'success');
        }else{
            return $this->message("修改申请单状态失败！",$this->redirect(['withdraw']),'error');
        }
    }

    protected function turnOutCC_v2($data){
        $coin_symbol  = $data->coin_symbol;
        $coins = Coin::find()->where(['symbol' => $coin_symbol])->andWhere(['enable' => 1])->one();
        if (!$coins) {
            return ['code'=>0, 'msg'=>'暂不支持该币种类型转出服务！'];
        }
        $uinfo['id']  = $data->member_id;
        // 转账前获取用户资产信息
        $_POST['return_way'] = 'array';
        $_POST['chain_network'] = 'main_network';
        $balance_all = Trade::balance_v2($uinfo['id']);// 成功返回数据，失败返回false
        if (!$balance_all) {
            return ['code'=>0, 'msg'=>'获取用户总资产信息时发生错误！'];
        }
        $balance_all = array_column($balance_all[0], NULL, 'name');
        $user_balance = $balance_all[$coin_symbol];
        if (empty($user_balance)) {
            return ['code'=>0, 'msg'=>'货币类型有误！'];
        }
        if (empty($user_balance['addr'])) {
            return ['code'=>0, 'msg'=>'用户该币种类型的银行账户未生成！'];
        }
        $coin_is_token = $coins->ram_status==1 ? true : false;

        $wallet_addr  = $data->addr;
        $value_dec    = $data->value_dec;
        $network      = $data->chain_network;
        $current      = (float)$data->current;
        $withdraw_fee = (float)$data->withdraw_fee;

        $gas = 0;
        $gas_price    = 0;
        // 矿工费和手续费
        $gas_16       = '0x';
        $gas_price_16 = '0x';

        if ((doubleval($user_balance['available'])+doubleval($user_balance['withdraw_freeze'])) < (doubleval($value_dec) + $withdraw_fee + doubleval($current))) {
            return ['code'=>0, 'msg'=>'用户总可用资产不足！'];
        }

        //直接提出交易所余额到银行
        if ((float)$user_balance['exchange_available'] > 0){
            $transaction_work = Yii::$app->db->beginTransaction();
            try {
                $bank_balance = Bank::getBalance($uinfo['id'],$coin_symbol);
                $balance_log              = new BalanceLog();
                $balance_log->type        = 1;//1:充值，10:取出
                $balance_log->member_id   = (int)$uinfo['id'];
                $balance_log->coin_symbol = $coin_symbol;
                $balance_log->addr        = $user_balance['addr'];
                $balance_log->change      = (float)$user_balance['exchange_available_rel'];
                $balance_log->balance     = (float)$user_balance['exchange_available_rel'] + $bank_balance;
                $balance_log->fee         = 0.0;
                $balance_log->detial_type = 'exchange';
                $balance_log->network     = $network;
                if(!$balance_log->save(false)){
                    return ['code'=>0, 'msg'=>'Save Error#balance_log'];
                }

                //更新交易所余额
                $rpc = new VesRPC();
                $rpc_ret = $rpc->do_rpc('balance.update', [intval($uinfo['id']),$coin_symbol,"trade",$balance_log->attributes['id'],strval(-(float)$user_balance['exchange_available_rel']),['id'=>$balance_log->attributes['id']]]);
                if ($rpc_ret['code'] == 0) {
                    $transaction_work->rollBack();
                    return ['code'=>0, 'msg'=>$rpc_ret['data']];
                } else {//更新成功
                    $transaction_work->commit();
                }
            } catch(\Exception $e) {
                $transaction_work->rollBack();
                return ['code'=>0, 'msg'=>$e->getMessage()];
            } catch(\Throwable $e) {
                $transaction_work->rollBack();
                return ['code'=>0, 'msg'=>$e->getMessage()];
            }
        }
        //12-05 12:07 CC 加入判断申请提现地址是否是银行内部，即内部互转，若是直接互转，扣手续费，不走链
        $find_user_bank_addr = MemberWallet::find()->where(['addr'=>$wallet_addr,'coin_symbol'=>"_{$coin_symbol}_",'status'=>1,'network'=>$network])->orderBy('id desc')->asArray()->one();
        if (!empty($find_user_bank_addr) && $find_user_bank_addr['coin_symbol'] == "_{$coin_symbol}_"){//找到内部账号
            $find_user_bank_uid = $find_user_bank_addr['uid'];
            $find_user_bank_addr = $find_user_bank_addr['addr'];
            $transaction0 = Yii::$app->db->beginTransaction();
          
            $pay_user_type = 1;
            //再查余额
            $bank_balance = Bank::getBalance($uinfo['id'],$coin_symbol);
            //扣币
            $balance_model = new BalanceLog();
            $balance_model->type = 10;//1:充值，10:取出
            $balance_model->member_id = $uinfo['id'];
            $balance_model->coin_symbol = $coin_symbol;
            $balance_model->addr = $user_balance['addr'];
            $balance_model->change = -(double)$value_dec;
            $balance_model->balance = $bank_balance - (double)$value_dec;
            $balance_model->fee = 0.0;
            $balance_model->detial_type = 'withdraw';
            $balance_model->network = $network;

            if(!$balance_model->save(false)){
                $transaction0->rollBack();
                return ['code'=>0, 'msg'=>'扣款失败，请重试'];
            }
            //扣手续费
            //再查余额
            $bank_balance = Bank::getBalance($uinfo['id'],$coin_symbol);
            $balance_model2 = new BalanceLog();
            $balance_model2->type = 10;//1:充值，10:取出
            $balance_model2->member_id = $uinfo['id'];
            $balance_model2->coin_symbol = $coin_symbol;
            $balance_model2->addr = $user_balance['addr'];
            $balance_model2->change = 0-(double)$withdraw_fee-(double)$current;
            $balance_model2->balance = $bank_balance - (double)$withdraw_fee-(double)$current;
            $balance_model2->fee = 0.0;
            $balance_model2->detial_type = 'withdraw_fee';
            $balance_model2->network = $network;

            if(!$balance_model2->save(false)){
                $transaction0->rollBack();
                return ['code'=>0, 'msg'=>'扣款失败，请重试#2'];
            }

            //加币
            $bank_balance2 = Bank::getBalance($find_user_bank_uid,$coin_symbol);
            $balance_model3 = new BalanceLog();
            $balance_model3->type = 1;//1:充值，10:取出
            $balance_model3->member_id = $find_user_bank_uid;
            $balance_model3->coin_symbol = $coin_symbol;
            $balance_model3->addr = $find_user_bank_addr;
            $balance_model3->change = (double)$value_dec;
            $balance_model3->balance = $bank_balance2 + (double)$value_dec;
            $balance_model3->fee = 0.0;
            $balance_model3->detial_type = 'chain';
            $balance_model3->network = $network;

            if(!$balance_model3->save(false)){
                $transaction0->rollBack();
                return ['code'=>0, 'msg'=>'加币失败，请重试'];
            }
            $transaction0->commit();
            //扣币 扣手续费 加币成功end
            return ['code'=>1, 'data'=>['pay_addr'=>'', 'pay_user_type'=>$pay_user_type, 'tx_hash'=>'withdraw_to_internal_member']];
        }
        //WalletRPC--Begin

        $transaction0 = Yii::$app->db->beginTransaction();
        //$_POST['chain_network'] = 'testnet';//测试网

        //找到自己的钱包信息
        $user_addr = MemberWallet::find()->where(['uid'=> $uinfo['id'],'coin_symbol'=>"_{$coin_symbol}_",'status'=>1,'network'=>$network])
                            ->orderBy('id desc')
                            ->one();

        $pay_addr = $user_addr->addr;
        $pay_wallet_id = $user_addr->seed;

      
        $pay_user_type = 1;

        //再查余额
        $bank_balance = Bank::getBalance($uinfo['id'],$coin_symbol);
        //扣币
        $balance_model = new BalanceLog();
        $balance_model->type = 10;//1:充值，10:取出
        $balance_model->member_id = $uinfo['id'];
        $balance_model->coin_symbol = $coin_symbol;
        $balance_model->addr = $user_balance['addr'];
        $balance_model->change = -(double)$value_dec;
        $balance_model->balance = $bank_balance - (double)$value_dec;
        $balance_model->fee = 0.0;
        $balance_model->detial_type = 'withdraw';
        $balance_model->network = $network;

        if(!$balance_model->save(false)){
            $transaction0->rollBack();
            return ['code'=>0, 'msg'=>'扣款失败，请重试'];
        }
        //扣手续费
        //再查余额
        $bank_balance = Bank::getBalance($uinfo['id'],$coin_symbol);
        $balance_model2 = new BalanceLog();
        $balance_model2->type = 10;//1:充值，10:取出
        $balance_model2->member_id = $uinfo['id'];
        $balance_model2->coin_symbol = $coin_symbol;
        $balance_model2->addr = $user_balance['addr'];
        $balance_model2->change = 0-(double)$withdraw_fee;
        $balance_model2->balance = $bank_balance - (double)$withdraw_fee;
        $balance_model2->fee = 0.0;
        $balance_model2->detial_type = 'withdraw_fee';
        $balance_model2->network = $network;

        if(!$balance_model2->save(false)){
            $transaction0->rollBack();
            return ['code'=>0, 'msg'=>'扣款失败，请重试#2'];
        }

        //$value_hex = (string)'0x'.$rpc->bc_dechex($value_dec * pow(10, 18));
        $value_hex = '0x';
        // 数据库存储记录
        $transaction = new Transaction;
        $transaction->type          = 3;// 1:钱包转账交易 2:存入银行 3:取出银行 4:场外交易
        $transaction->member_id     = $uinfo['id'];
        $transaction->coin_symbol   = $coin_symbol;
        $transaction->from          = $pay_addr;
        $transaction->to            = $wallet_addr;
        $transaction->value_hex     = $value_hex;
        $transaction->value_dec     = (string)$value_dec;
        $transaction->gas_hex       = (string)$gas_16;
        $transaction->gas_dec       = (string)$gas;
        $transaction->gas_price_hex = (string)$gas_price_16;
        $transaction->gas_price_dec = (string)$gas_price;
        $transaction->nonce_hex     = '0x0';
        $transaction->nonce_dec     = '0';
        $transaction->tx_status     = 'prepare';
        $transaction->network       = $network;
        if (!$transaction->save(false)) {
            return ['code'=>0, 'msg'=>'Save Error#transaction'];
        }
        $transaction_no = $transaction->attributes['id'];
        // 三、开始进行转出操作
        $proto = Yii::$app->config->info('WALLET_API_PROTOCAL');;
        $host = Yii::$app->config->info('WALLET_API_URL');;
        $port = Yii::$app->config->info('WALLET_API_PORT');;
        $_md5_key = Yii::$app->config->info('WALLET_API_KEY');;       
        $rpc = new WalletRPC($proto,$host,$port,$_md5_key );
        $rpc_ret = $rpc->account_transfer($pay_wallet_id,$pay_addr,$value_dec,$wallet_addr,$transaction_no);
        if ($rpc_ret['code'] == 0) {
            $transaction0->rollBack();
            return ['code'=>0, 'msg'=>'交易过程失败：'.$rpc_ret['data']];
        }else{
            $tx_hash = $rpc_ret['data']['tx_id'];
            
            $find_transaction = Transaction::find()->where(['id'=> $transaction_no])->one();
         
            if($find_transaction){
                $find_transaction->tx_hash = $tx_hash;
                $find_transaction->tx_status = 'pending';
                $find_transaction->save(false);
            }  
           $find_transaction = Transaction::find()->where(['id'=> $transaction_no])->one();
           $transaction0->commit();
            
        }
        return ['code'=>1, 'data'=>['pay_addr'=>$pay_addr, 'pay_user_type'=>$pay_user_type, 'tx_hash'=>$tx_hash]];
        //WalletRPC--End

    }


}