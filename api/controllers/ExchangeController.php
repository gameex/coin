<?php
/**
 * Created by PhpStorm.
 * User: op
 * Date: 2018-05-30
 * Time: 19:45
 */

namespace api\controllers;

use Yii;
use common\jinglan\Trade;
use common\jinglan\CreateWallet;

class ExchangeController extends ApibaseController
{
    public $modelClass = '';

    public function init(){
        parent::init();
    }

    public function actionMarket()
    {
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $os = strtolower($request->post('os'));
        if (!empty($access_token)){
            $uinfo = $os == 'web' ? $this->memberToken($access_token) : $this->checkToken($access_token);
            $uid = $uinfo['id'];
        }else{
            $uid = 0;
        }
        Trade::market_v3($uid);
    }

    public function actionBalance()
    {
        $request = Yii::$app->request;

        $access_token = $request->post('access_token');
        if (!empty($access_token)){
            $uinfo = $this->checkToken($access_token);
            $uid = $uinfo['id'];
        }else{
            $uid = 0;
        }
        Trade::balance_v2($uid);
        //Trade::balance($uid);
    }

    //Exchange-CC-04.交易所限价单买卖
    public function actionOrderLimit(){
        $request = Yii::$app->request;

        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);
        $uid = $uinfo['id'];

        Trade::order($uid);
    }

    // 撤销委托单
    public function actionCancelOrder()
    {
        $request = Yii::$app->request;

        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);
        $uid = $uinfo['id'];

        Trade::cancelOrder($uid);
    }

    // 生成货币地址
    public function actionGenerateAddress()
    {
        $request = Yii::$app->request;

        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);
        $uid = $uinfo['id'];

        CreateWallet::create_v2($uid);
    }
}