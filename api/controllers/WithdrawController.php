<?php 
/**
* name: xiaocai
* date: 2018-08-30 16:00:00
*/
namespace api\controllers;

use Yii;
use api\models\MemberVerified;
use common\jinglan\WithdrawCash;
use common\jinglan\CreateWallet;

class WithdrawController extends ApibaseController
{
	public $modelClass = '';

    public function init(){
        parent::init();
    }

    // 银行提现【转出】
    public function actionTurnOut(){
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $uinfo = $this->memberToken($access_token);

        WithdrawCash::turnOut($uinfo['id']);

    }

    // 银行交易明细
    public function actionRechargeDetails()
    {
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $uinfo = $this->memberToken($access_token);

        WithdrawCash::financialLog($uinfo['id']);
    }

    // 获取币种列表
    public function actionCoinList()
    {
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $os = strtolower($request->post('os'));
        if (in_array($os, ['ios','android'])){
            $uinfo = $this->checkToken($access_token);
        }else{
            $uinfo = $this->memberToken($access_token);
        }

        WithdrawCash::coinList($uinfo['id']);
    }

    // 获取用户提现申请记录
    public function actionApplyLog()
    {
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $uinfo = $this->memberToken($access_token);

        WithdrawCash::applyLog($uinfo['id']);
    }

    // 撤销提现申请
    public function actionRevokeApply()
    {
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $uinfo = $this->memberToken($access_token);

        WithdrawCash::revokeApply($uinfo['id']);
    }

    // 生成币种地址
    public function actionGenerateAddress()
    {
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $uinfo = $this->memberToken($access_token);

        CreateWallet::create_v2($uinfo['id']);
    }

    // 添加提现地址
    public function actionAddAddress()
    {
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $os = strtolower($request->post('os'));
        if (in_array($os, ['ios','android'])){
            $uinfo = $this->checkToken($access_token);
        }else{
            $uinfo = $this->memberToken($access_token);
        }

        WithdrawCash::addAddress($uinfo['id']);
    }

    // 删除提现地址
    public function actionDelAddress()
    {
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $os = strtolower($request->post('os'));
        if (in_array($os, ['ios','android'])){
            $uinfo = $this->checkToken($access_token);
        }else{
            $uinfo = $this->memberToken($access_token);
        }
        WithdrawCash::delAddress($uinfo['id']);
    }

    // 编辑提现地址
    public function actionEditAddress()
    {
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $os = strtolower($request->post('os'));
        if (in_array($os, ['ios','android'])){
            $uinfo = $this->checkToken($access_token);
        }else{
            $uinfo = $this->memberToken($access_token);
        }

        WithdrawCash::addAddress($uinfo['id']);
    }

    // 查询提现地址
    public function actionGetAddress()
    {
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $os = strtolower($request->post('os'));
        if (in_array($os, ['ios','android'])){
            $uinfo = $this->checkToken($access_token);
        }else{
            $uinfo = $this->memberToken($access_token);
        }

        WithdrawCash::getAddress($uinfo['id']);
    }
}