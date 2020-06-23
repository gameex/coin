<?php 
/**
* name: xiaocai
* date: 2018-08-30 10:10:00
*/
namespace common\jinglan;

use Yii;
use api\models\Coin;
use api\models\MemberWallet;
use api\models\Transaction;
use jinglan\bitcoin\Unspent;
use jinglan\bitcoin\Balance;
use common\models\TransactionBtc;
use Denpa\Bitcoin\Client as BitcoinClient;
use Denpa\Bitcoin\Omnicore as OmnicoreClient;
use yii\data\Pagination;
use common\jinglan\Trade;
use api\models\BalanceLog;
use linslin\yii2\curl;
use jinglan\ves\VesRPC;
use common\models\WithdrawApply;
use common\models\WithdrawAddress;
use common\jinglan\Jinglan;
use api\models\Member;
use api\models\EmailCode;
use api\models\Varcode;

class WithdrawCash extends Jinglan
{
    // 银行转出最低额度
    protected static $limit_trun_out = 0.0001;

    // 提现【从用户银行账户转到指定钱包地址】
    public static function turnOut($uid = 0)
    {
        $request = Yii::$app->request;
        if (empty($uid) || $uid == 0) {
            Jinglan::error_message('_Please_Check_If_The_User_ID_Is_Correct_');
        }
        $uinfo['id'] = $uid;

        $chain_network = empty($_POST['chain_network']) ? 'testnet' : $_POST['chain_network'];
        if ($chain_network == 'main_network') {
            $network_type = 0;// 主网
        }else{
            $network_type = 1;// 测试网
        }

        // 获取参数【token、kk钱包地址、转出金额、币种类型】
        $coin_symbol = $request->post('coin_symbol');
        $wallet_addr = $request->post('wallet_addr');
        $value_dec   = $request->post('value');
        $current     = $request->post('current');
        $description = $request->post('memo');
        
        $useremail = Member::find()->select('email')->where("id = ".$uinfo['id'])->asArray()->one();
        $code = $request->post('code');
        $mobile_phone = $useremail['mobile_phone'];
        $email = $useremail['email'];
         //var_dump($description);var_dump($code);var_dump($email);die();
        $varcode_result = EmailCode::find()->where( ['email'=>$email, 'varcode'=>$code , 'type'=>3] )->one();

        //$varcode_result = Varcode::find()->where(['mobile_phone' => $mobile_phone, 'varcode'=>$code])->one();
        if (empty($varcode_result)){
            Jinglan::error_message('验证码错误');
        }

        Jinglan::check_empty($coin_symbol, '_MoneyType_Not_Empty_');
        Jinglan::check_empty($wallet_addr, '_Wallet_Address_Not_Empty_');
        Jinglan::check_empty($value_dec, '_The_Amount_Of_Transfer_Can_Not_Be_Empty_');
        //Jinglan::check_empty($current, '_Miners_fees_can_not_be_empty_');

        //实名认证【verified_status】
        $member = Member::findOne(intval($uinfo['id']));
        if ($member->verified_status != 1) {
           Jinglan::error_message('_Users_Are_Not_Authenticated_By_Real_Names_');
        }

        // 判断申请参数的合法性
        // 1：币种合法性【不支持的币种不接受提现】
        $coins = Coin::find()->where(['symbol' => $coin_symbol])->andWhere(['enable' => 1])->one();
        if (!$coins) {
            Jinglan::error_message('_Temporarily_Do_Not_Support_The_Currency_Type_Out_Of_Service_');
        }

        if ($value_dec < $coins->limit_amount){
            Jinglan::error_message('_The_Amount_Of_Transfer_Should_Not_Be_Lower_Than_The_Minimum_Turnover_');
        }

        // 存储本次提现手续费
        $withdraw_fee = $coins->withdraw_fee;

        // 判断币种是否为代币
        $coin_is_token = $coins->ram_status==1 ? true : false;
        if ($coin_is_token) {
            $coin_token_name = $coin_symbol;
            $coin_symbol     = 'ETH';
        }

        // 2：地址合法性
        switch ($coin_symbol) {
            case 'ETH':
                // 判断长度是否为40位【去除0x】
                if (strlen(substr($wallet_addr, 2)) != 40) {
                    Jinglan::error_message('_Address_is_illegal_');
                }
                break;
            default:
                // Jinglan::error_message('地址合法性未确认！');
                break;
        }

        // 3：金额合法性
        if (!is_numeric($value_dec)) {
            Jinglan::error_message('_The_amount_of_withdrawals_is_not_legal_');
        }

        // // 4：矿工费用合法性
        // if (!is_numeric($current)) {
        //     Jinglan::error_message('_The_absenteeism_cost_parameter_is_not_legal_');
        // }

        // $current   = (string)$current;
        $current   = 0;
        $value_dec = floatval($value_dec);

        // 重置代币名称
        if ($coin_is_token) {
            $coin_symbol = $coin_token_name;
        }

        // 获取用户资产
        $_POST['return_way'] = 'array';
        $balance_all = Trade::balance_v2($uinfo['id']);// 成功返回数据，失败返回false
        if (!$balance_all) {
            Jinglan::error_message('_The_application_process_failed_unexpectedly_Please_try_again_later_');
        }
        foreach ($balance_all[0] as $key => $value) {
            if ($value['name'] == $coin_symbol) {
                if(empty($value['addr'])){
                    Jinglan::error_message('_The_Bank_Account_Of_The_Currency_Type_Is_Not_Generated_');
                }
                if ($value['available'] < ($value_dec+floatval($current)+$coins->withdraw_fee)) {
                    Jinglan::error_message('Your_bank_account_is_insufficient_please_change_the_amount_transferred');
                }
                break;
            }
        }

        // 存储提现申请表
        $withdraw_apply = new WithdrawApply();
        $withdraw_apply->member_id     = intval($uinfo['id']);
        $withdraw_apply->coin_symbol   = $coin_symbol;
        $withdraw_apply->addr          = $wallet_addr;
        $withdraw_apply->value_dec     = $value_dec;
        $withdraw_apply->current       = $current;
        $withdraw_apply->withdraw_fee  = $withdraw_fee;
        $withdraw_apply->description   = $description;
        $withdraw_apply->status        = 1;//【1：待审核、2：审核通过、3：未通过】
        $withdraw_apply->created_at    = time();
        $withdraw_apply->chain_network = $network_type;
        if (!$withdraw_apply->save()) {
            Jinglan::error_message('_Withdrawals_failed_');
        }
        Jinglan::success_message('','_Successful_application_for_withdrawals_');
    }

