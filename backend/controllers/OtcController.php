<?php
namespace backend\controllers;


use common\models\Coins;
use common\models\OtcCoinList;
use common\models\OtcMarket;
use common\models\OtcOrder;
use common\models\member\Member;
use yii\web\NotFoundHttpException;

use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use common\models\ApiAccessToken;
use linslin\yii2\curl;
/**
 *场外交易控制器
 *
 * Class MemberController
 * @package backend\modules\member\controllers
 */
class OtcController extends MController{
	
    public $ORDER_STATUS = [
        0  => '已经取消',
        1  => '交易成功',
        2  => '待支付',
        3  => '待放币',
        11 => '申诉未处理',
        12 => '申诉已处理',
    ];
    
    public $MARKET_STATUS = [
        0 => '下架',
        1 => '正常',
        2 => '撤销',
    ];
 
      public $COIN_STATUS = [
        0 => '禁用中',
        1 => '已启用',
    ];
          
    public $SIDE_NAME = [
        1 => '卖',
        2 => '买',
    ];    

    /**
     * 订单管理
     */
    public function actionOrder()
    {
    	$request = Yii::$app->request;       
 		$tablePrefix = Yii::$app->db->tablePrefix;
        if($request->isGet){
            $type = $request->get('type',200);
            $keyword = $request->get('keyword','');
        }
        switch ($type){
            case 1:
                $where = ['like','a.coin_name',$keyword];
                break;
            case 2:
                $where = ['like','a.seller_uid',$keyword];
                break;
            case 3:
                $where = ['like','a.buyer_uid',$keyword];
                break;
            default:
                $where = [];
                break;
        }
		$data = (new \yii\db\Query())
          	 ->select('a.id,a.status,a.side,a.seller_uid,a.buyer_uid,a.price_usd,a.amount,a.coin_name,a.total_price_usd,order_time,pay_time,deal_time,b.id uid,b.nickname,b.username,b.head_portrait')
          	   ->from("{$tablePrefix}otc_order  AS a")->where($where);
	        // if(0){
	        	
            	     //$data = $data->where(['and', "1=1", ['or', "seller_uid={$uid}", "buyer_uid={$uid}"]])
	        // }
        $pages  = new Pagination(['totalCount' =>$data->count(), 'pageSize' =>$this->_pageSize]);
        $models = $data->leftJoin("{$tablePrefix}member AS b",'a.other_uid = b.id')
                ->offset($pages->offset)
        		->orderBy('a.id desc')                           		
                ->limit($pages->limit)
                ->andWhere($where)
                ->all();
        if (!empty($models)){
            $order_ids = array_column($models,'id');
            $appeals = \common\models\OtcAppeal::find()->where(['in','order_id',$order_ids])->select('order_id,status')->asArray()->all();
            if (!empty($appeals)){
                $appeals = array_column($appeals,'status','order_id');
                $appeal_order_ids = array_keys($appeals);
            }else{
                $appeals = [];
                $appeal_order_ids = [];
            }
        }else{
            $appeals = [];
            $appeal_order_ids = [];
        }
                
        return $this->render('index', [
             'models'           => $models,
       	     'Pagination'       => $pages,
       	     'status'           => $this->ORDER_STATUS,
       	     'side'             => $this->SIDE_NAME,
             'type'             => $type,
             'keyword'          => $keyword,
            'appeals'           => $appeals,
            'appeal_order_ids'=> $appeal_order_ids,
        ]);
    }

    /**
     * 广告列表
     *
     */
    public function actionMarket()
    {
      	$request = Yii::$app->request;       
 		$tablePrefix = Yii::$app->db->tablePrefix;
	    if($request->isGet){
            $type = $request->get('type',200);
            $keyword = $request->get('keyword','');
        }
        switch ($type){
            case 1:
                $where = ['like','a.coin_name',$keyword];
                break;
            case 2:
                $where = ['like','a.uid',$keyword];
                break;
            default:
                $where = [];
                break;
        }
		$data = (new \yii\db\Query())
            ->select('a.id,a.uid,a.side,a.coin_name,a.min_num,a.max_num,a.price_usd,card_enable,alipay_enable,wechat_enable,b.nickname,b.head_portrait,a.deal_count,a.deal_rate,a.publish_time,a.status')
            ->from("{$tablePrefix}otc_market AS a")->where($where);
	        // if(0){
            	     // where(['a.status'=>1,'a.side'=>$side,'coin_name'=>$coin_name])
	        // }
	        $pages  = new Pagination(['totalCount' =>$data->count(), 'pageSize' =>$this->_pageSize]);
	        $models = $data->leftJoin("{$tablePrefix}member AS b",'a.uid = b.id')
	                ->offset($pages->offset)
            	    ->orderBy('a.id desc')                           		
	                ->limit($pages->limit)
                    ->andWhere($where)
	                ->all();
	        return $this->render('market', [
	            'models'           => $models,
           	     'Pagination'       => $pages,
           	     'side'       =>  $this->SIDE_NAME,	     
           	     'status'       =>  $this->MARKET_STATUS,   
                 'type'             => $type,
                 'keyword'          => $keyword,        	           
	        ]);
    }

