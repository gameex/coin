<?php
/**
 * Created by PhpStorm.
 * User: op
 * Date: 2018-05-28
 * Time: 19:55
 */

namespace jinglan\sms;

use jinglan\sms\juhe\juhesms;

require_once dirname(__DIR__) . '/sms/api_sdk/vendor/autoload.php';


use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\SendBatchSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;

// 加载区域结点配置
Config::load();

class SMS
{
    private $mobile_phone;
    private $code;

    static $acsClient = null;

    /**
     * 参数初始化
     * @param $mobile_phone
     * @param $code
     */
    public function __construct($mobile_phone, $code){
        $this->mobile_phone = $mobile_phone;
        $this->code = $code;
    }

    /**
     * 取得AcsClient
     *
     * @return DefaultAcsClient
     */
    public static function getAcsClient() {
        //产品名称:云通信短信服务API产品,开发者无需替换
        $product = "Dysmsapi";

        //产品域名,开发者无需替换
        $domain = "dysmsapi.aliyuncs.com";

        // TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)
        $accessKeyId = \Yii::$app->config->info("SMS_ALIYUN_ACCESSKEYID"); // AccessKeyId  

        $accessKeySecret = \Yii::$app->config->info("SMS_ALIYUN_ACCESSKEYSECRET"); // AccessKeySecret 

        // 暂时不支持多Region
        $region = "cn-hangzhou";

        // 服务结点
        $endPointName = "cn-hangzhou";


        if(static::$acsClient == null) {

            //初始化acsClient,暂不支持region化
            $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
            // 增加服务结点
            DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

            // 初始化AcsClient用于发起请求
            static::$acsClient = new DefaultAcsClient($profile);

        }
        return static::$acsClient;
    }

 /**
     * 发送短信
     * @return stdClass
     */
    public static function sendSms($phone,$code) {
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();

        //可选-启用https协议
        //$request->setProtocol("https");

        // 必填，设置短信接收号码
        $request->setPhoneNumbers($phone);

        // 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $request->setSignName(\Yii::$app->config->info("SMS_ALIYUN_SIGNNAME"));

        // 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $request->setTemplateCode(\Yii::$app->config->info("SMS_ALIYUN_TEMPLATECODE"));

        // 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
        $request->setTemplateParam(json_encode(array(  // 短信模板中字段的值
            "code"=>$code,
        ), JSON_UNESCAPED_UNICODE));

        // 可选，设置流水号
        //$request->setOutId("yourOutId");

        // 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
        //$request->setSmsUpExtendCode("1234567");

        // 发起访问请求
        $acsResponse = self::getAcsClient()->getAcsResponse($request);

        return $acsResponse;
    }

    /*请求*/
    public function send(){
        //获取系统短信配置
        switch (\Yii::$app->config->info("SMS_ENABLE")){
            case 0:
                $ret = ['code'=>1,'message'=>'系统未指定短信服务商'];
                break;
            case 1://聚合短信
                $sms_juhe_appkey = \Yii::$app->config->info("SMS_JUHE_APPKEY");
                if (isset($_POST['sms_type']) && $_POST['sms_type'] == 'otc'){
                    $sms_juhe_templateid = \Yii::$app->config->info("SMS_JUHE_TEMPLATEID_OTC");
                }else{
                    $sms_juhe_templateid = \Yii::$app->config->info("SMS_JUHE_TEMPLATEID");
                }
                if(empty($sms_juhe_appkey) || empty($sms_juhe_templateid)){
                    $ret = ['code'=>1,'message'=>'聚合短信设置有误'];
                }else{
                    $sms = new juhesms();
                    $conf  = array(
                        "key"=> $sms_juhe_appkey,
                        "mobile"=>$this->mobile_phone,
                        "tpl_id"=> $sms_juhe_templateid,
                        "tpl_value"=>"#code#=".$this->code,
                    );
                    $sms->smsConf= $conf;
                    $ret = $sms->Send();
                }
                break;
            case 2://阿里大于
                $response = self::sendSms($this->mobile_phone,$this->code);
                //var_dump($response);die();
                if($response->Code=='OK'){
                    $ret = ['code'=>0,'message'=>$response->Message];
                }else{
                    $ret = ['code'=>1,'message'=>$response->Message];
                }
                break;
            case 3://来信码
                $lxm_accesskey = \Yii::$app->config->info("SMS_LXM_ACCESSKEY");
                $lxm_secretkey = \Yii::$app->config->info("SMS_LXM_SECRETKEY");
                $lxm_sign = \Yii::$app->config->info("SMS_LXM_SIGN");
                if(empty($lxm_accesskey) || empty($lxm_secretkey) || empty($lxm_sign)){
                    $ret = ['code'=>1,'message'=>'来信码短信设置有误'];
                }else{
                    $host='https://imlaixin.cn/Api/send/data/json?accesskey='.$lxm_accesskey.'&secretkey='.$lxm_secretkey.'&mobile='.$this->mobile_phone.'&content=验证码：'.$this->code.'【'.$lxm_sign.'】';
                    //echo $host;die();
                    $ret = $this -> curl($host);
                    // $ret = file_get_contents($host);
                    // var_dump($ret);die();
                    $response = json_decode($ret);
                    //var_dump($response);die();
                    if($response->result=='01'){
                        $ret = ['code'=>0,'message'=>$response->desc];
                    }else{
                        $ret = ['code'=>1,'message'=>$response->desc];
                    }
                }
                break;
        }
        return $ret;
    }






      /**
     * 返回带协议的域名
     */
    protected function actionGetHost(){
        $host=$_SERVER["HTTP_HOST"];
        $protocol=$this->is_ssl()?"https://":"http://";
        return $protocol.$host;
    }
    /**
     * 判断是否SSL协议
     * @return boolean
     */
    function is_ssl() {
        if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
            return true;
        }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
            return true;
        }
        return false;
    }
  

    public function curl($url, $postdata = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在      
        // curl_setopt($ch, CURLOPT_HTTPHEADER, [
        //     "Content-Type: application/json",
        // ]);
        $output = curl_exec($ch);
        $info   = curl_getinfo($ch);
        return $output;
    }











}