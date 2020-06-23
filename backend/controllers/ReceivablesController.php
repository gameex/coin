<?php
/**
 * Created by PhpStorm.
 * User: landehua
 * Date: 2018/6/4 0004
 * Time: 10:29
 */

namespace backend\controllers;

use common\models\MemberProceeds;
use Yii;
use yii\data\Pagination;

class ReceivablesController extends MController{
    public $PROCEEDS_TYPE = [
        'alipay' => '支付宝',
        'wxpay'  => '微信',
        'bank'   => '银行卡',
        'gongbank' => '中国工商银行',
        'jianbank' => '中国建设银行',
        'zhaobank' => '招商银行',
    ];
    public function actionIndex(){

        $request  = Yii::$app->request;
        $type     = $request->get('type',1);
        $keyword  = $request->get('keyword','');

        $where = [];
        if(!empty($keyword)) {
            switch ($type) {
                case '1':
                    $where = ['like', 'proceeds_type', $keyword];
                    break;
                case '2':
                    $where = ['like', 'account', $keyword];
                    break;
                case '3':
                    $where = ['like', 'bank_name', $keyword];
                    break;
                default:
                    $where = [];
                    break;
            }
        }
        $data = $type == 3?$data = MemberProceeds::find()->with('user')->where(['is_delete' => 0,'proceeds_type'=>'bank'])->andWhere($where):$data = MemberProceeds::find()->with('user')->where(['is_delete' => 0])->andWhere($where);

        $pages  = new Pagination(['totalCount' =>$data->count(), 'pageSize' =>$this->_pageSize]);
        $models = $data->offset($pages->offset)
            ->orderBy(['member_id' => SORT_ASC, 'id' => SORT_ASC])
            ->limit($pages->limit)
            ->all();
        return $this->render('index',[
            'models'  => $models,
            'pages'   => $pages,
            'type'    => $type,
            'keyword' => $keyword,
            'proceeds_type'    => $this->PROCEEDS_TYPE
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function actionDelete($id){
        $model = $this->findModel($id);
        $model->is_delete = 1;
        if($model->save()) {
            return $this->message("删除成功",$this->redirect(['index']));
        } else {
            return $this->message("删除失败",$this->redirect(['index']),'error');
        }
    }

    /**
     * 返回模型
     *
     * @param $id
     * @return MemberProceeds|null|static
     */
    protected function findModel($id)
    {
        if (empty($id)) {
            return new MemberProceeds();
        }

        if (empty($model = MemberProceeds::findOne($id))) {
            return new MemberProceeds();
        }

        return $model;
    }
}