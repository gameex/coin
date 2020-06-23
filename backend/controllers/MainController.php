<?php
namespace backend\controllers;

use api\models\ExchangeCoins;
use api\models\Member;
use common\jinglan\Jinglan;
use common\models\OtcCoinList;
use common\models\OtcOrder;
use jinglan\ves\VesRPC;
use Yii;
use jianyan\basics\common\models\sys\Manager;

/**
 * 主控制器
 *
 * Class MainController
 * @package backend\controllers
 */
class MainController extends MController
{
    /**
     * 主体框架
     */
    public function actionIndex()
    {
        // 用户ID
        // 
        $id = Yii::$app->user->id;
        $user = Manager::find()
            ->where(['id' => $id])
            ->with('assignment')
            ->asArray()
            ->one();

        if (empty($user)) {
            if (!empty($_SESSION['__admin'])) {
                $id = $_SESSION['__admin'];
                    $user = Member::find()
                        ->where(['id' => $id])
                        ->asArray()
                        ->one();
            }
        }
        if (empty($user)) {
            Yii::$app->getResponse()->redirect('/backend/site/login/');
        }else{
            return $this->renderPartial('@basics/backend/views/main/index',[
                'user'  => $user,
            ]);
        }
    }

    /**
     * 系统首页
     */
    public function actionSystem()
    {
        $data['member_count'] =  $this->actionUserCount();
        $data['otc_coin'] = $this->actionGetOtcCoins();
        $data['exchange_coin'] = $this->actionGetExchangeCoins();

        $pair = $this->actionGetExchangeCoins();
        $pairData = $this->actionPairData($pair[0]['id']);

        return $this->render('system',[
            'data' =>$data,
            'pairData' => $pairData,
        ]);
    }

    public function actionAgent()
    {
        return $this->render('agent',[
            'data' =>1,
        ]);
    }


    // 获取法币的币种名称
    private function actionGetOtcCoins(){
        $otc_coin = OtcCoinList::find()->select('id,coin_id,coin_name')->asArray()->all();
        if(!empty($otc_coin)){
            return $otc_coin;
        }else{
            return false;
        }
    }

    // 获取法币统计数据
    public function actionGetOtcData(){
        $yesterday = date('Y-m-d H:i:s',strtotime(date("Y-m-d",strtotime('-1 day')),time()));
        $today = date('Y-m-d H:i:s',strtotime(date("Y-m-d"),time()));

        $request = Yii::$app->request;
        if($request->isPost){
            $id = htmlspecialchars($request->post('id'));
        }
        $order_count = OtcOrder::find()->where(['coin_id' => $id,'status' => 1])->count('amount');
        $old_order_count = OtcOrder::find()->where(['coin_id' => $id,'status' => 1])->andWhere(['<=','deal_time',$today])->andWhere(['>=','deal_time',$yesterday])->count('amount');
        if(empty($order_count)){
            return json_encode(['code' => 500 ,'message' => '暂无数据']);
        }
        $money_sum = OtcOrder::find()->where(['coin_id' => $id,'status' => 1])->sum('total_price_usd');
        $old_money_sum = OtcOrder::find()->where(['coin_id' => $id,'status' => 1])->andWhere(['<=','deal_time',$today])->andWhere(['>=','deal_time',$yesterday])->sum('total_price_usd');
        if(empty($money_sum)){
            return json_encode(['code' => 500 ,'message' => '暂无数据']);
        }
        $otc_total = [
            'relaData' => [
                'count' => $order_count,
                'sum'    => $money_sum,
            ],
           'oldData' => [
               'count' => $old_order_count,
               'sum'    => $old_money_sum,
           ]
        ];
        return json_encode(['code' => 200,'data' =>$otc_total,'message' => 'Success']);

    }

    // 获取币币交易的币种名称
    private function actionGetExchangeCoins(){
        $exchange_coin = ExchangeCoins::find()->where(['enable'=>1])->select('id,stock,money')->orderBy('listorder DESC')->asArray()->all();
        if(!empty($exchange_coin)){
            return $exchange_coin;
        }else{
            return false;
        }
    }
    // 获取币币交易统计数据
    public function actionGetPairData(){
        $request = Yii::$app->request;
        if($request->isPost){
            $id = htmlspecialchars($request->post('id'));
        }
        if($data = $this->actionPairData($id)){
            return json_encode(['code' => 200,'data' => $data,'message' => 'Success']);
        }else{
            return json_encode(['code' => 500,'message' => '暂无数据']);
        }
    }

    private function actionPairData($id){
        $exchange_model = ExchangeCoins::find()->select('id,stock,money')->where(['id' => $id])->one();
        if(empty($exchange_model)){
            return json_encode(['code' => 500, 'message' => '暂无数据']);
        }
        $pair = $exchange_model->stock . $exchange_model->money;
        $rpc = new VesRPC();
        $rpc_ret = $rpc->do_rpc('market.summary', []);
        if($rpc_ret['code'] == 0){
            return json_encode(['code' => 500, 'message' =>$rpc_ret['data']]);
        }else{
            $market = $rpc_ret['data'];
        }
        $data = [];
        foreach ($market as $val){
            if($pair == $val['name']){
                $data['pair_money'] = $val['ask_amount'] + $val['bid_amount'];
                $data['ask_count'] = $val['ask_count'];
                $data['bid_count'] = $val['bid_count'];
                $data['ask_amount'] = $val['ask_amount'];
                $data['bid_amount'] = $val['bid_amount'];
            }
        }
        if(empty($data)){
            $data['pair_money'] = 0;
            $data['ask_count'] = 0;
            $data['bid_count'] = 0;
            $data['ask_amount'] = 0;
            $data['bid_amount'] = 0;
            return $data;
        }else{
            return $data;
        }
    }


    // 所有用户的统计值
    public function actionUserCount(){
        $member_total = $this->actionCount();
        $otc_merchant_total = $this->actionCount(['otc_merchant' => 1]);
        $verified_member_total = $this->actionCount(['verified_status' => 1]);

        $Yes_member_total = $this->actionYesCount([]);
        $Yes_otc_merchant_total = $this->actionYesCount(['otc_merchant' => 1]);
        $Yes_verified_member_total = $this->actionYesCount(['verified_status' => 1]);

        $data['total'] = [
            'member_total' => $member_total,
            'otc_merchant_total' => $otc_merchant_total,
            'verified_member_total' => $verified_member_total,
        ];
        $data['yesterday_total'] = [
            'member_total' => $Yes_member_total,
            'otc_merchant_total' => $Yes_otc_merchant_total,
            'verified_member_total' => $Yes_verified_member_total,
        ];
        return $data;
    }
    // 当天用户的统计数
    public function actionCount($where = []){
        $total = Member::find()->where($where)->count();
        return $total;
    }
    // 昨天用户的统计数
    public function actionYesCount($where = []){
        $yesterday = strtotime(date("Y-m-d",strtotime('-1 day')),time());
        $today = strtotime(date("Y-m-d"),time());
        $total = Member::find()->select('id')->where(['>=','created_at',$yesterday])->andWhere(['<=','created_at',$today])->andWhere($where)->count();
        return $total;
    }
    public function actionRedirect($url){
        header('Location: '.$url);
        exit;
    }

}