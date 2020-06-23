<?php

namespace backend\controllers;

use Yii;
use common\models\Robot;
use api\models\ExchangeCoins;
use yii\web\NotFoundHttpException;


class RobotController extends MController{

    public $STATUS = [
        0 => '关闭交易',
        1 => '开启交易'
    ];

    public $STATUS_COLOR = [
        0 => 'red',
        1 => '#1ab394',
    ];
    
    public function actionIndex(){
        $data = Robot::find()->joinwith('exchangeCoins')->all();
        foreach ($data as &$v){
          $v['intime'] = $this->time2string($v['intime']);
        }
        return $this->render('index', [
            'data' => $data,
            'status'  => $this->STATUS,
            'status_color' => $this->STATUS_COLOR
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
        $model->status = $status;
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
        $id  =  htmlspecialchars($request->get('id'));
        $model   = $this->findModel($id);
        if($model->load(Yii::$app->request->post())) {
            // $market_id = $_POST['ExchangeCoins']['id'];
            // p($model);exit();
            // p($model);exit();

            // $model->market_id = $id;
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

            return new Robot();
        }

        if (empty($model = Robot::findOne($id))) {
            return new Robot();
        }

        return $model;
    }

    /**
     * 处理时间函数
     * @param $second
     * @return string
     */
    function time2string($second){
        $day = floor($second/(3600*24));
        $second = $second%(3600*24);
        $hour = floor($second/3600);
        $second = $second%3600;
        $minute = floor($second/60);
        $second = $second%60;
        if(strlen($second) == 1){
            $second = '0'.$second;
        }
        if($day == 0 && $hour == 0 && $minute == 0){
            return $second.'秒';
        }elseif($day == 0 && $hour == 0 && $minute != 0 && $second == 0){
            return $minute.'分钟';
        }elseif( $day == 0 && $hour == 0 ){
            return $minute.'分钟'.$second.'秒';
        }elseif( $day == 0){
            return $hour.'小时'.$minute.'分钟'.$second.'秒';
        }else{
            return $day.'天'.$hour.'小时'.$minute.'分钟'.$second.'秒';
        }

    }
}
