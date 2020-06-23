<?php
/**
 * Created by PhpStorm.
 * User: op
 * Date: 2018-05-29
 * Time: 19:26
 */

namespace api\controllers;

use Yii;
use yii\db\Query;
use yii\db\Expression;
use yii\web\UploadedFile;
use yii\web\Session;
use yii\data\Pagination;
use api\models\Member;
use api\models\MemberVerified;
use api\models\Message;
use api\models\Transaction;
use api\models\EmailCode;
use api\models\Varcode;
use common\helpers\IdCardHelper;
use common\helpers\FileHelper;
use common\helpers\StringHelper;
use common\jinglan\Common;
use common\jinglan\Trade;
use common\jinglan\Jinglan;
use common\jinglan\CreateWallet;
use common\models\OtcMerchants;
use common\models\MemberWealthOrder;
use common\models\MemberWealthPackage;
use common\models\base\AccessToken;
use jinglan\bitcoin\Balance;
use Denpa\Bitcoin\Client as BitcoinClient;

class TaskController extends ApibaseController
{
    public $modelClass = '';

    public function init(){
        parent::init();
    }



    /**
     * 锁仓收益返利定时任务
     */
    public function actionRelease()
    {
        //一个月之前的时间戳
        //var_dump(date('Y-m-d H:i:s',strtotime("-0 year -1 month -0 day")));die();
        //1.查询需要产生收益VIP订单
        $where = [
            'and',
            ['=', 'status', 1],
            ['>', 'surplus_period', 0],
            ['<', 'last_allocation', strtotime("-0 year -0 month -1 day")],
        ];
        $query = MemberWealthOrder::find();
        $data = $query->where($where)->select('*')->orderBy('ctime DESC')->asArray()->all();

        if (empty($data)){
            die('none order process ...');
        }


        $count = 0;
        foreach ($data as $k => &$x){
            $day_profit = $x['day_profit'];
            $order_amount = $x['amount'];
            $coin_symbol = $x['coin_symbol'];
            $uid = $x['uid'];


            $revenue = $order_amount * $day_profit / 100;

            $release_log_str = $x['log'].'(时间'.date('Y-m-d H:i:s', time()).', 第'.(($x['period']-$x['surplus_period'])+1).'次释放, 获得收益:'.$revenue.', 剩余天数:'.($x['surplus_period']-1).')--';


            //更新订单状态
            $order_model = MemberWealthOrder::find()->where(['id'=>$x['id']])->one();
            $order_model->revenue = $order_model->revenue + $revenue;
            $order_model->get_profit = $order_model->get_profit + $revenue;
            $order_model->surplus_period = $order_model->surplus_period -1;
            $order_model->last_allocation = time();
            $order_model->log = $release_log_str;
            $order_model->save(false);

            //加收益
            $tablePrefix = Yii::$app->db->tablePrefix;
            Yii::$app->db->createCommand()->insert("{$tablePrefix}member_wealth_balance", [ 
                'uid' => $uid,
                'uid2' => 0,
                'amount' => $revenue ,
                'coin_symbol' => $coin_symbol,
                'memo' => '矿机释放收益,数量: '.$revenue.' '.$coin_symbol,
                'ctime' => time(),
            ])->execute();
            $id = Yii::$app->db->getLastInsertID();


            //给上级返利
            $this->rebate($uid,$revenue,$coin_symbol);
            $count += 1;
        }
        echo 'ok';
        die();
    }

    //给上级返利,参数为当前用户id,当前用户获得的收益
    private function rebate($uid,$money=0,$coin_symbol='HTC'){
        $user_info = Member::find()->where(['id'=>$uid])->asArray()->one();
        //上级节点奖励  -1-200001-200002-200003-200004-200005-
        $path = $user_info['path'];
        if (empty($path)) {
            return;
        }
        $path_arr =  explode("-",$path);
        $count = count($path_arr);
        for($i=0;$i<$count;$i++){
            $top_uid = intval($path_arr[$i]);         
            $level = $i + 1;
            $profit_num = $this->calc_top_profit($top_uid,$level,$money);
            echo "--->Top uid : $top_uid , level $level , num $profit_num \r\n";
            if($profit_num>0){
                //加返利
                $tablePrefix = Yii::$app->db->tablePrefix;
                Yii::$app->db->createCommand()->insert("{$tablePrefix}member_wealth_balance", [ 
                    'uid' => $top_uid,
                    'uid2' => $uid,
                    'amount' => $profit_num ,
                    'coin_symbol' => $coin_symbol,
                    'memo' => "用户$uid-层级$level-返奖励$profit_num",
                    'ctime' => time(),
                ])->execute();
                $id = Yii::$app->db->getLastInsertID();
            }
        }
    }

    //计算上级应得奖励
    private function calc_top_profit($top_uid,$level=1,$num){
        if ($level == 1) {
            return $num*0.5;
        }
        if ($level == 2) {
            return $num*0.3;
        }
        if ($level == 3) {
            return $num*0.2;
        }
        if (($level > 2) && ($level < 11)) {
            return $num*0.1;
        }
        return 0;
    }












}