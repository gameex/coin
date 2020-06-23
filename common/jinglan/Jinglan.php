<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/22
 * Time: 12:45
 */

namespace common\jinglan;

use Yii;

class Jinglan
{
    public static $db_balance_log = [
        'type' => [
            '1'=>'+',
            '10'=>'-',
        ],
        'detial_type' => [
            'exchange' => '交易所',
            'chain'    => '链上',
            'system'   => '系统',
            'otc'      => '场外交易',
            'withdraw'      => '提现转出',
            'withdraw_fee'      => '提现转出手续费',
        ],
    ];

    public static $db_withdraw_apply = [
        'status' => [
            '1' => '待确认',
            '2'    => '通过',
            '3'   => '拒绝',
            '4'      => '失败',
            '5'      => '提现成功',
            '6'      => '执行处理中',
        ],
    ];

    protected static function get_user_avatar_url($avatar){
        if($avatar){
            if(strpos($avatar, "http")===0){
                return $avatar;
            }else{
                return \Yii::$app->request->hostInfo . $avatar;
            }
        }else{
            return \Yii::$app->request->hostInfo . '/attachment/images/head_portrait.png';
        }
    }

    //参数不能为空
    protected static function check_empty($input,$descrp='_NOT_Empty_'){
        $language = Yii::$app->request->post('language') == 'en_us'?'en_us':'zh_cn';
        if (empty($input)) {
            if(is_string($descrp)){
                $ret = array('code'=>500,'message'=>Yii::t($language,$descrp));
            }else{
                $ret = array('code'=>500,'message'=>$descrp);
            }
            self::do_aes(json_encode($ret));
        }
    }

    //普通错误信息,客户端直接提示即可,客户端不需要对此状态吗做特殊处理
    protected static function error_message($descrp='_Information_Wrong_'){
        $language = Yii::$app->request->post('language') == 'en_us'?'en_us':'zh_cn';
        if(is_string($descrp)){
            $ret = array('code'=>501,'message'=>Yii::t($language,$descrp));
        }else{
            $ret = array('code'=>501,'message'=>$descrp);
        }
        self::do_aes(json_encode($ret));
    }

    //普通成功信息,统一格式
    protected static function success_message($data='',$descrp = '_Submission_Success_'){
        $language = Yii::$app->request->post('language') == 'en_us'?'en_us':'zh_cn';
        if (empty($data)) {
            $ret = array('code'=>200,'message'=>Yii::t($language,$descrp));
        }else{
            $ret = array('code'=>200,'data'=>$data,'message'=>Yii::t($language,$descrp));
        }
        self::do_aes(json_encode($ret));
    }

    //统一返回处理
    protected static function do_aes($str){
        die($str);
    }

    /**
     * @param $num         科学计数法字符串  如 2.1E-5
     * @param int $double 小数点保留位数 默认18位
     * @return string
     */

    public static function sctonum($num, $double = 18){
        if(false !== stripos($num, "e")){
            $a = explode("e",strtolower($num));
            $b = bcmul($a[0], bcpow(10, $a[1], $double), $double);
            $c = rtrim($b, '0');
            return $c;
        }else{
            return $num;
        }
    }

    /*
    * @param $number int或者string
    * @return string
    */
    // 十进制转十六进制
    public static function bc_dechex($number)
    {
        if ($number <= 0) {
            return false;
        }
        $conf = ['0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f'];
        $char = '';
        do {
            $key = fmod($number, 16);
            $char = $conf[$key].$char;
            $number = floor(($number-$key)/16);
        } while ( $number > 0);
        return $char;
    }

    public static function check_mobile_phone($mobile_phone){
        $mobile_phone = preg_replace('/^(&nbsp;|＼s)*|(&nbsp;|＼s)*$/', '', $mobile_phone);
        self::check_empty($mobile_phone,'_PhoneNum_Not_Empty_');
      
   //     if(!preg_match("/^1[3|4|5|6|7|8|9][0-9]\d{4,8}$/",$mobile_phone)){
    //        self::error_message('_MobilePhoneNum_Illegal_');
      //  }
      $preg= "/^(\+|00){0,2}(9[976]\d|8[987530]\d|6[987]\d|5[90]\d|42\d|3[875]\d|2[98654321]\d|9[8543210]|8[6421]|6[6543210]|5[87654321]|4[987654310]|3[9643210]|2[70]|7|1)\d{1,14}$/";
      if(!preg_match($preg , $mobile_phone)){
         self::error_message('_MobilePhoneNum_Illegal_');
      }
        return $mobile_phone;
    }
  
      public static function check_email($email){
        $email = preg_replace('/^(&nbsp;|＼s)*|(&nbsp;|＼s)*$/', '', $email);
        self::check_empty($email,'邮箱不能为空');

         $preg= "/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/";
      if(!preg_match($preg , $email)){
         self::error_message('邮箱异常');
      }
        return $email;
    }

    public static function usd_to_cny(){
        $tablePrefix = Yii::$app->db->tablePrefix;

        $rst = (new \yii\db\Query())
            ->from("{$tablePrefix}extension")
            ->where(['type'=>'usd_to_cny'])
            ->select('detial')
            ->one();
        return (float)$rst['detial'];
    }

    public static function get_extension($type){
        $tablePrefix = Yii::$app->db->tablePrefix;

        $rst = (new \yii\db\Query())
            ->from("{$tablePrefix}extension")
            ->where(['type'=>$type])
            ->select('detial')
            ->one();
        return empty($rst['detial']) ? null : $rst['detial'];
    }
}