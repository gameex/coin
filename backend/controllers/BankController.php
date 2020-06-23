<?php
/**
 * Created by PhpStorm.
 * User: landehua
 * Date: 2018/6/1 0001
 * Time: 19:22
 */

namespace backend\controllers;

use common\models\Bank;
use Yii;

class BankController extends MController{
    public $STATUS = [
        0 => '禁用',
        1 => '启用'
    ];
    public $STATUS_COLOR = [
        0 => 'red',
        1 => '#1ab394',
    ];
    public function actionIndex(){
        $data = Bank::find()->all();
        return $this->render('index',[
            'data' => $data,
            'status'  => $this->STATUS,
            'status_color' => $this->STATUS_COLOR
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
                return $this->redirect(['index']);
            }
        }
        return $this->render('edit', [
            'model' => $model,
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
     * @param $id
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
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
     * 返回模型
     *
     * @param $id
     * @return Bank|null|static
     */
    protected function findModel($id)
    {
        if (empty($id)) {
            return new Bank();
        }

        if (empty($model = Bank::findOne($id))) {
            return new Bank();
        }

        return $model;
    }
}