<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/13
 * Time: 17:08
 */

namespace common\jinglan;

use jinglan\qrcode\Qrcode;
use Yii;
use api\models\Varcode;
use api\models\IpLog;
use jinglan\sms\SMS;

class Common extends Jinglan
{

    public static function send_mob_varcode($mobile_phone){
        $mobile_phone = preg_replace('/^(&nbsp;|＼s)*|(&nbsp;|＼s)*$/', '', $mobile_phone);
        //短信服务检验
        $limit_time = Yii::$app->config->info("SMS_SEND_LIMIT_TIME") > 0 ? intval(Yii::$app->config->info("SMS_SEND_LIMIT_TIME")) : 5;
        $varcode_result = Varcode::find()->where(['mobile_phone'=>$mobile_phone])->one();
        if($varcode_result){
            if(time() - $varcode_result->attributes['updated_at'] < $limit_time*60){
                Jinglan::error_message($limit_time.'每分钟内限发一次');
            }
        }
        $ip = Yii::$app->request->getUserIP();
        if(Yii::$app->config->info("SMS_IP_LIMIT_ENABLE") == 1){
            $limit_times = Yii::$app->config->info("SMS_IP_LIMIT_TIMES") > 0 ? intval(Yii::$app->config->info("SMS_IP_LIMIT_TIMES")) : 5;
            $varcode_result2 = Varcode::find()->where(['ip'=>$ip])->one();
            if($varcode_result2){
                if(time() - $varcode_result2->attributes['updated_at'] < $limit_time*60){
                    Jinglan::error_message($limit_time.'每分钟内限发一次');
                }
            }
            //记录请求ip，限制同一ip一天内请求次数
            //查询是否有该ip
            $ip_result = IpLog::find()->where(['ip'=>$ip])->one();
            if(empty($ip_result)){
                $ip_log_model = new IpLog();
                $ip_log_model->ip = $ip;
                $ip_log_model->times = 1;
                $ip_log_model->save();
            }else{
                $ip_log_model = IpLog::findOne(['id'=>$ip_result->attributes['id']]);
                if(time() - $ip_result->attributes['updated_at'] > 86400){//本次请求大于上次1天
                    $ip_log_model->ip = $ip;
                    $ip_log_model->times = 1;
                }else{//同一天的请求
                    if($ip_result->attributes['times'] >= $limit_times){
                        Jinglan::error_message('超出系统设置公网限制次数');
                    }else{
                        $ip_log_model->times += 1;
                    }
                }
                $ip_log_model->save();
            }
        }
        $code = strval(rand(100000,999999));
        // 新增固定号码返回固定验证码[13552017673]
        if ($mobile_phone == '13552017673') {
            $code = '123456';
        }
        $sms = new SMS($mobile_phone, $code);
        $send_result = $sms->send();
        if($send_result['code'] == 0) {//发送成功
            if(empty($varcode_result)) {//表中无记录
                $varcode_model = new Varcode();
                $varcode_model->mobile_phone = $mobile_phone;
                $varcode_model->varcode = $code;
                $varcode_model->ip = $ip;
                $varcode_model->member_id = 0;
                if($varcode_model->save() > 0){
                    //Jinglan::success_message(['code'=>$varcode_model['varcode']]);
                    Jinglan::success_message();
                }else{
                    Jinglan::error_message('SAVE ERROR#1');
                }
            }else {//表中有记录
                $varcode_model = Varcode::findOne(['id'=>$varcode_result->id]);
                $varcode_model->mobile_phone = $mobile_phone;
                $varcode_model->varcode = $code;
                $varcode_model->ip = $ip;
                $varcode_model->member_id = 0;
                if($varcode_model->save() > 0){
                    Jinglan::success_message();
                }else{
                    Jinglan::error_message('SAVE ERROR#2');
                }
            }
        }else{
            Jinglan::error_message($send_result['message']);
        }
    }

    public static function Qrcode($url,$size){
        $qr = new Qrcode();
        $level = 2;
        $qr::png($url, false, $level, $size);
    }
}