<?php 
/**
* name: xiaocai
* date: 2018-9-6 14:30:00
*/
namespace backend\controllers;

use Yii;
use yii\data\Pagination;
use common\models\BalanceLog;

class BalanceController extends MController
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
        'withdraw'      => '提现转出',
        'withdraw_fee'      => '提现转出手续费',
		'other'    => '<i title="未知类型" class="fa fa-exclamation-triangle text-warning"></i>',
	];

	public function actionIndex()
	{
		// 获取查询条件
		$request  = Yii::$app->request;
		$keyword  = $request->get('keyword')?: '';
		$key_type = $request->get('key_type')?: '';

        $query = BalanceLog::find()->select(['jl_balance_log.*','jl_member.nickname']);
        // 判断搜索条件
        if (!empty($keyword) && !empty($key_type)) {
        	$query->where(['jl_balance_log.'.$key_type => $keyword]);
        }

		$count = $query->count();
		$pagination = new Pagination(['totalCount' => $count]);


		$balance_logs = $query->offset($pagination->offset)
			->limit($pagination->limit)
			->leftJoin('jl_member', 'jl_balance_log.member_id = jl_member.id')
			->asArray()
			->orderBy('ctime DESC')
			->all();

		return $this->render('index',[
			'models'      => $balance_logs,
			'log_type'    => $this->log_type,
			'pagination'  => $pagination,
			'detial_type' => $this->detial_type,
        ]);
	}
}