<?php
namespace frontend\controllers;

use Yii;
use common\jinglan\Trade;
use common\models\base\AccessToken;
use common\models\WithdrawApply;
use api\controllers\ApibaseController;
use api\models\Member;
use api\models\MemberWallet;
use common\jinglan\CreateWallet;
use common\jinglan\Bank;
use api\models\BalanceLog;
//手机模板
class WapController extends IController
{

    // 不显示头尾
    public $layout = false;
    /**
     * 系统首页
     * @return string
     */

    //检测token
    public function checkToken($access_token){
        $access_token_info = AccessToken::findIdentityByAccessToken($access_token);
        if (empty($access_token_info)){
            $access_token_info = Member::findAccessToken($access_token);
            if (empty($access_token_info)){
                return $this->render('guoqi');
                die();
            }else{
                $user_id = $access_token_info->attributes['id'];
                $uinfo['uid'] = $user_id;
                $uinfo['access_token'] = $access_token;
                return $uinfo;
            }
        }else{
            $user_id = $access_token_info->attributes['user_id'];
            $uinfo['uid'] = $user_id;
            $uinfo['access_token'] = $access_token;
            return $uinfo;
        }
    }

    //检测PC Token
    public function memberToken($access_token){
        $this->check_empty($access_token,'_User_Information_Anomaly_');
        $access_token_info = Member::findAccessToken($access_token);
        if(empty($access_token_info)){
            // $this->error_message('token值输入错误');
            $actions = Yii::$app->params['user.optional'];
            if (in_array($this->action->id, $actions)) {
                return ['id'=>0];
            }
            $result = array('code'=>501, 'message'=>'Unauthorized', 'token_status'=>0);
            $this->do_aes(json_encode($result));
        }else{
            $user_id = $access_token_info->attributes['id'];
            $uinfo = $this->getUserInfoById($user_id);
            $uinfo['access_token'] = $access_token;
            return $uinfo;
        }
    }



    //普通错误信息,客户端直接提示即可,客户端不需要对此状态吗做特殊处理
    protected function error_message($descrp='_Information_Wrong_'){
        $language = Yii::$app->request->post('language') == 'en_us'?'en_us':'zh_cn';
        if(is_string($descrp)){
            $ret = array('code'=>501,'message'=>Yii::t($language,$descrp));
        }else{
            $ret = array('code'=>501,'message'=>$descrp);
        }
        die(json_encode($ret));
    }

    //普通成功信息,统一格式
    protected function success_message($data='',$descrp = '_Submission_Success_'){
        $language = Yii::$app->request->post('language') == 'en_us'?'en_us':'zh_cn';
        if (empty($data)) {
            $ret = array('code'=>200,'message'=>Yii::t($language,$descrp));
        }else{
            $ret = array('code'=>200,'data'=>$data,'message'=>Yii::t($language,$descrp));
        }
        die(json_encode($ret));
    }



    public function actionIndex()
    {
        $request = Yii::$app->request;
        $access_token = $request->get('access_token');
        $subtype = $request->get('subtype');
        if (empty($access_token) || empty($subtype)){
            return $this->render('guoqi');
            die();
        }

        $uinfo = $this->checkToken($access_token);



        if ($subtype == "RCNY"){
            return $this->render('wkt');
            die();
        }
        
        if ($subtype == "ITT"){
            return $this->render('itt');
            die();
        }
        die();

//var_dump($uinfo);

        $_POST['chain_network'] = 'main_network';
        $_POST['return_way'] = 'array';
        $balance_all = Trade::balance_v2($uinfo['uid']);// 成功返回数据，失败返回false
//var_dump($balance_all);

        $uinfo['available_balance'] = 0;
        foreach($balance_all[0] as $v){
            if ($v['name'] == $subtype) {
                $uinfo['available_balance'] = $v['available'];
            }
        }

        return $this->render('index', [
                    'uinfo'  => $uinfo,
                  ]);
    }

    public function actionNotopen()
    {
        return $this->render('notopen');
    }

    //提现记录
    public function actionWithdraw()
    {
        // 提现状态【1：待审核、2：审核通过、3：未通过、4：提现失败、5：链上待确认】
        $apply_status = ['默认','待审核', '提现成功', '未通过', '提现失败', '提现成功'];

        // 获取Token
        $request = Yii::$app->request;
        $token   = $request->get('access_token');
        $access_token_info = AccessToken::findIdentityByAccessToken($token);
        if (empty($access_token_info)){
            // token无效
            $withdraw_apply = [];
        }else{
            $user_id = $access_token_info->attributes['user_id'];
            $withdraw_apply = WithdrawApply::find()
                ->where(['member_id' => intval($user_id)])
                ->andWhere(['type' => 1])
                ->all();
        }
        
        return $this->render('withdraw', [
            'withdraw_apply' => $withdraw_apply,
            'apply_status'   => $apply_status,
        ]);
    }



