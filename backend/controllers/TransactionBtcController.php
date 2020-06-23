<?php

namespace backend\controllers;
use common\models\member\Member;
use Yii;
use common\models\TransactionBtc;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class TransactionBtcController extends MController
{
    protected $TYPE = [
        'prepare' => '<i class="fa fa-spinner fa-pulse text-warning" title="准备中..."></i>',//准备中
        'pending' => '<i class="fa fa-spinner fa-pulse text-warning" title="待确认..."></i>',//待确认
        'success' => '<i class="fa fa-check text-info" title="交易成功"></i>',//成功
        'fail' => '<i class="fa fa-close text-danger" title="交易失败"></i>',//失败
    ];

    // 交易类型【1:钱包转账交易 2:存入银行 3:取出银行 4:场外交易 5:其他】
    protected $transaction_type = ['其他', '转账', '充值', '提现', '场外交易'];

    public function actionIndex()
    {
    	// 设置查询类型映射：0=>用户id，1=>发送方地址，2=>接收方地址

        // 获取查询条件
        $request = Yii::$app->request;
        $keyword = $request->get('keyword');
        $key_type = $request->get('key_type');
        $starttime  = $request->get('starttime')?: '';
        $endtime  = $request->get('endtime')?: '';

        $starttime  = strtotime($starttime);
        $endtime  = strtotime($endtime);

        // 判断是否追加查询条件
        $db = new \yii\db\Query;
        $data = $db->select('t.id,t.type,t.member_id,t.tx_hash,t.from,t.to,t.coin_symbol,t.value_dec,t.tx_status,t.block,t.updated_at,t.rpc_response,m.nickname')->from('jl_transaction_btc t');
        $where[]='and';
        $where[]=array('=','t.type',1);
        if (!empty($starttime)) {
            $where[]=array('>','t.updated_at',$starttime);
        }
        if (!empty($endtime)) {
            $where[]=array('<','t.updated_at',$endtime);
        }

        if (!empty($keyword)) {
            if($key_type == 0){
                $where[]=array('=','t.member_id',$keyword);
            }elseif($key_type == 1){
                $where[]=array('=','t.from',$keyword);
            }else{
                $where[]=array('=','t.to',$keyword);
            }
        }

        $data->where($where);
        $count = $data->count();
        $pagination = new Pagination(['totalCount' => $count]);


        
        $models = $data->join('left join', 'jl_member m', 't.member_id=m.id')
                       ->offset($pagination->offset)
                       ->orderBy('t.id desc')
                       ->limit($pagination->limit)
                       ->all();
        return $this->render('index', [
            'models'           => $models,
            'Pagination'       => $pagination,
            'type'             => $this->TYPE,
            'keyword'          => $keyword,
            'key_type'         => $key_type,
            'transaction_type' => $this->transaction_type,
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

    protected function findModel($id)
    {
        if (empty($id)) {
            return new TransactionBtc();
        }

        if (empty($model = TransactionBtc::findOne($id))) {
            return new TransactionBtc();
        }

        return $model;
    }

}