    // 银行交易明细
    public static function rechargeDetails($uid)
    {
        $request = Yii::$app->request;
        if (empty($uid) || $uid == 0) {
            Jinglan::error_message('_Please_Check_If_The_User_ID_Is_Correct_');
        }
        $uinfo['id'] = (int)$uid;

        // 获取分页参数
        $page      = $request->post('page') ? $request->post('page') : 1;// 获取页面【默认第1页】
        $page      = (int)$page<=1 ? 1 : (int)$page;
        $page_size = $request->post('page_size') ? $request->post('page_size') : 20;// 每页数据量【默认20条数据】
        $page_size = $page_size<=0 ? 20 : (int)$page_size;

        // 获取查询参数
        $coin_symbol      = $request->post('coin_symbol') ? $request->post('coin_symbol') : 'all';// 货币类型【默认all】
        $transaction_type = $request->post('transaction_type') ? $request->post('transaction_type') : 'all';// 交易类型【默认all】
        $begin_time       = $request->post('begin_time') ? $request->post('begin_time') : '0';// 时间【默认为0】
        $end_time         = $request->post('end_time') ? $request->post('end_time') : time();// 时间【默认为0】

        $query_eth = Transaction::find()
            ->select(['type', 'coin_symbol', 'created_at', 'value_dec', 'tx_status'])
            ->where(['member_id' => $uinfo['id']]);
        $query_btc = TransactionBtc::find()
            ->select(['type', 'coin_symbol', 'created_at', 'value_dec', 'tx_status'])
            ->where(['member_id' => $uinfo['id']]);

        // 汇总查询条件语句
        if ($coin_symbol != 'all') {
            $query_eth = $query_eth->andWhere(['coin_symbol' => $coin_symbol]);
            $query_btc = $query_btc->andWhere(['coin_symbol' => $coin_symbol]);
        }
        if ($transaction_type != 'all') {
            $query_eth = $query_eth->andWhere(['type' => (int)$transaction_type]);
            $query_btc = $query_btc->andWhere(['type' => (int)$transaction_type]);
        }else{
            $query_eth = $query_eth->andWhere(['in', 'type', [2, 3]]);
            $query_btc = $query_btc->andWhere(['in', 'type', [2, 3]]);
        }
        $query_eth = $query_eth->andWhere(['between', 'created_at', $begin_time, $end_time]);
        $query_btc = $query_btc->andWhere(['between', 'created_at', $begin_time, $end_time]);

        // 合并查询数据
        $data_eth = $query_eth->asArray()->orderBy('created_at DESC')->all();
        $data_btc = $query_btc->asArray()->orderBy('created_at DESC')->all();
        $data = array_merge($data_eth, $data_btc);

        // 获取币种图标地址
        $coins = Coin::find()->select(['symbol', 'icon'])->asArray()->all();
        foreach ($coins as $key => $value) {
            $coins_img[$value['symbol']] = $value['icon'];
        }

        $count_eth = $query_eth->count();
        $count_btc = $query_btc->count();
        $count     = (int)$count_eth + (int)$count_btc;// 总条数
        $page_count = ceil($count/$page_size);// 总页码数
        $offset = ($page-1) * $page_size;
        $data = array_slice($data, $offset, $page_size);
        // 为数据追加币种图片地址
        foreach ($data as $key => $value) {
            $data[$key]['img_addr'] = $coins_img[$value['coin_symbol']];
        }

        // 整合返回数据
        $result = [];
        $result['page_now']  = $page;
        $result['page_count'] = $page_count;
        $result['total'] = $count;
        $result['data'] = $data;

        Jinglan::success_message($result);
    }

