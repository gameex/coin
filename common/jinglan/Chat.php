<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/11
 * Time: 14:39
 */

namespace common\jinglan;

use common\models\WithdrawApply;
use yii\data\Pagination;
use jinglan\cloud\RTC\ServerAPI;
use Yii;

class Chat extends Jinglan
{
    public static function init($uinfo){
        $merchantId = Yii::$app->config->info('CHAT_MERCHANTID');
        $appKey = Yii::$app->config->info("CHAT_APPKEY");
        $appSecret = Yii::$app->config->info("CHAT_APPSECRET");
        $url = Yii::$app->config->info("CHAT_WEBSOCKET_URL");
        $ret = array(
            'appkey' => $appKey,
            'websocket_url' => $url.'?merchant_uid='.$merchantId.'&app='.$appKey.'&token='.$appKey.'&uid='.$uinfo['id'].'&nickname='.$uinfo['nickname'].'&portrait='.urlencode($uinfo['head_portrait']),
        );
        Jinglan::success_message($ret);
    }
    
    // 用户申请提现记录
    public static function initPc($uid=0){
        $request = Yii::$app->request;
        if (empty($uid) || $uid == 0) {
            Jinglan::error_message('_Please_Check_If_The_User_ID_Is_Correct_');
        }
        $uinfo['id'] = $uid;

        $status = [
            1 => "待审核",
            2 => "审核通过",
            3 => "未通过",
            4 => "提现失败",
            5 => "提现成功",
        ];
        
        // 分页参数
        $page = intval($request->post('page'))>=1 ? $request->post('page') : 1;
        $page_size = intval($request->post('page_size'))>0 ? $request->post('page_size') : 20;
        $coin_symbol      = $request->post('coin_symbol') ? $request->post('coin_symbol') : 'all';// 货币类型【默认all】
        $begin_time       = $request->post('begin_time') ? $request->post('begin_time') : '0';// 时间【默认为0】
        $end_time         = $request->post('end_time') ? $request->post('end_time') : time();// 时间【默认为0】

        $query = WithdrawApply::find()
            ->select(['id', 'coin_symbol', 'value_dec', 'status', 'error_message', 'created_at'])
            ->where(['member_id' => intval($uinfo['id'])]);
        if ($coin_symbol != 'all') {
            $query = $query->andWhere(['coin_symbol' => $coin_symbol]);
        }
        $query = $query->andWhere(['and', 'created_at>='.intval($begin_time), 'created_at<='.intval($end_time)]);
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
            $withdraw_apply[$key]['created_at'] = date('Y-m-d H:i:s', $value['created_at']);
            $withdraw_apply[$key]['status'] = $status[$withdraw_apply[$key]['status']];

        }

        $result = [
            'page_now'   => $pagination->getPage()+1,
            'page_count' => $pagination->getPageCount(),
            'total'      => $count,
            'data'       => $withdraw_apply,
        ];
        Jinglan::success_message($result);
    }

    public static function history($uid){
        $request = \Yii::$app->request;
        $merchantId = Yii::$app->config->info('CHAT_MERCHANTID');
        $appKey = Yii::$app->config->info("CHAT_APPKEY");
        $appSecret = Yii::$app->config->info("CHAT_APPSECRET");
        $order_id = $request->post('order_id');
        Jinglan::check_empty($order_id,'_Order_ID_Cannot_Be_Empty_');
        
        try{
            $m = new \MongoDB\Driver\Manager("mongodb://".Yii::$app->config->info("CHAT_MONGODB_USER").":".Yii::$app->config->info("CHAT_MONGODB_PASSWORD")."@".Yii::$app->config->info("CHAT_MONGODB_HOST").":".Yii::$app->config->info("CHAT_MONGODB_PORT")."/".Yii::$app->config->info("CHAT_MONGODB_DB"));
            //$m = new \MongoDB\Driver\Manager("mongodb://im_dba:im_dba@127.0.0.1:27017/im");
            // 查询数据
            $filter = array(
                'type' => 'TxtMsg',
                'merchant_uid' => (int)$merchantId,
                'app_key' => $appKey,
                'groupId' => (string)$order_id,
            );
            $options = [
                'projection' => ['_id'=> 0, 'client_id'=>0, 'type'=>0, 'merchant_uid'=>0],
                'sort' => ['x' => -1],
            ];
            $query = new \MongoDB\Driver\Query($filter, $options);
            $cursor = $m->executeQuery('im.message', $query);
            $ret = [];
            foreach ($cursor as $document) {
                array_push($ret, json_decode(json_encode($document), true));
            }
            
            if (empty($ret)){
                Jinglan::error_message('_No_Data_Query_');
            }else{
                Jinglan::success_message($ret);
            }
        }catch (Exception $e){
           Jinglan::error_message("检查mongodb");
        }
    }
}