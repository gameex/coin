<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/11
 * Time: 14:36
 */

namespace api\controllers;

use Yii;
use common\models\Robot;
use api\models\ExchangeCoins;


class RobotController extends ApibaseController
{
    public $modelClass = '';

    public function init(){
        parent::init();
    }

    public function actionRobotList()
    {
		$host=$this->actionGetHost().'/huobi.php';
        $ret = $this -> curl($host);
        $ret2 = json_decode($ret);
        $exchange_coins_list = ExchangeCoins::find()->select(['id','stock','money'])->asArray()->all();
        foreach($ret2 as $key => $val){
            foreach($exchange_coins_list as $k=>$v){
                if ($key == strtolower($v['stock'].$v['money'])) {
                    $robot = Robot::find()->where(['market_id'=>$v['id']])->one();
                    if(empty($robot)){
                      continue;
                    }
                    $big_money = $val->max_price;
                    $small_money = $val->min_price;

                    $robot->big_money = $big_money*1.0005;
                    $robot->small_money = $small_money*0.9995;
                    $robot->save();
              		//var_dump($key);              
              		//var_dump($big_money);
                }
            }
        }

        
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
        ]);
        $output = curl_exec($ch);
        $info   = curl_getinfo($ch);
        curl_close($ch);
        return $output;
    }
    
}