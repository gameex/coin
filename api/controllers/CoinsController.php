<?php 
/*
* name: xiaocai
* date: 2018-8-27 12:00
*/
namespace api\controllers;

use Yii;
use api\models\Coin;
use jinglan\ves\VesRPC;

class CoinsController extends ApibaseController
{
    public $modelClass = '';

    public function init(){
        parent::init();
    }


    public function actionUp()
    {
        $rpc_method = 'market.last';
        $rpc_params = ['BTCUSDT'];
        $rpc = new VesRPC();
        $rpc_ret = $rpc->do_rpc($rpc_method, $rpc_params);
        
        if(!empty($rpc_ret ["data"])){
            $coin_model = Coin::findOne(['symbol'=>'BTC']);
            $coin_model->usd = $rpc_ret ["data"];
            $coin_model->cny = $rpc_ret ["data"]*6.81;
            $coin_model->save();
        }

        $rpc_method = 'market.last';
        $rpc_params = ['ETHUSDT'];
        $rpc = new VesRPC();
        $rpc_ret = $rpc->do_rpc($rpc_method, $rpc_params);
        if(!empty($rpc_ret ["data"])){
            $coin_model = Coin::findOne(['symbol'=>'ETH']);
            $coin_model->usd = $rpc_ret ["data"];
            $coin_model->cny = $rpc_ret ["data"]*6.81;
            $coin_model->save();
        }
        die('ok');
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