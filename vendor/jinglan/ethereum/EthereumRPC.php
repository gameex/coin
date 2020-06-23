<?php
namespace jinglan\ethereum;
/**
 * Created by PhpStorm.
 * User: op
 * Date: 2018-05-25
 * Time: 15:39
 */
use Yii;
class EthereumRPC
{
    //private $NODE_HOST = "http://116.62.129.180:8545";
	//private $NODE_HOST = "http://47.105.103.240:8545";
    private $method;    //Ethereum RPC method
    private $params;    //Ethereum RPC params(array)

    /**
     * 参数初始化
     * @param $method
     * @param $params
     */
    public function __construct($method,$params){
        $this->method = $method;
        $this->params = $params;
    }

    /*请求*/
    public function do_rpc(){
        try{
            // if(isset($_POST['chain_network']) && $_POST['chain_network'] == 'main_network'){
            //     $NODE_HOST = "http://47.105.103.240:8545";
            // }else{
            //     $NODE_HOST = "http://116.62.129.180:8545";
            // }
            $open=fopen("createwallet.txt","a+" );
            fwrite($open,"\r\n##### ETH ##############\r\n");

           if(isset($_POST['chain_network']) && $_POST['chain_network'] == 'main_network'){
               $select_host = Yii::$app->config->info('ETH_SELECT_HOST');
               if ($select_host == '1') {
                   $NODE_HOST = Yii::$app->config->info('ETH_HOST_1');
               }else{
                   $NODE_HOST = Yii::$app->config->info('ETH_HOST_2');
               }
           }else{
               $select_host = Yii::$app->config->info('ETH_SELECT_HOST_TEST');
               if ($select_host == '1') {
                   $NODE_HOST = Yii::$app->config->info('ETH_HOST_TEST_1');
               }else{
                   $NODE_HOST = Yii::$app->config->info('ETH_HOST_TEST_2');
               }
           }

           $language = Yii::$app->request->post('language') == 'en_us'?'en_us':'zh_cn';
           if ($language == 'en_us') {
               $err_msg = 'The background network node is not configured. Please try again later';
           }else{
               $err_msg = '后台网络节点未配置，请稍后再试！';
           }
           // 后台未配置节点信息，返回错误信息！
           if (empty($NODE_HOST)) {
               return array(
                   'code' => 0,
                   'data' => $err_msg
               );
           }
            fwrite($open,'$NODE_HOST::'.$NODE_HOST."\r\n");

            $data = array(
                'jsonrpc' => "2.0",
                'method' => $this->method,
                'params' => $this->params,
                'id' => 1
            );
            fwrite($open,'data::'.json_encode($data)."\r\n");

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $NODE_HOST);
            curl_setopt($ch, CURLOPT_POST, 1);
            $httpHeader[] = 'Content-Type:Application/json';
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data) );

            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false); //处理http证书问题
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $ret = curl_exec($ch);

            if (false === $ret) {
                $code = 0;
                $rst = curl_errno($ch);
            }else{
                $ret = json_decode($ret);
                if(!empty($ret->error->code)){
                    $code = 0;
                    $rst = $ret->error->message;
                }else{
                    $code = 1;
                    $rst = $ret->result;
                }
            }
            curl_close($ch);
            fclose($open);
        }catch(Exception $e){
            $code = 0;
            $rst = $e->getMessage();
        }
        return array(
            'code' => $code,
            'data' => $rst
        );
    }


    /**
     * @param $num         科学计数法字符串  如 2.1E-5
     * @param int $double 小数点保留位数 默认18位
     * @return string
     */

    public function sctonum($num, $double = 18){
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
    public function bc_dechex($number)
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
}
