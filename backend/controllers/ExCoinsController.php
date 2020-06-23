<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/14
 * Time: 12:49
 */

namespace backend\controllers;


use api\models\ExchangeCoins;
use common\models\Coins;
use Yii;
use yii\data\Pagination;

class ExCoinsController extends MController{

    protected $STATUS = [
        '0' => '<sapn style="color:#E33545"><i class="fa fa-fw fa-close"></i>已禁用</span>',
        '1' => '<sapn style="color:#28A745"><i class="fa fa-fw fa-check"></i>已启用</span>',
    ];
    // 交易对
    public function actionTransactionPair(){
        $request = Yii::$app->request;
        if($request->isGet){
            $type = $request->get('type',200);
            $keyword = $request->get('keyword','');
        }
        switch ($type){
            case 1:
                $where = ['like','coin_name',$keyword];
                break;
            case 2:
                !empty($keyword)?$where = ['=','symbol',$keyword]:$where = [];
                break;
            case 3:
                if($type == 500){
                    $keyword = 0;
                }

                !empty($keyword)? $where = ['=','enable',$keyword]:$where = [];
                break;
            default:
                $where = [];
                break;
        }
        $models = ExchangeCoins::find()->orderby('listorder');

        $pages = new Pagination(['totalCount' =>$models->count(), 'pageSize' =>$this->_pageSize]);
        $data = $models->offset($pages->offset)->limit($pages->limit)->andWhere($where)->all();

        $symbol = Coins::find()->all();
        return $this->render('list', [
            'Pagination' => $pages,
            'data' => $data,
            'status'  => $this->STATUS,
            'type' => $type,
            'keyword' => $keyword,
            'symbol' => $symbol,
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionEdit(){
        $request = Yii::$app->request;
        $id      = $request->get('id');
        $model   = $this->findModel($id);
        if($model->load(Yii::$app->request->post())) {

            if ($model->save()) {
                return $this->redirect(['transaction-pair']);
            }
        }
        return $this->render('edit', [
            'model' => $model,
        ]);
    }

    public function actionCheckConis(){
        $request = Yii::$app->request;

        if($request->isPost){
            $stock = htmlspecialchars($request->post('stock'));
            $money = htmlspecialchars($request->post('money'));
        }

        $data = ExchangeCoins::find()->select('id')->where(['stock' => $stock,'money' => $money])->orWhere(['stock' => $money,'money' => $stock])->one();
        if(!empty($data)){
            return json_encode(['code' => 200,'message' => '提交成功']);
        }else{
            return json_encode(['code' => 500,'message' => '请求失败']);
        }


    }



    /**
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if($this->findModel($id)->delete())
        {
            return $this->message("删除成功",$this->redirect(['transaction-pair']));
        }
        else
        {
            return $this->message("删除失败",$this->redirect(['transaction-pair']),'error');
        }
    }

    /**
     * @return string
     */
    public function actionEnable(){
        $request = Yii::$app->request;
        $id      = $request->post('id');
        $status  = $request->post('status');
        $model   = $this->findModel($id);
        $model->enable = $status;

        if($model->save()){
            return json_encode(['code' => 200, 'message' => '操作成功']);
        }else{
            return json_encode(['code' => 201, 'message' => '操作失败']);
        }
    }

    /**
     * Finds the Coins model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Coins the loaded model
     */
    protected function findModel($id)
    {
        if (empty($id)) {
            return new ExchangeCoins();
        }

        if (empty($model = ExchangeCoins::findOne($id))) {
            return new ExchangeCoins();
        }

        return $model;
    }

    public function actionChangeOrder()
    {
        $request = Yii::$app->request;
        $id      = $request->post('id');
        $orders  = $request->post('orders');

        if (empty($orders)) {
            $orders = 0;
        }

        $result = [
            'code'    => 500,
            'message' => '',
        ];

        $coin = ExchangeCoins::find()->where(['id' => $id])->one();
        $coin->listorder = (int)$orders;
        if ($coin->save()) {
            $result['code'] = 200;
        }else{
            $result['message'] = $coin->getErrors();
        }

        return json_encode($result);
    }
}