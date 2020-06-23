<?php
/**
* name: xiaocai
* date: 2018-9-6 14:30:00
*/
namespace backend\controllers;

use Yii;
use yii\data\Pagination;
use common\models\BalanceLog;
use common\models\Coins;

class RechargeController extends MController
{
	// 财务日志类型
	protected $log_type = [
		0  => '<i title="未知类型" class="fa fa-exclamation-triangle text-warning"></i>',
		1  => '充值',
		10 => '转出',
	];

	// 财务日志detail_type
	protected $detial_type = [
		'exchange' => '交易所',
		'chain'    => '链上',
		'system'   => '系统',
		'otc'      => '场外交易',
		'other'    => '<i title="未知类型" class="fa fa-exclamation-triangle text-warning"></i>',
	];

	public function actionIndex()
	{
		// 获取查询条件
		$request  = Yii::$app->request;
		$starttime  = $request->get('starttime')?: '';
		$endtime  = $request->get('endtime')?: '';
		$key_type = $request->get('key_type')?: '';
		$id = $request->get('id')?: '';

		$starttime  = strtotime($starttime);
		$endtime  = strtotime($endtime);

		$query = BalanceLog::find()->select(['jl_balance_log.*','jl_member.nickname']);
		$where[]='and';
		$where[]=array('=','jl_balance_log.type',1);
		if (!empty($starttime)) {
			$where[]=array('>','jl_balance_log.ctime',$starttime);
		}
		if (!empty($endtime)) {
			$where[]=array('<','jl_balance_log.ctime',$endtime);
		}
		if (!empty($key_type)) {
			$where[]=array('=','jl_balance_log.coin_symbol',$key_type);
		}
		if (!empty($id)) {
			$rows = (new \yii\db\Query())
			    ->select(['id'])
			    ->from('jl_member')
			    ->where(['last_member' => $id])
			    ->all();
			$rows = array_column($rows, 'id');
			$where[]=array('in','jl_balance_log.member_id',$rows);
		}

		$query->where($where);
		$count = $query->count();
		$pagination = new Pagination(['totalCount' => $count]);

		$balance_logs = $query->offset($pagination->offset)
			->limit($pagination->limit)
			->leftJoin('jl_member', 'jl_balance_log.member_id = jl_member.id')
			->asArray()
			->orderBy('ctime DESC')
			->all();

        // $sql = $query->createCommand()->getSql();
        // var_dump($sql);die();


		$all_num = BalanceLog::find()->where($where)->select(['sum(jl_balance_log.change) as all_num'])
			->leftJoin('jl_member', 'jl_balance_log.member_id = jl_member.id')
			->asArray()
			->one();
		if (empty($all_num)) {
			$all_num["all_num"] = '0';
		}

		//币种列表
		$where2['enable']=1;
		$symbol = Coins::find()->select(['id','symbol'])->where($where2)->asArray()->all();
		$symbol_list = array();
		foreach ($symbol as $value) {
		   $symbol_list[$value['id']] = $value['symbol'];
		}

		return $this->render('index',[
			'models'      => $balance_logs,
			'log_type'    => $this->log_type,
			'pagination'  => $pagination,
			'detial_type' => $this->detial_type,
			'symbol_list' => $symbol_list,
			'all_num' => $all_num["all_num"],
		]);
	}
}