    /**
     * 检测是否已添加
     */
    public function actionCheckCoin(){

        $request = Yii::$app->request;
        if($request->isPost){
            $id = htmlspecialchars($request->post('id'));
        }

        $model = OtcCoinList::find()->select('id')->where(['coin_id' => $id])->asArray()->one();

        if(!$model){
            return json_encode(['code'=>200,'message' => 'success']);
        }else{
            return json_encode(['code'=>500,'message'=>'error']);
        }
    }
    /**
     * @return string|\yii\web\Response
     */
    public function actionEdit(){

        $request = Yii::$app->request;

        $id  =  htmlspecialchars($request->get('id'));

        $model   = $this->actionFindCoinModel($id);


        if($model->load(Yii::$app->request->post())) {
            $coin_id = $_POST['OtcCoinList']['coin_name'];
            $coin_model = Coins::find()->select('symbol')->where(['id' => $coin_id])->asArray()->one();

            $model->coin_id = $coin_id;
            $model->coin_name = $coin_model['symbol'];

            if ($model->save()) {
                return $this->redirect(['coins']);
            }
        }
        return $this->render('edit',[
            'model' => $model,
        ]);

    }
    public function actionFindCoinModel($id){
        if(empty($id)){
            return new OtcCoinList();
        }
        if(empty($model = OtcCoinList::findOne($id))){
            return new OtcCoinList();
        }
        return $model;
    }

