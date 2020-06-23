<?php
/**
 * Created by PhpStorm.
 * User: op
 * Date: 2018-06-26
 * Time: 17:20
 */

namespace jinglan\bitcoin;


class Unspent
{
    //private $NODE_HOST = "https://testnet.blockchain.info/unspent";
	//private $NODE_HOST = "https://blockchain.info/unspent";

    /*请求*/
    public function do_curl($params){
        try{
			if(isset($_POST['chain_network']) && $_POST['chain_network'] == 'main_network'){
				$NODE_HOST = "https://blockchain.info/unspent";
			}else{
				$NODE_HOST = "https://testnet.blockchain.info/unspent";
			}
            $url = $NODE_HOST . '?active=' . $params;
			if(\Yii::$app->request->hostName == 'wallet.kinlink.cn'){
                $url = "http://v4.deerlive.com/unspent.php?active=".$params."&chain_network=".$_POST['chain_network'];
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Blockchain-PHP/1.0');
            //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false); //处理http证书问题
            curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__).'/blockchain/src/Blockchain/ca-bundle.crt');
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $ret = curl_exec($ch);
            curl_close($ch);

            if (false === $ret) {
                $code = 0;
                $rst = curl_errno($ch);
            }else{
                if(strpos($ret, 'unspent_outputs') !== false){
                    $ret = json_decode($ret, true);
                    if(!empty($ret['unspent_outputs'])){
                        $code = 1;
                        $rst = $ret['unspent_outputs'];
                        foreach ($rst as $k => &$v){
                            if($v['confirmations'] < 6){
                                unset($rst[$k]);
                            }
                        }
                    }else{
                        $code = 0;
                        $rst = 'unspent error';
                    }
                }else{
                    if($ret == "No free outputs to spend"){
                        $code = 1;
                        $rst = 0;
                    }else{
                        $code = 0;
                        $rst = $ret;
                    }
                }
            }
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