    //参数不能为空
    protected function check_empty($input,$descrp='_NOT_Empty_'){
        $language = Yii::$app->request->post('language') == 'en_us'?'en_us':'zh_cn';
        if (empty($input)) {
            if(is_string($descrp)){
                $ret = array('code'=>500,'message'=>Yii::t($language,$descrp));
            }else{
                $ret = array('code'=>500,'message'=>$descrp);
            }
            die(json_encode($ret));
        }
    }


    public function curl($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//禁止调用时就输出获取到的数据
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function actionInvite_friends(){
        $request = Yii::$app->request;
        $access_token = $request->get('access_token');
        if (empty($access_token)){
            return $this->render('guoqi');
            die();
        }
        $uinfo = $this->checkToken($access_token);
        return $this->render('invite_friends', [
            'token' => $access_token,
        ]);
    }

    public function actionWealthlock(){
        $request = Yii::$app->request;
        $access_token = $request->get('access_token');
        if (empty($access_token)){
            return $this->render('guoqi');
            die();
        }
        $uinfo = $this->checkToken($access_token);
        return $this->render('wealthlock', [
            'token' => $access_token,
        ]);
    }

    public function actionWealthbuy(){
        $request = Yii::$app->request;
        $access_token = $request->get('access_token');
        if (empty($access_token)){
            return $this->render('guoqi');
            die();
        }
        $uinfo = $this->checkToken($access_token);

        $request  = Yii::$app->request;
        $id       = $request->get('id');
        if(empty($id)){
          die("该商品已经售完");
        }
        $data = (new \yii\db\Query())->from('jl_member_wealth_package')->where(['status' => 1,'id'=>$id])->one();
        if(empty($data)){
          die("该商品已经售完");
        }
        $data['min_num'] = sprintf('%.2f',$data['min_num']);

        return $this->render('wealthbuy', [
            'token' => $access_token,
            'wealtdetail'=>$data,
        ]);
    }

    public function actionGoogle_code_send()
    {
        // 提现状态【1：待审核、2：审核通过、3：未通过、4：提现失败、5：链上待确认】
        $apply_status = ['默认','待审核', '审核通过', '未通过', '提现失败', '链上待确认'];

        // 获取Token
        $request = Yii::$app->request;
        $token   = $request->get('access_token');
        $access_token_info = AccessToken::findIdentityByAccessToken($token);

        if (empty($access_token_info)){
            // token无效
            $withdraw_apply = [];
        }else{
            $user_id = $access_token_info->attributes['user_id'];
            $withdraw_apply = WithdrawApply::find()
                ->andWhere(['type' => 1])
                ->all();
        }
        
        return $this->render('googlecode', [
            'withdraw_apply' => $withdraw_apply,
            'apply_status'   => $apply_status,
        ]);

    }

    public function actionGoogle_code_show()
    {
        // 提现状态【1：待审核、2：审核通过、3：未通过、4：提现失败、5：链上待确认】
        $apply_status = ['默认','待审核', '审核通过', '未通过', '提现失败', '链上待确认'];

        // 获取Token
        $request = Yii::$app->request;
        $token   = $request->get('access_token');
        $access_token_info = AccessToken::findIdentityByAccessToken($token);
//var_dump($access_token_info);

        if (empty($access_token_info)){
            // token无效
            $withdraw_apply = [];
        }else{
            $user_id = $access_token_info->attributes['user_id'];
            $withdraw_apply = WithdrawApply::find()
                ->andWhere(['type' => 1])
                ->all();
        }
        
        return $this->render('googlecode', [
            'withdraw_apply' => $withdraw_apply,
            'apply_status'   => $apply_status,
        ]);

    }

    public function actionGoogle_code_open()
    {
        //确认开启谷歌认证
        $apply_status = ['默认','待审核', '审核通过', '未通过', '提现失败', '链上待确认'];

        // 获取Token
        $request = Yii::$app->request;
        $token   = $request->get('access_token');
        $access_token_info = AccessToken::findIdentityByAccessToken($token);

        $is_google_check = Member::find()->select('is_google_check')->where(['id'=>$access_token_info->user_id])->asArray()->one();

        if ($is_google_check["is_google_check"] == 1) {
            return $this->render('google_code_close', [
                'access_token_info' => $access_token_info,
            ]);
        }else{
            return $this->render('google_code_open', [
                'access_token_info' => $access_token_info,
            ]);   
        }
    }


    public function actionGoogle_code_help()
    {
        //确认开启谷歌认证
        $apply_status = ['默认','待审核', '审核通过', '未通过', '提现失败', '链上待确认'];

        // 获取Token
        $request = Yii::$app->request;
        $token   = $request->get('access_token');
        $access_token_info = AccessToken::findIdentityByAccessToken($token);

        $is_google_check = Member::find()->select('is_google_check')->where(['id'=>$access_token_info->user_id])->asArray()->one();

        if ($is_google_check["is_google_check"] == 1) {
            return $this->render('google_code_close', [
                'access_token_info' => $access_token_info,
            ]);
        }else{
            return $this->render('google_code_open', [
                'access_token_info' => $access_token_info,
            ]);   
        }
    }

    public function actionShare_register()
    {
        return $this->render('share_register');
    }





}
