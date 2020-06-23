<?php
/**
 * Created by PhpStorm.
 * User: landehua
 * Date: 2018/5/30 0030
 * Time: 11:22
 */

namespace backend\modules\member\controllers;

use common\models\member\Member;
use common\models\MemberVerified;
use Yii;
use yii\data\Pagination;
use common\models\MemberWealthOrder;
use common\models\MemberWealthPackage;

class AuthController extends UController{
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
                $where = ['like', 'real_name', $keyword];
                break;
            case '2':
                $where = ['like', 'id_number', $keyword];
                break;
            default:
                $where = [];
                break;
        }

        $data = MemberVerified::find()->with('member')->where(['>', 'status', 0])->andWhere($where);
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
        if(empty($id) || !in_array($type, ['fail', 'success'])){
            return json_encode(['code' => 201, 'message' => '缺少参数']);
        }
        if($type == 'fail'){
            $status = 3;
        }else{
            $status = 2;
        }
        $model = MemberVerified::findOne(['id' => $id]);
        if(in_array($model->status, [2,3])){
            return json_encode(['code' => 201, 'message' => '已操作过，不能再进行操作']);
        }
        $uid = $model->uid;
        $model->status = $status;
        $model->audit_time = date('Y-m-d H:i:s');
        if($model->save()){
            if($status == 2) {
                $User = Member::findOne(['id' => $uid]);
                $User->verified_status = 1;
                $User->save();

                //实名认证通过奖励活期余额50HTC
                //活期且已经买过直接加订单数量，写log,返回。定期直接加记录
                // $where = [
                //     'and',
                //     ['=', 'status', 1],
                //     ['=', 'type', 2],
                //     ['=', 'uid', $uid],
                // ];
                // $order_model = MemberWealthOrder::find()->orderBy('id desc')->where($where)->one();
                // $num = 50;
                // $coin_symbol = 'HTC';
                // if (!empty($order_model)){
                //     $release_log_str = $order_model->log.'(时间'.date('Y-m-d H:i:s', time()).',通过实名认证奖励50HTC,奖励后余额'.($order_model->amount + $num).')--';
                //     $order_model->amount = $order_model->amount + 50;
                //     $order_model->log = $release_log_str;
                //     $order_model->save(false);
                // }else{
                //     $package_info = MemberWealthPackage::find()->where(['status'=>1,'type'=>2])->select('id,type,coin_symbol,name,period,min_num,day_profit')->orderBy('ctime asc')->asArray()->one();
                //     if(!empty($package_info)){
                //         //写记录
                //         $tablePrefix = Yii::$app->db->tablePrefix;
                //         Yii::$app->db->createCommand()->insert("{$tablePrefix}member_wealth_order", [
                //             'uid' => $uid,
                //             'type' => $package_info['type'],
                //             'order_id' => 0,
                //             'wealth_pid' => $package_info['id'],
                //             'name' => $package_info['name'],
                //             'period' => $package_info['period'],
                //             'day_profit' => $package_info['day_profit'],
                //             'surplus_period' => $package_info['period'],
                //             'status' => 1,
                //             'amount' => $num ,
                //             'coin_symbol' => $coin_symbol,
                //             'ctime' => time(),
                //             'last_allocation' => time(),
                //             'log' => '(时间'.date('Y-m-d H:i:s', time()).',通过实名认证奖励50HTC,奖励后余额50)--',
                //         ])->execute();
                //         $id = Yii::$app->db->getLastInsertID();
                //     }
                // }

            }
            return json_encode(['code' => 200, 'message' => '操作成功']);
        }else{
            return json_encode(['code' => 201, 'message' => '操作失败']);
        }
    }

    public function actionDelete($id){
        $model = MemberVerified::findOne(['id' => $id]);
        if($model->status != 2) {
            $model->status = 0;
            $model->audit_time = date('Y-m-d H:i:s');
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