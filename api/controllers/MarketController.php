<?php
namespace api\controllers;

use Yii;
use api\models\Coin;
use api\models\ExchangeRate;

class MarketController extends ApibaseController{
    public $modelClass = '';

	public function init(){
		parent::init();
	}


	//获取行情
	public function actionTicker(){
		$request = Yii::$app->request;
		$language = $request->post('language');
		$select = $language == 'en_us' ? 'usd' : 'cny';
		$coins = Coin::find()->where(['check_rate'=>1])->select('symbol,usd,cny')->asArray()->all();

        //获取币种汇率当天第一笔更新
        $symbol = array_column($coins,'symbol');

        $exchange_rate = ExchangeRate::find()->where(['in','coin_symbol',$symbol])->andWhere(['>=','created_at',strtotime(date('Y-m-d'))])->groupBy('coin_symbol')->orderBy('id ASC')->asArray()->all();
        //p($exchange_rate->createCommand()->getRawSql());

        $exchange_rate = array_column($exchange_rate,NULL,'coin_symbol');

        foreach($coins as &$v){
            $first_usd = $v['usd'] - $exchange_rate[$v['symbol']]['usd'];
            $v['status'] = $first_usd < 0 ? '-' : '+';
            $v['rate'] = $v['status'] . sprintf('%0.2f',abs($first_usd) / $exchange_rate[$v['symbol']]['usd'] * 100) . '%';
        }


		if ($coins) {
			$this->success_message($coins,'_Success_');
		}else{
			$this->error_message('_No_Data_Query_');
		}
	}
}
