<?php
/**
 * Created by PhpStorm.
 * User: op
 * Date: 2018-05-30
 * Time: 19:45
 */

namespace api\controllers;

use Yii;
use jinglan\ves\VesRPC;
use common\jinglan\Trade;

class BargainController extends ApibaseController
{
    public $modelClass = '';

    public function init(){
        parent::init();
    }

    public function actionMarket()
    {
        Trade::market();
    }

    public function actionMarketLogin()
    {
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $uinfo = $this->memberToken($access_token);
        $uid = $uinfo['id'];
        Trade::login($uid);
    }

    public function actionBalance()
    {
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        if (!empty($access_token)){
            $uinfo = $this->memberToken($access_token);
            $uid = $uinfo['id'];
        }else{
            $uid = 0;
        }
        Trade::balance_v2($uid);
    }

    //Exchange-CC-04.交易所限价单买卖
    public function actionOrderLimit(){
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $uinfo = $this->memberToken($access_token);
        $uid = $uinfo['id'];
        Trade::order($uid);
    }

    // 撤销委托单
    public function actionCancelOrder()
    {
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $uinfo = $this->memberToken($access_token);
        $uid = $uinfo['id'];
        Trade::cancelOrder($uid);
    }
}