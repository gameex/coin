<?php
/**
 * Created by PhpStorm.
 * User: landehua
 * Date: 2018/6/4 0004
 * Time: 15:59
 */

namespace backend\controllers;

use common\models\member\Member;
use Yii;
use common\models\Message;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class MessageController extends MController{
    public $TYPE = [
        0 => '公告',
        1 => '单个用户消息'
    ];
    public function actionIndex(){

        $data = Message::find()->with('user')->where(['status' => 1]);
        $pages  = new Pagination(['totalCount' =>$data->count(), 'pageSize' =>$this->_pageSize]);
        $models = $data->offset($pages->offset)
            ->orderBy('id desc')
            ->limit($pages->limit)
            ->all();
        return $this->render('index', [
            'models' => $models,
            'Pagination' => $pages,
            'type' => $this->TYPE,
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

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = 0;
        if($model->save())
        {
            return $this->message("删除成功",$this->redirect(['index']));
        }
        else
        {
            return $this->message("删除失败",$this->redirect(['index']),'error');
        }
    }


    public function actionGetUsers(){
        $type = Yii::$app->request->post('type');
        if($type == 0){
            return json_encode([0 => '所有用户']);
        }else{
            $data = Member::find()->select('id,nickname')->asArray()->all();
            $data = ArrayHelper::map($data, 'id', 'nickname');
            return json_encode($data);
        }
    }

    /**
     * Finds the Coins model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Message the loaded model
     */
    protected function findModel($id)
    {
        if (empty($id)) {
            return new Message();
        }

        if (empty($model = Message::findOne($id))) {
            return new Message();
        }

        return $model;
    }
}