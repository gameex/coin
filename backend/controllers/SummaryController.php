<?php 

namespace backend\controllers;

use api\models\MemberWallet;
use yii\data\Pagination;
use Yii;
use common\models\PersonalAccount;
use common\models\SysAddr;
use common\jinglan\TransferAccountsHelper;
use common\models\Coins;

class SummaryController extends MController
{
	// 链上资产汇总
	public function actionIndex()
	{
		// 获取查询条件
		$request  = Yii::$app->request;
		$keyword  = $request->get('keyword')?: '';
		$key_type = $request->get('key_type')?: '';

		$query = MemberWallet::find()
			->select(['jl_member_wallets.*', 'jl_member.nickname'])
			->where(['jl_member_wallets.status' => 1])// 启用
			->andWhere(['network' => 0])// 主网
			->andWhere(['>', 'jl_member_wallets.balance', 0])// 资产不为0
			->leftJoin('jl_member', 'jl_member_wallets.uid=jl_member.id');

		// 追加查询条件
		if ($key_type == 'uid' && !empty($keyword)) {
			$query->andWhere(['jl_member.id' => intval($keyword)]);
		}
		if (!empty($keyword) && !empty($key_type) && $key_type != 'uid') {
			$query->andWhere(['jl_member_wallets.'.$key_type => $keyword]);
		}

		$count      = $query->count();
		$pagination = new Pagination(['totalCount' => $count]);

		$member_wallets = $query->offset($pagination->offset)
		    ->limit($pagination->limit)
		    ->orderBy('jl_member_wallets.balance desc')
		    ->asArray()
		    ->all();

		// 获取系统账户和个人账户
		$system_wallet = SysAddr::find()->select(['symbol','addr'])->where(['del_status'=>0])->asArray()->all();
		$system_wallet = array_column($system_wallet, 'addr', 'symbol');
		$personal_wallet = PersonalAccount::find()->select(['symbol','addr'])->where(['del_status'=>0])->asArray()->all();
		$personal_wallet = array_column($personal_wallet, 'addr', 'symbol');

		return $this->render('index', compact('member_wallets','pagination','system_wallet','personal_wallet'));
	}

	// 资产汇总
	public function actionSummaryAssets()
	{
		$result = [
			'code'    => 500,
			'message' => '',
		];
		// 接收参数
		$request = Yii::$app->request;
		$id      = $request->post('id');
		$type    = $request->post('type');
		$symbol  = $request->post('symbol');
        $value_amount  = $request->post('value_amount');
		if (empty($id) || empty($type) || empty($symbol) || empty($value_amount)) {
			$result['message'] = '参数不完整！';
			return json_encode($result);
		}

		// 获取发起方账户信息
		$from_account = MemberWallet::find()
			->where(['id'=>intval($id)])
			->andWhere(['>','balance',0])
			->andWhere(['network' => 0])
			->one();
		if (empty($from_account)) {
			$result['message'] = '查询不到该账户信息！';
			return json_encode($result);
		}

		// 根据汇总类型（system, personal）获取系统账户或者个人账户
		if ($type == 'system') {
			$to = SysAddr::find()->where(['symbol'=>$symbol])->andWhere(['del_status'=>0])->one();
			if (empty($to)) {
				$result['message'] = '该币种类型的系统账户未生成！';
				return json_encode($result);
			}
		}else{
			$to = PersonalAccount::find()->where(['symbol'=>$symbol])->andWhere(['del_status'=>0])->one();
			if (empty($to)) {
				$result['message'] = '该币种类型的个人账户不存在！';
				return json_encode($result);
			}
		}

		// 根据币种类型选择不同的转账方式
		switch ($symbol) {
			case 'ETH':
				// $from, $to, $value, $password, $gas='', $gasPrice=''
				$data = TransferAccountsHelper::eth(
					$from_account->addr,
					$to->addr,
					/*$from_account->balance,*/
                    $value_amount,
					$from_account->uid
				);
				break;
			case 'BTC':
				// $accountName, $to, $value
				$data = TransferAccountsHelper::btc(
					$from_account->id,
					$to->addr,
					/*$from_account->balance*/
                    $value_amount
				);
				break;
			case 'USDT':
				// $accountName, $to, $value
				$data = TransferAccountsHelper::usdt(
					$from_account->id,
					$to->addr,
					/*$from_account->balance*/
                    $value_amount
				);
				break;
			default:
				// 判断是否为代币，代币采用代币汇总方法，否则不支持该币种汇总操作
				$coin_find = Coins::find()->where(['symbol' => $symbol])->one();
				if (!empty($coin_find) && $coin_find->ram_status==1) {
					// 代币
					// 判断是否存在合约地址
					if (empty($coin_find->ram_token_addr)) {
						$result['message'] = '该币种的合约地址不存在！请先完善币种信息';
						return json_encode($result);
					}
					// $from, $to, $value, $password, $contractAddress, $ram_token_decimals, $gas, $gasPrice
					$data = TransferAccountsHelper::token(
						$from_account->addr,
						$to->addr,
						/*$from_account->balance,*/
                        $value_amount,
						$from_account->uid,
						$coin_find->ram_token_addr,
                        $coin_find->ram_token_decimals
					);
				}else{
					// 不支持的币种
					$result['message'] = '不支持该币种汇总！';
					return json_encode($result);
				}
				break;
		}

		// 结果
		if ($data['status']) {
			// 成功
			// 修改账户余额为0
			//$from_account->balance = 0;
            $from_account->balance = $from_account->balance - $value_amount;
			if ($from_account->save()) {
				$result['code'] = 200;
				$result['message'] = '汇总成功！'.$data['data'];
			}else{
				$result['message'] = '汇总之后更新账户余额失败！';
			}
		}else{
			$result['message'] = $data['message'];
		}
		return json_encode($result);
	}
}