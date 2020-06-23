<?php 
namespace backend\controllers;

use Yii;
use yii\data\Pagination;
use common\models\PersonalAccount;
use common\models\Coins;

class PersonalWalletController extends MController
{
	public function actionIndex()
	{
		$query = PersonalAccount::find()
			->where(['del_status'=>0]);

		$count = $query->count();
		$pagination = new Pagination(['totalCount' => $count]);
		$personal_account = $query->offset($pagination->offset)
		    ->limit($pagination->limit)
		    ->asArray()
		    ->all();

		return $this->render('index',compact('personal_account','pagination'));
	}

	public function actionEdit()
	{
		$request = Yii::$app->request;
        $id      = $request->get('id');
        $model   = $this->findModel($id);

        // 获取支持币种
		$coins = Coins::find()->select(['id','symbol'])->where(['enable' => 1])->asArray()->all();
		$coins = array_column($coins, 'symbol', 'id');

        if($model->load(Yii::$app->request->post())) {
        	$model->symbol = $coins[$model->coin_id];

        	if ($id) {
        		// 更新
        		$model->updated_at = time();
        	}else{
        		// 新增
        		$model->created_at = time();
                // 查询该币种个人账户是否存在
                $has_sys_wallet = PersonalAccount::find()
                    ->where(['coin_id' => $model->coin_id])
                    ->andWhere(['del_status'=>0])
                    ->one();
                if ($has_sys_wallet) {
                    return $this->message("该币种个人账户已存在，请勿重复添加！",$this->redirect(['index']),'error');
                }
        	}

            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }
        return $this->render('edit', [
			'model'      => $model,
			'coins_list' => $coins,
        ]);
	}

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

    protected function findModel($id)
    {
        if (empty($id)) {
            return new PersonalAccount();
        }
        if (empty($model = PersonalAccount::findOne($id))) {
            return new PersonalAccount();
        }
        return $model;
    }
}