    // 获取币种列表
    public static function coinList($uid=0)
    {
        $request = Yii::$app->request;
        if (empty($uid) || $uid == 0) {
            Jinglan::error_message('_Please_Check_If_The_User_ID_Is_Correct_');
        }
        $uinfo['id'] = (int)$uid;

        // 获取搜索条件
        $page_size = $request->post('page_size') ? $request->post('page_size') : 20;
        if ($page_size < 0) {
            $page_size = 20;
        }
        $page = $request->post('page') ? $request->post('page') : 0;
        if ($page < 0) {
            $page = 0;
        }

        $query = Coin::find()->select(['coin_name', 'symbol']);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count]);
        $pagination->setPageSize($page_size);
        $pagination->setPage($page);
        $coins = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->asArray()
            ->all();

        $result = [
            'page_now' => $pagination->getPage(),
            'page_count' => $pagination->getPageCount(),
            'data' => $coins,
        ];
        Jinglan::success_message($result);
    }

    // 用户申请提现记录
    public static function applyLog($uid=0)
    {
        $request = Yii::$app->request;
        if (empty($uid) || $uid == 0) {
            Jinglan::error_message('_Please_Check_If_The_User_ID_Is_Correct_');
        }
        $uinfo['id'] = $uid;

        // 分页参数
        $page = intval($request->post('page'))>=1 ? $request->post('page') : 1;
        $page_size = intval($request->post('page_size'))>0 ? $request->post('page_size') : 20;

        $query = WithdrawApply::find()
            ->select(['id', 'coin_symbol', 'value_dec','current','withdraw_fee', 'status', 'error_message', 'created_at'])
            ->where(['member_id' => intval($uinfo['id'])]);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count]);
        $pagination->setPage(intval($page)-1);
        $pagination->setPageSize(intval($page_size));
        $withdraw_apply = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy('created_at DESC')
            ->asArray()
            ->all();

        // 暂无数据
        if (!$withdraw_apply) {
            Jinglan::error_message('_No_Data_Query_');
        }

        foreach ($withdraw_apply as $key => $value) {
            $withdraw_apply[$key]['value_dec'] = $value['value_dec'].($value['withdraw_fee']?('(手续费'.$value['withdraw_fee'].($value['current']?('|矿工费'.$value['current']):'').')'):'');
            $withdraw_apply[$key]['created_at'] = date('Y-m-d H:i:s', $value['created_at']);
            $withdraw_apply[$key]['status_msg'] = Jinglan::$db_withdraw_apply['status'][$withdraw_apply[$key]['status']];
        }
        $result = [
            'page_now'   => $pagination->getPage()+1,
            'page_count' => $pagination->getPageCount(),
            'total'      => $count,
            'data'       => $withdraw_apply,
        ];
        Jinglan::success_message($result);
    }

    // 撤销提现申请单
    public static function revokeApply($uid=0)
    {
        $request = Yii::$app->request;
        if (empty($uid) || $uid == 0) {
            Jinglan::error_message('_Please_Check_If_The_User_ID_Is_Correct_');
        }

        // 所需参数
        $uinfo['id'] = $uid;
        $apply_id    = $request->post('apply_id');
        Jinglan::check_empty($apply_id, '_Application_form_ID_can_not_be_empty_');

        $withdraw_apply = WithdrawApply::find()
            ->where(['id' => intval($apply_id)])
            ->andWhere(['member_id' => intval($uinfo['id'])])
            ->one();

        // 订单不存在
        if (!$withdraw_apply) {
            Jinglan::error_message('_The_application_information_was_not_inquired_');
        }

        // 删除该订单信息
        if ($withdraw_apply->delete()) {
            Jinglan::success_message('', '_Delete_Success_');
        }else{
            Jinglan::error_message('_Delete_Failure_');
        }
    }

    // 交易所财务日志
    public static function financialLog($uid=0)
    {
        $request = Yii::$app->request;
        if (empty($uid) || $uid == 0) {
            Jinglan::error_message('_Please_Check_If_The_User_ID_Is_Correct_');
        }
        $uinfo['id'] = (int)$uid;

        // 获取分页参数
        $page      = $request->post('page') ? $request->post('page') : 1;// 获取页面【默认第1页】
        $page      = (int)$page<=1 ? 1 : (int)$page;
        $page_size = $request->post('page_size') ? $request->post('page_size') : 20;// 每页数据量【默认20条数据】
        $page_size = $page_size<=0 ? 20 : (int)$page_size;

        // 获取查询参数
        $coin_symbol      = $request->post('coin_symbol') ? $request->post('coin_symbol') : 'all';// 货币类型【默认all】
        $transaction_type = $request->post('transaction_type') ? $request->post('transaction_type') : 'all';// 交易类型【默认all】
        $begin_time       = $request->post('begin_time') ? $request->post('begin_time') : '0';// 时间【默认为0】
        $end_time         = $request->post('end_time') ? $request->post('end_time') : time();// 时间【默认为0】

        $query = BalanceLog::find()
            ->select(['type', 'coin_symbol', 'ctime', 'change', 'detial_type'])
            ->where(['member_id' => $uinfo['id']]);

        if ($coin_symbol != 'all') {
            $query = $query->andWhere(['coin_symbol' => $coin_symbol]);
        }
        if ($transaction_type != 'all') {
            // 新增交易所查询数据【类型定义为20】
            if (intval($transaction_type) == 20) {
                $rpc = new VesRPC();
                $map1 = 'balance.history';
                // {"id":4,"method":"balance.history","params":[1000013,"BTC","",0,1111111111111,0,50]}: 
                if ($coin_symbol == 'all') {
                    $coin_symbol = '';
                }
                $offset = ($page-1) * $page_size;
                $limit  = $offset + $page_size;
                $map2   = [$uinfo['id'], $coin_symbol, '', $begin_time, $end_time, $offset, $limit];
                $rpc_ret = $rpc->do_rpc($map1, $map2);
                if ($rpc_ret['code'] == 0) {
                    Jinglan::error_message('_Requested_Data_Not_Exist_');
                } else {
                    $data = $rpc_ret['data']['records'];
                    $result = [
                        'page_now'   => $page,
                        'page_count' => '',
                        'total'      => '',
                    ];
                    if (count($data)) {
                        foreach ($data as $key => $value) {
                            $result['data'][$key]['type'] = '20';
                            $result['data'][$key]['coin_symbol'] = $value['asset'];
                            $result['data'][$key]['ctime'] = (string)intval($value['time']);
                            $result['data'][$key]['change'] = $value['change'];
                            if ($value['business'] == 'deposit') {
                                $result['data'][$key]['detail_type'] = '银行账户收入';
                            }else{
                                $result['data'][$key]['detail_type'] = '银行账户支出';
                            }
                        }
                    }else{
                        $result['data'] = [];
                    }
                    Jinglan::success_message($result);
                }
            }else{
                $query = $query->andWhere(['type' => intval($transaction_type)]);
            }
        }
        $query       = $query->andWhere(['and', 'ctime>='.intval($begin_time), 'ctime<='.intval($end_time)]);

        $count       = $query->count();
        $pagination  = new Pagination(['totalCount' => $count]);
        $pagination->setPage($page-1);
        $pagination->setPageSize($page_size);
        $balance_log = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy('ctime DESC')
            ->asArray()
            ->all();

        foreach ($balance_log as $key => $value) {
            if ($value['detial_type'] == 'exchange') {
                if ($value['type'] == 1) {
                    $balance_log[$key]['detial_type'] = '用户交易所收入';
                }else{
                    $balance_log[$key]['detial_type'] = '用户交易所支出';
                }
            }
            if ($value['detial_type'] == 'chain') {
                if ($value['type'] == 1) {
                    $balance_log[$key]['detial_type'] = '充值成功';
                }else{
                    $balance_log[$key]['detial_type'] = '提现成功';
                }
            }
            if ($value['detial_type'] == 'system') {
                if ($value['type'] == 1) {
                    $balance_log[$key]['detial_type'] = 'Successful transfer';
                }else{
                    $balance_log[$key]['detial_type'] = '系统扣款';
                }
            }
            if ($value['detial_type'] == 'withdraw') {
                if ($value['type'] == 1) {
                    $balance_log[$key]['detial_type'] = '提现';
                }else{
                    $balance_log[$key]['detial_type'] = '提现';
                }
            }
            if ($value['detial_type'] == 'withdraw_fee') {
                if ($value['type'] == 1) {
                    $balance_log[$key]['detial_type'] = '提现手续费';
                }else{
                    $balance_log[$key]['detial_type'] = '提现手续费';
                }
            }
            if ($value['detial_type'] == 'release_invite_reward') {
                $balance_log[$key]['detial_type'] = '邀请奖励释放';                
            }            
        }

        $result = [
            'page_now' => $pagination->getPage()+1,
            'page_count' => $pagination->getPageCount(),
            'total' => $count,
            'data' => $balance_log,
        ];

        Jinglan::success_message($result);

    }

    // 添加提现地址
    public static function addAddress($uid=0)
    {
        $request = Yii::$app->request;
        if (empty($uid) || $uid == 0) {
            Jinglan::error_message('_Please_Check_If_The_User_ID_Is_Correct_');
        }
        $uinfo['id'] = (int)$uid;

        // 获取参数【token、title、addr、coin_symbol、description】
        $title       = $request->post('title');
        $addr        = $request->post('addr');
        $coin_symbol = $request->post('coin_symbol');
        $description = $request->post('description');
        Jinglan::check_empty($title, '_The_name_of_the_cash_address_can_not_be_empty_');
        Jinglan::check_empty($addr, '_The_address_can_not_be_empty_');
        Jinglan::check_empty($coin_symbol, '_The_currency_type_of_the_cash_address_can_not_be_empty_');
        $coin_symbol = strtoupper($coin_symbol);

        // 币种支持
        $coins = Coin::find()->where(['symbol' => $coin_symbol])->one();
        if (!$coins) {
            Jinglan::error_message('_Do_not_support_the_currency_withdrawals_do_not_add_such_addresses_');
        }

        // 地址合法性验证
        switch ($coin_symbol) {
            case 'ETH':
                // 判断长度是否为40位【去除0x】
                if (strlen(substr($addr, 2)) != 40) {
                    Jinglan::error_message('_Address_is_illegal_');
                }
                break;

            case 'BTC':
                $btn_client = new BitcoinClient;
                $map1 = 'validateaddress';
                $map2 = [$addr];
                $btc_result = $btn_client->request($map1, $map2);
                if ($btc_result['code'] == 0) {
                    Jinglan::error_message('_Address_is_illegal_');
                }else{
                    if (!$btc_result['data']['isvalid']) {
                        Jinglan::error_message('_Address_is_illegal_');
                    }
                }
                break;
            case 'USDT':
                $usdt_client = new OmnicoreClient;
                $map1 = 'validateaddress';
                $map2 = [$addr];
                $usdt_result = $usdt_client->request($map1, $map2);
                if ($usdt_result['code'] == 0) {
                    Jinglan::error_message('_Address_is_illegal_');
                }else{
                    if (!$usdt_result['data']['isvalid']) {
                        Jinglan::error_message('_Address_is_illegal_');
                    }
                }
                break;
            default:
                // Jinglan::error_message('地址合法性未确认！');
                break;
        }

        $addr_id = $request->post('addr_id');
        // 判断是编辑还是新增
        if (isset($addr_id) && !empty($addr_id)) {
            // 编辑
            $withdraw_address = WithdrawAddress::findOne(intval($addr_id));
            if (!$withdraw_address) {
                Jinglan::error_message('_Failed_to_search_for_this_record_failed_to_change_the_address_');
            }else{
                $withdraw_address->title       = $title;
                $withdraw_address->addr        = $addr;
                $withdraw_address->coin_symbol = $coin_symbol;
                $withdraw_address->member_id   = $uinfo['id'];
                $withdraw_address->coin_id     = $coins->id;
                $withdraw_address->description = $description;
                $withdraw_address->updated_at  = time();
                if ($withdraw_address->save()) {
                    Jinglan::success_message('','_Saved_address_is_successful_');
                }else{
                    Jinglan::error_message('_Saved_address_failed_');
                }
            }
        }else{
            // 新增
            $withdraw_address              = new WithdrawAddress();
            $withdraw_address->title       = $title;
            $withdraw_address->addr        = $addr;
            $withdraw_address->coin_symbol = $coin_symbol;
            $withdraw_address->member_id   = $uinfo['id'];
            $withdraw_address->coin_id     = $coins->id;
            $withdraw_address->description = $description;
            $withdraw_address->created_at  = time();
            if ($withdraw_address->save()) {
                Jinglan::success_message('','_Saved_address_is_successful_');
            }else{
                Jinglan::error_message('_Saved_address_failed_');
            }
        }
    }

    // 删除提现地址
    public static function delAddress($uid=0)
    {
        $request = Yii::$app->request;
        if (empty($uid) || $uid == 0) {
            Jinglan::error_message('_Please_Check_If_The_User_ID_Is_Correct_');
        }
        $uinfo['id'] = (int)$uid;

        $addr_id = $request->post('addr_id');
        Jinglan::check_empty($addr_id, '_Please_select_the_address_to_delete_');


        
        $withdraw_address = WithdrawAddress::findOne(intval($addr_id));
        if (!$withdraw_address) {
            Jinglan::error_message('_Failure_to_query_the_record_and_delete_the_address_failed_');
        }
        if ($withdraw_address->delete()) {
            Jinglan::success_message('','_Delete_Success_');
        }else{
            Jinglan::error_message('_Delete_Failure_');
        }
    }

    // 查询提现地址
    public static function getAddress($uid=0)
    {
        $request = Yii::$app->request;
        if (empty($uid) || $uid == 0) {
            Jinglan::error_message('_Please_Check_If_The_User_ID_Is_Correct_');
        }
        $uinfo['id'] = (int)$uid;

        $withdraw_address = WithdrawAddress::find()
            ->select(['jl_withdraw_address.id', 'jl_withdraw_address.title', 'jl_withdraw_address.addr', 'jl_withdraw_address.coin_symbol', 'jl_withdraw_address.description','jl_coins.icon'])
            ->where(['member_id' => $uinfo['id']])
            ->leftJoin('jl_coins', 'jl_coins.id = jl_withdraw_address.coin_id')
            ->asArray()
            ->all();

        if (count($withdraw_address)) {
            Jinglan::success_message($withdraw_address);
        }else{
            Jinglan::error_message('_No_Data_Query_');
        }
    }

    // 提现前准备
    public static function withdrawPrepare($uid=0)
    {
        $request = Yii::$app->request;
        if (empty($uid) || $uid == 0) {
            Jinglan::error_message('_Please_Check_If_The_User_ID_Is_Correct_');
        }
        $uinfo['id'] = (int)$uid;

        // 获取参数
        $coin_symbol = $request->post('coin_symbol');
        Jinglan::check_empty($coin_symbol, '_Currency_Sign_Not_Empty_');

        // 是否支持该币种提现
        $coins = Coin::find()->where(['symbol' => $coin_symbol])->andWhere(['enable' => 1])->asArray()->one();

        if (!$coins) {
            Jinglan::error_message('_Temporarily_Do_Not_Support_The_Currency_Type_Out_Of_Service_');
        }

        if($coins['withdraw_enable']!='1'){
             Jinglan::error_message('该币种暂停提现');
        }
        // 获取用户该币种总资产
        $_POST['return_way'] = 'array';
        $balance_all = Trade::balance_v2($uinfo['id']);
        $user_balance = 0;
        foreach ($balance_all[0] as $key => $value) {
            if ($value['name'] == $coin_symbol) {
                $user_balance = $value['available'];
                break;
            }
        }
        // 判断币种是否为代币
        $coin_is_token = $coins['ram_status']==1 ? true : false;
        if ($coin_is_token) {
            $coin_token_name = $coin_symbol;
            $coin_symbol     = 'ETH';
        }

        $result = [
            'balance'      => (string)$user_balance,
            'low'          => 0,
            'height'       => 0,
            'current'      => 0,
            'limit_amount' => (string)$coins['limit_amount'],
            'withdraw_fee' => (string)$coins['withdraw_fee'],
            'unit'         => $coins['unit'],
        ];
        Jinglan::success_message($result);
    }

    // 提现【从用户银行账户转到指定钱包地址】
    public static function turnCard($uid = 0)
    {
        $request = Yii::$app->request;
        if (empty($uid) || $uid == 0) {
            Jinglan::error_message('_Please_Check_If_The_User_ID_Is_Correct_');
        }
        $uinfo['id'] = $uid;

        $chain_network = empty($_POST['chain_network']) ? 'testnet' : $_POST['chain_network'];
        if ($chain_network == 'main_network') {
            $network_type = 0;// 主网
        }else{
            $network_type = 1;// 测试网
        }

        // 获取参数【token、kk钱包地址、转出金额、币种类型】
        $coin_symbol = $request->post('coin_symbol');
        $wallet_card = $request->post('wallet_card');
        $wallet_name = $request->post('wallet_name');
        $phone = $request->post('phone');
        $value_dec   = $request->post('value');
        $description = $request->post('description');
        Jinglan::check_empty($coin_symbol, '_MoneyType_Not_Empty_');
        Jinglan::check_empty($wallet_card, '帐号不能为空');
        Jinglan::check_empty($value_dec, '_The_Amount_Of_Transfer_Can_Not_Be_Empty_');
        $phone = Jinglan::check_mobile_phone($phone);
        // 实名认证【verified_status】
        $member = Member::findOne(['id'=>intval($uinfo['id'])]);
        if ($member->verified_status != 1) {
           Jinglan::error_message('_Users_Are_Not_Authenticated_By_Real_Names_');
        }

        // 判断申请参数的合法性
        // 1：币种合法性【不支持的币种不接受提现】
        $coins = Coin::find()->where(['symbol' => $coin_symbol])->andWhere(['enable' => 1])->one();
        if (!$coins) {
            Jinglan::error_message('_Temporarily_Do_Not_Support_The_Currency_Type_Out_Of_Service_');
        }
        // 存储本次提现手续费
        $withdraw_fee = $coins->withdraw_fee;

        // 判断币种是否为代币
        $coin_is_token = $coins->ram_status==1 ? true : false;
        if ($coin_is_token) {
            $coin_token_name = $coin_symbol;
            $coin_symbol     = 'ETH';
        }

        // 3：金额合法性
        if (!is_numeric($value_dec)) {
            Jinglan::error_message('_The_amount_of_withdrawals_is_not_legal_');
        }

        // $current   = (string)$current;
        $value_dec = floatval($value_dec);

        // 重置代币名称
        if ($coin_is_token) {
            $coin_symbol = $coin_token_name;
        }

        // 获取用户资产
        $_POST['return_way'] = 'array';
        $balance_all = Trade::balance_v2($uinfo['id']);// 成功返回数据，失败返回false
        if (!$balance_all) {
            Jinglan::error_message('_The_application_process_failed_unexpectedly_Please_try_again_later_');
        }
        foreach ($balance_all[0] as $key => $value) {
            if ($value['name'] == $coin_symbol) {
                if ($value['available'] < ($value_dec+$coins->withdraw_fee)) {
                    Jinglan::error_message('Your_bank_account_is_insufficient_please_change_the_amount_transferred');
                }
                break;
            }
        }

        // 存储提现申请表
        $withdraw_apply = new WithdrawApply();
        $withdraw_apply->member_id     = intval($uinfo['id']);
        $withdraw_apply->coin_symbol   = $coin_symbol;
        $withdraw_apply->wallet_card   = $wallet_card;
        $withdraw_apply->wallet_name   = $wallet_name;
        $withdraw_apply->value_dec     = $value_dec;
        // $withdraw_apply->current       = $current;
        $withdraw_apply->phone       = $phone;
        $withdraw_apply->withdraw_fee  = $withdraw_fee;
        $withdraw_apply->description   = $description;
        $withdraw_apply->status        = 1;//【1：待审核、2：审核通过、3：未通过】
        $withdraw_apply->created_at    = time();
        $withdraw_apply->chain_network = $network_type;
        $withdraw_apply->type = 1;
        if (!$withdraw_apply->save()) {
            Jinglan::error_message('_Withdrawals_failed_');
        }
      Jinglan::success_message('','_Successful_application_for_withdrawals_');  
    }
}
