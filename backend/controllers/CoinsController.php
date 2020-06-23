<?php

namespace backend\controllers;

use Yii;
use common\models\Coins;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;


class CoinsController extends MController{
    public $STATUS = [
        0 => '禁用',
        1 => '启用'
    ];
    public $ram_status = [
        0 => '正常币种',
        1 => '衍生代币'
    ];
    public $STATUS_COLOR = [
        0 => 'red',
        1 => '#1ab394',
    ];
    public function actionIndex(){
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
        $models = Coins::find()->with('parent')->orderby('jl_coins.listorder');

        $pages = new Pagination(['totalCount' =>$models->count(), 'pageSize' =>$this->_pageSize]);
        $data = $models->offset($pages->offset)->limit($pages->limit)->andWhere($where)->all();

        $symbol = Coins::find()->all();
        return $this->render('index', [
            'Pagination' => $pages,
            'data' => $data,
            'status'  => $this->STATUS,
            'status_color' => $this->STATUS_COLOR,
            'ram_status' => $this->ram_status,
            'type' => $type,
            'keyword' => $keyword,
            'symbol' => $symbol,
        ]);
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
     * @return string|\yii\web\Response
     */
    public function actionEdit(){
        $request = Yii::$app->request;
        $id      = $request->get('id');
        $model   = $this->findModel($id);
        $host = Yii::$app->request->hostInfo;

        if($model->load(Yii::$app->request->post())) {
            // 禁止添加重复币种
            if (empty($id)) {
                $coins = Coins::find()->select(['symbol'])->all();
                if ($coins) {
                    $coins = array_column($coins, 'symbol');
                }else{
                    $coins = [];
                }
                if (in_array(strtoupper($model->symbol), $coins)) {
                    return $this->message("该币种类型已经添加，请勿重复添加！",$this->redirect(['index']),'error');
                }
            } 

            $model->enable    = 1;
            $model->parent_id = 0;
            $model->symbol    = strtoupper($model->symbol);
            $model->ram_token_addr = strtolower($model->ram_token_addr);
            // $symbol      = $request->get('symbol');
            // if((isset($id) && $id < 3) || $symbol == 'ETH' || $symbol == 'BTC'){
            //     return $this->message("不能修改默认或系统冲突",$this->redirect(['index']),'error');
            // }

            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }
        return $this->render('edit', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Coins model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
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
     * Finds the Coins model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Coins the loaded model
     */
    protected function findModel($id)
    {
        if (empty($id)) {
            return new Coins();
        }

        if (empty($model = Coins::findOne($id))) {
            return new Coins();
        }

        return $model;
    }

    // 更新排序字段
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

        $coin = Coins::find()->where(['id' => $id])->one();
        $coin->listorder = (int)$orders;
        if ($coin->save()) {
            $result['code'] = 200;
        }else{
            $result['message'] = $coin->getErrors();
        }

        return json_encode($result);
    }

    // 更新汇率字段
    public function actionChangeRate()
    {
        $request = Yii::$app->request;
        $id      = $request->post('id');
        $rate    = $request->post('rate');
        $type    = $request->post('type');

        if (empty($rate)) {
            $rate = 0;
        }

        $result = [
            'code'    => 500,
            'message' => '',
        ];

        $coin = Coins::find()->where(['id' => $id])->one();
        if ($type == 'usd') {
            $coin->usd = floatval($rate);
        }else{
            $coin->cny = floatval($rate);
        }
        if ($coin->save()) {
            $result['code'] = 200;
        }else{
            $result['message'] = $coin->getErrors();
        }

        return json_encode($result);
    }
}