    /**
     * 币种列表
     *
     */
    public function actionCoins()
    {
    	           
      	$request = Yii::$app->request;       
 		$tablePrefix = Yii::$app->db->tablePrefix;
	
		$data = (new \yii\db\Query())
            ->select('a.id,a.coin_id,a.limit_amount,a.max_register_time,a.max_register_num,a.coin_name,b.icon,a.status')
            ->from("{$tablePrefix}otc_coinlist AS a");

	        $pages  = new Pagination(['totalCount' =>$data->count(), 'pageSize' =>$this->_pageSize]);
	        $models = $data->leftJoin("{$tablePrefix}coins AS b",'a.coin_id = b.id')        
	                       ->offset($pages->offset)
            		     ->orderBy('a.coin_id asc')                           		
	                       ->limit($pages->limit)
	                       ->all();

	        return $this->render('coins', [
	            'models'           => $models,
           	     'Pagination'       => $pages,
                  'status'       =>  $this->COIN_STATUS,
          	    
	        ]);

    }
    /**
     * @param $id
     * @return mixed
     */
    public function actionCoinDelete($id)
    {
        if($this->actionFindCoinModel($id)->delete())
        {
            return $this->message("删除成功",$this->redirect(['coins']));
        }
        else
        {
            return $this->message("删除失败",$this->redirect(['coins']),'error');
        }
    }
    /**
     * 删除
     *
     * @param $id
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     * @throws yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {   

        if($this->findModel($id)->delete())
        {
            return $this->message("删除成功",$this->redirect(['index']));
        }
        else
        {
            return $this->message("删除失败",$this->redirect(['index']),'error');
        }
    }

    /**
     * 修改个人资料
     *
     * @return string|yii\web\Response
     */
    public function actionPersonal()
    {
        $request  = Yii::$app->request;
        $id       = $request->get('id');
        $model    = $this->findModel($id);

        // 提交表单
        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['index']);
        }

        return $this->render('personal', [
            'model' => $model,
        ]);
    }
    /**
     * @return string
     */
    public function actionDisable(){
        $request = Yii::$app->request;
        $id      = $request->post('id');
        $status  = $request->post('status');
        $model   = $this->findOrderModel($id);
        $model['status'] = $status;
        if($model->save()){
            return json_encode(['code' => 200, 'message' => '操作成功']);
        }else{
            return json_encode(['code' => 201, 'message' => '操作失败']);
        }
    }
    
    protected function findOrderModel($id)
    {
        if (empty($id))
        {
            return new OtcOrder;
        }

        if (empty($model = OtcOrder::findOne($id)))
        {
            return new OtcOrder;
        }

        return $model;
    }

    /**
     * 返回模型
     *
     * @param $id
     * @return Member|null|static
     */
    protected function findModel($id)
    {
        if (empty($id))
        {
            return new Member;
        }

        if (empty($model = Member::findOne($id)))
        {
            return new Member;
        }

        return $model;
    }
    /**
     * @return string
     */
    public function actionEnable(){
        $request = Yii::$app->request;
        $id      = $request->post('id');
        $status  = $request->post('status');
        $model   = $this->findMarketModel($id);
        $model['status'] = $status;
        if($model->save()){
            return json_encode(['code' => 200, 'message' => '操作成功']);
        }else{
            return json_encode(['code' => 201, 'message' => '操作失败']);
        }
    }

    protected function findMarketModel($id)
    {
        if (empty($id)) {
            return new OtcMarket();
        }

        if (empty($model = OtcMarket::findOne($id))) {
            return new OtcMarket();
        }

        return $model;
    }

    /**
     * @return string
     */
    public function actionCoinEnable(){
        $request = Yii::$app->request;
        $id      = $request->post('id');
        $status  = $request->post('status');
        $model   = $this->actionFindCoinModel($id);
        $model->status = $status;

        if($model->save()){
            return json_encode(['code' => 200, 'message' => '操作成功']);
        }else{
            return json_encode(['code' => 201, 'message' => '操作失败']);
        }
    }
    public function actionQuery($id)
    {    
        // 获取用户资产信息
        // $session = Yii::$app->session;
        // $session->open();
        // $session->set('return_way', 'array');
        // $balance_all = Trade::balance($id);// 成功返回数据，失败返回false
        // $session->remove('return_way');
        // $session->close();
        $aaa = new \vendor\jinglan\ves\VesRPC();
        p(22222);exit();
        $balance_all = Trade::aaa();
        if (!$balance_all) {
            return json_encode(['code'=>500,'data'=>'没有查询到数据']);
        }
        return json_encode(['code'=>200,'data'=>$balance_all]);
    }


    // 执行放币操作
    public function actionPutMoney()
    {
        // 获取订单id
        $request = Yii::$app->request;
        $order_id = $request->get('id')??'';
        if (empty($order_id)) {
            return $this->message("请传递正确的参数！",$this->redirect(['order']),'error');
        }

        // 获取订单详情
        $order = OtcOrder::findOne(intval($order_id));
        if (empty($order)) {
            return $this->message("查询不到该订单信息！",$this->redirect(['order']),'error');
        }

        // 获取发布广告用户id及access_token
        $uid = $order->seller_uid;
        $access_token = ApiAccessToken::find()->select(['access_token'])->where(['user_id'=>intval($uid)])->one();
        if (empty($access_token)) {
            return $this->message("无法获取订单用户的token！",$this->redirect(['order']),'error');
        }

        // php发起curl请求api接口
        $curl = new curl\Curl();
        $url = Yii::$app->request->hostInfo.'/api/otc/deal';
        $data = [
            'access_token' => $access_token->access_token,
            'order_id'     => $order_id,
        ];
        $response = $curl->setRequestBody(http_build_query($data))->post($url);
        switch ($curl->responseCode) {
            case 'timeout':
                return $this->message("请求超时！",$this->redirect(['order']),'error');
                break;

            case 200:
                $result = $response;
                break;

            case 404:
                return $this->message("404找不到请求地址！",$this->redirect(['order']),'error');
                break;

            default:
                $result = $response;
        }

        // 判断最终结果(先转换为json数据之后获取数据)
        $obj_result = json_decode($result);
        if ($obj_result == 200) {
            return $this->message("放币成功！",$this->redirect(['order']));
        }else{
            return $this->message("放币失败：".$result,$this->redirect(['order']),'error');
        }
    }
}