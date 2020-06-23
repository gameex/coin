<?php
namespace jinglan\ves;
/**
 * Created by PhpStorm.
 * User: op
 * Date: 2018-05-25
 * Time: 15:39
 */
use Yii;

class VesRPC
{
    /*请求*/
    public function do_rpc($method, $params){
        try{
            // $NODE_HOST = "http://47.105.103.240:8080";
            $NODE_HOST = Yii::$app->config->info('VIA_HTTP_API');
          
            $data = array(
                'jsonrpc' => "2.0",
                'method' => $method,
                'params' => $params,
                'id' => time(),
            );
       
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $NODE_HOST);
            curl_setopt($ch, CURLOPT_POST, 1);
            $httpHeader[] = 'Content-Type:application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data) );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false); //处理http证书问题
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
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
                    $rst = json_decode(json_encode($ret->result), true);
                }
            }
            curl_close($ch);
        }catch(Exception $e){
            $code = 0;
            $rst = $e->getMessage();
        }
        return array(
            'code' => $code,
            'data' => $rst
        );
    }
}
