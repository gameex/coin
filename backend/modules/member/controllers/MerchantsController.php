<?php
/**
 * Created by PhpStorm.
 * User: landehua
 * Date: 2018/5/30 0030
 * Time: 11:22
 */

namespace backend\modules\member\controllers;

use common\models\member\Member;
use common\models\Merchants;
use Yii;
use yii\data\Pagination;

class MerchantsController extends UController{
    public $STATUS = [
        0 => '已删除',
        1 => '待审核',
        2 => '认证通过',
        3 => '认证失败',
    ];
    public $STATUS_COLOR = [
        0 => '#aaa',
        1 => '#f7a54a',
        2 => '#1ab394',
        3 => 'red',
    ];
    public function actionIndex(){

        $request  = Yii::$app->request;
        $type     = $request->get('type',1);
        $keyword  = $request->get('keyword','');

        switch ($type) {
            case '1':
                $where = ['like', 'id', $keyword];
                break;
            case '2':
                $where = ['like', 'describe', $keyword];
                break;
            default:
                $where = [];
                break;
        }

        $data = Merchants::find()->with('member')->where(['>', 'status', 0])->andWhere($where);
        // p($data);exit;
        $pages  = new Pagination(['totalCount' =>$data->count(), 'pageSize' =>$this->_pageSize]);
        $models = $data->offset($pages->offset)
            ->orderBy('id DESC')
            ->limit($pages->limit)
            ->all();

        return $this->render('index',[
            'models'  => $models,
            'Pagination' => $pages,
            'type'    => $type,
            'keyword' => $keyword,
            'status'  => $this->STATUS,
            'status_color' => $this->STATUS_COLOR
        ]);
    }

    public function actionExamine(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        $type = $request->post('type');
        // p($type);exit;
        if(empty($id) || !in_array($type, ['fail', 'success'])){
            return json_encode(['code' => 201, 'message' => '缺少参数']);
        }
        if($type == 'fail'){
            $status = 3;
        }else{
            $status = 2;
        }
        $model = Merchants::findOne(['id' => $id]);
        if(in_array($model->status, [2,3])){
            return json_encode(['code' => 201, 'message' => '已操作过，不能再进行操作']);
        }
        $uid = $model->uid;
        $model->status = $status;
        $model->updated_at = date('Y-m-d H:i:s');
        if($model->save()){
            if($status == 2) {
                $User = Member::findOne(['id' => $uid]);
                $User->otc_merchant = 1;
                $User->save();
            }
            return json_encode(['code' => 200, 'message' => '操作成功']);
        }else{
            return json_encode(['code' => 201, 'message' => '操作失败']);
        }
    }

    public function actionDelete($id){
        $model = Merchants::findOne(['id' => $id]);
        if($model->status != 2) {
            $model->status = 0;
            $model->updated_at = date('Y-m-d H:i:s');
            if ($model->save()) {
                return $this->message("删除成功", $this->redirect(['index']));
            } else {
                return $this->message("删除失败", $this->redirect(['index']), 'error');
            }
        }else{
            return $this->message("已认证通过，不能删除", $this->redirect(['index']), 'error');
        }
    }
}