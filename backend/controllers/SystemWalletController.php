<?php 
/*
* name: xiaocai
* date: 2018-10-11 10:10
*/

namespace backend\controllers;

use Yii;
use common\models\SysAddr;
use common\models\Coins;
use yii\data\Pagination;
use common\jinglan\CreateWallet;
use common\helpers\StringHelper;
use jinglan\walletapi\WalletRPC;
use Denpa\Bitcoin\Client as BitcoinClient;
use Denpa\Bitcoin\Omnicore as OmnicoreClient;

class SystemWalletController extends MController
{
	public function actionIndex()
	{
  //       $query = Coins::find()
  //           ->select(['jl_coins.symbol', 'jl_coins.id as coin_id', 'jl_sys_addr.id', 'jl_sys_addr.addr', 'jl_sys_addr.balance', 'jl_sys_addr.account_name', 'jl_sys_addr.created_at', 'jl_sys_addr.del_status'])
  //           ->where(['jl_coins.enable' => 1])
  //           ->LeftJoin('jl_sys_addr', 'jl_coins.id=jl_sys_addr.coin_id');

		// $count      = $query->count();
		// $pagination = new Pagination(['totalCount' => $count, 'pageSize' => 10]);

		// $sys_addr = $query->offset($pagination->offset)
		//     ->limit($pagination->limit)
  //           ->asArray()
		//     ->all();

		// return $this->render('index',[
		// 	'sys_wallets' => $sys_addr,
		// 	'pagination'  => $pagination,
		// ]);

        $query = Coins::find()
            ->select(['id as coin_id', 'symbol'])
            ->where(['jl_coins.enable' => 1]);

        $count      = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => 10]);

        $coins = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->asArray()
            ->all();

        $sys_addr = SysAddr::find()
            ->select(['id', 'symbol', 'addr', 'balance', 'account_name', 'created_at'])
            ->where(['del_status' => 0])
            ->asArray()
            ->all();
        $sys_addr = array_column($sys_addr, NULL, 'symbol');


        foreach ($coins as $key => $value) {
            if (isset($sys_addr[$value['symbol']])) {
                $coins[$key] = array_merge($coins[$key], $sys_addr[$value['symbol']]);
            }
        }

        return $this->render('index',[
         'sys_wallets' => $coins,
         'pagination'  => $pagination,
        ]);
	}

	// public function actionEdit()
	// {
	// 	$request = Yii::$app->request;
 //        $id      = $request->get('id');
 //        $model   = $this->findModel($id);

 //        // 获取支持币种
	// 	$coins = Coins::find()->select(['id','symbol'])->where(['enable' => 1])->asArray()->all();
	// 	$coins = array_column($coins, 'symbol', 'id');

 //        if($model->load(Yii::$app->request->post())) {
 //        	$model->symbol = $coins[$model->coin_id];

 //        	if ($id) {
 //        		// 更新
 //        		$model->updated_at = time();
 //        	}else{
 //        		// 新增
 //        		$model->created_at = time();
 //                // 查询该币种系统账户是否存在
 //                $has_sys_wallet = SysAddr::find()
 //                    ->where(['coin_id' => $model->coin_id])
 //                    ->andWhere(['network' => $model->network])
 //                    ->one();
 //                if ($has_sys_wallet) {
 //                    return $this->message("该币种系统账户已存在，请勿重复添加！",$this->redirect(['index']),'error');
 //                }
 //        	}

 //            if ($model->save()) {
 //                return $this->redirect(['index']);
 //            }
 //        }
 //        return $this->render('edit', [
	// 		'model'      => $model,
	// 		'coins_list' => $coins,
 //        ]);
	// }

	public function actionDelete($id)
    {
        $sys_addr = $this->findModel($id);
        $sys_addr->del_status = 1;
        if($sys_addr->save()){
            return $this->message("删除成功",$this->redirect(['index']));
        }else{
            return $this->message("删除失败",$this->redirect(['index']),'error');
        }
    }

    // 自动生成系统账户
    public function actionGenerate($coin_id)
    {
        $password = StringHelper::random(10);
        $coin = Coins::find()->where(['id' => $coin_id])->andWhere(['enable' => 1])->one();
        if ($coin) {
            // 判断如果为代币，生成ETH地址
            if ($coin->ram_status == 1) {
                $result = CreateWallet::generate_addr('ETH', $password);
            }else{
                $result = CreateWallet::generate_addr($coin->symbol, $password);
            }

            if ($result['code'] == 1) {
                // 生成成功
                $sys_addr = new SysAddr();
                // 比特币账户名称特殊存储
                if ($coin->symbol == 'BTC') {
                    $sys_addr->account_name = $result['data']['pwd'];
                }else{
                    $sys_addr->account_name = '系统账户';
                }
                $sys_addr->coin_id      = $coin->id;
                $sys_addr->symbol       = $coin->symbol;
                $sys_addr->addr         = $result['data']['addr'];
                $sys_addr->password     = $result['data']['pwd'];
                $sys_addr->created_at   = time();
                if ($sys_addr->save()) {
                    return $this->message("生成成功",$this->redirect(['index']));
                }
            }else{
                // 生成失败
                return $this->message($result['message'], $this->redirect(['index']),'error');
            }
        }else{
            return $this->message("未找到该币种信息，无法生成系统账户！",$this->redirect(['index']),'error');
        }
    }

    // 更新用户余额
    public function actionUpdateBalance($id)
    {
        $wallet = SysAddr::findOne($id);
        $_POST['chain_network'] = 'main_network';
        if ($wallet) {
            $proto = Yii::$app->config->info('WALLET_API_PROTOCAL');;
            $host = Yii::$app->config->info('WALLET_API_URL');;
            $port = Yii::$app->config->info('WALLET_API_PORT');;
            $_md5_key = Yii::$app->config->info('WALLET_API_KEY');;       
            $rpc = new WalletRPC($proto,$host,$port,$_md5_key );            
            $rpc_ret = $rpc->account_info($wallet->password);
            if($rpc_ret['code'] == 0){
                // 失败
                return $this->message($rpc_ret['data'], $this->redirect(['index']),'error');
            }else{
                // 成功
                $wallet->balance =$rpc_ret['data']['balance'];
                if ($wallet->save()) {
                    return $this->message('已更新为链上最新数据！', $this->redirect(['index']));
                }else{
                    return $this->message('余额存储失败！', $this->redirect(['index']),'error');
                }
            }
        }else{
            return $this->message("未查询到该系统账户信息！",$this->redirect(['index']),'error');
        }
    }



	protected function findModel($id)
    {
        if (empty($id)) {
            return new SysAddr();
        }
        if (empty($model = SysAddr::findOne($id))) {
            return new SysAddr();
        }
        return $model;
    }


}