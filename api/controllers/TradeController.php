<?php
/**
 * Created by PhpStorm.
 * User: op
 * Date: 2018-05-29
 * Time: 19:26
 */

namespace api\controllers;

use Yii;
use jinglan\ves\VesRPC;
use common\jinglan\Choose;
use api\models\CoinChoose;



class TradeController extends ApibaseController
{
    public $modelClass = '';

    public function init(){
        parent::init();
    }

    /**
     * 根据用户交易记录
     */
    public function actionRecord(){
        $request = Yii::$app->request;
        $language = $request->post('language');
        $page = $request->post('page');
        $page = empty($page) ? 1 : $page;
        $language = $language == 'en_us' ? 'usd' : 'cny';
        $s = $language == 'en_us' ? '$' : '￥';
        $this->error_message('_Coming_Soon_');
    }

    public function actionTradeAdd(){
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $os = strtolower($request->post('os'));
        if (in_array($os, ['ios','android'])){
            $uinfo = $this->checkToken($access_token);
        }else{
            $uinfo = $this->memberToken($access_token);
        }
        $uid = $uinfo['id'];
        $money = $request->post('money');
        $stock = $request->post('stock');
        $this->check_empty($money,'_NOT_Empty_');
        $this->check_empty($stock,'_NOT_Empty_');
        $models = CoinChoose::findOne(['stock'=>$stock,'money'=>$money,'uid'=>$uid]);
        if(empty($models)){
            $choose = new CoinChoose();
            $choose->uid = $uid;
            $choose->stock = $stock;
            $choose->money = $money;
            $choose->status = 1;
            if ($choose->save()>0) {
                $this->success_message();
            }else{
                $this->error_message('_Add_Failure_');
            }
        }else{
            $models->status = 1;
            if ($models->save()>0) {
                $this->success_message();
            }else{
                $this->error_message('_Add_Failure_');
            }
        }
    }

    public function actionTradeFind(){
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $os = strtolower($request->post('os'));
        if (in_array($os, ['ios','android'])){
            $uinfo = $this->checkToken($access_token);
        }else{
            $uinfo = $this->memberToken($access_token);
        }
        $uid = $uinfo['id'];
        Choose::find($uid);
    }

    public function actionTradeDelete(){
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $os = strtolower($request->post('os'));
        if (in_array($os, ['ios','android'])){
            $uinfo = $this->checkToken($access_token);
        }else{
            $uinfo = $this->memberToken($access_token);
        }
        $uid = $uinfo['id'];
        $money = $request->post('money');
        $stock = $request->post('stock');
        $this->check_empty($money,'_NOT_Empty_');
        $this->check_empty($stock,'_NOT_Empty_');
        $models = CoinChoose::findOne(['stock'=>$stock,'money'=>$money,'uid'=>$uid]);
        if(empty($models)){
            $this->error_message('_No_Data_Query_');
        }else{
            $models->status = 0;
            if ($models->save()>0) {
                $this->success_message();
            }else{
                $this->error_message('_Add_Failure_');
            }
        }
    }

}
















