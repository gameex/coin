<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6
 * Time: 17:07
 */

namespace api\controllers;

use common\jinglan\Jinglan;
use Yii;
use api\models\Member;
use common\models\base\AccessToken;
use common\helpers\StringHelper;
use common\jinglan\CreateWallet;
use api\models\MemberVerified;

class ThirdController extends ApibaseController
{
    public $modelClass = '';
    public function init(){
        parent::init();
    }

    public function actionFindUser(){
        $request = Yii::$app->request;
        $mobile_phone = $request->post('mobile_phone');
        Jinglan::check_mobile_phone($mobile_phone);
        $find_mobile = Member::findOne(['mobile_phone'=>$mobile_phone,'mobile_phone_status'=>1]);
        if ($find_mobile){
            $user_id = $find_mobile->attributes['id'];
            $group = 1;
            $rst = AccessToken::setMemberInfo($group, $user_id);
            $this->success_message(['access_token'=>$rst['access_token']]);
        }else{
            $this->error_message();
        }
    }

    public function actionGetToken(){
        $request = Yii::$app->request;
        $mobile_phone = $request->post('mobile_phone');
        $access_token = $request->post('access_token');
        $this->check_empty($access_token,'_Access_Token_Not_Empty_');
        $this->check_empty($mobile_phone,'_PhoneNum_Not_Empty_');
        $from = $request->post('from');
        if ($from == 'setpassword'){
            $password = $request->post('password');
            $this->check_empty($password,'_The_Password_Can_Not_Be_Empty_');
            $repassword = $request->post('password_confirm');
            $this->check_empty($repassword,'_Confirm_Password_Must_Not_Be_Empty_');
            if($password  !== $repassword){
                $this->error_message('_The_Two_Password_Input_Is_Inconsistent_');
            }
        }

        $member = Member::find()->where(['mobile_phone'=>$mobile_phone,'mobile_phone_status'=>1])->one();
        if($member){
            if ($from == 'token2token'){
                $user_id = $member->attributes['id'];
                $group = 1;
                $rst = AccessToken::setMemberInfo($group, $user_id);

                $uinfo = $this->getUserInfoById($user_id);
                $uinfo['access_token'] = $rst['access_token'];
                CreateWallet::create($user_id);
                $this->success_message($uinfo);
            }
            if ($from == 'exchange_uinfo'){
                $member->nickname = $request->post('nickname');
                $member->head_portrait = $request->post('head_portrait');
                $member->save(false);
                //查询认证状态
                $verified_data = MemberVerified::find()->where(['uid' => $member->attributes['id']])->andWhere(['>', 'status', '0'])->select('status')->asArray()->one();
                $verified_status = empty($verified_data['status']) ? 0 : $verified_data['status'];
                $verified_status = 2;
                $this->success_message(['verified_status'=>$verified_status]);
            }
            $this->error_message("_MobilePhone_Exist_");
        }
        if ($from == 'token2token'){
            $this->error_message("_Please_set_exchange_password_");
        }
        $password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
        $member_model= new Member();
        $member_model->username = $mobile_phone;
        $member_model->mobile_phone = $mobile_phone;
        $member_model->mobile_phone_status = 1;
        $member_model->password_hash = $password_hash;
        $member_model->nickname = StringHelper::random(8);
        $member_model->head_portrait = '/attachment/images/head_portrait.png';
        $member_model->created_at = time();
        $member_model->last_time = time();
        $member_model->last_ip = Yii::$app->request->getUserIP();
        if($member_model->save(false) > 0){
            $user_id = $member_model->attributes['id'];
            $group = 1;
            $rst = AccessToken::setMemberInfo($group, $user_id);

            $uinfo = $this->getUserInfoById($user_id);
            $uinfo['access_token'] = $rst['access_token'];
            CreateWallet::create($user_id);
            $this->success_message($uinfo);
        }else{
            $this->error_message('_Registration_Failed_Try_Later_');
        }
    }
}