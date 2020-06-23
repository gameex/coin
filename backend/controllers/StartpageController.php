<?php

namespace backend\controllers;

use Yii;
use common\models\StartPage;
use yii\web\NotFoundHttpException;


class StartpageController extends MController{
    // public $TYPE = [
    //     0 => 'Android',
    //     1 => 'Ios'
    // ];
    public $STATUS = [
        0 => '禁用',
        1 => '启用'
    ];
    public $TYPE = [
        1 => '中文Banner',
        2 => '英文Banner',
    ];
    public $STATUS_COLOR = [
        0 => 'red',
        1 => '#1ab394',
    ];
    public function actionIndex(){

        $data = StartPage::find()->all();
        return $this->render('index', [
            'data' => $data,
            'status'  => $this->STATUS,
            'type'  => $this->TYPE,
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
        $id      = $request->get('id');
        $model   = $this->findModel($id);
        if($model->load(Yii::$app->request->post())) {
            $model->add_time = time();
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }
        return $this->render('edit', [
            'model' => $model,
            'type'  => $this->TYPE,
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
            return new StartPage();
        }

        if (empty($model = StartPage::findOne($id))) {
            return new StartPage();
        }

        return $model;
    }
}
