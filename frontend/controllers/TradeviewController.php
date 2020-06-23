<?php
namespace frontend\controllers;

use Yii;
use yii\web\Session;
use jinglan\ves\VesRPC;

/**
 * Index controller
 */
class TradeviewController extends IController
{
    /**
     * 系统首页
     * @return string
     */
    public function actionConfig()
    {
        $arr = array(
            "supports_search" => true,
            "supports_group_request"=> false,
            "supported_resolutions"=> ["1", "5", "15", "30", "60", "1D", "1W", "1M"],
            "supports_marks"=> false,
            "supports_time"=> true
        );
        die(json_encode($arr));
    } 

     public function actionSymbols()
    {
        $symbol = explode("/",$_GET['symbol']);
        if (empty($symbol[1])) {
            die();
        }
        $stock = $symbol[0];
        $money = $symbol[1];

        if (($stock == 'BTC') && ($money == 'USDT'))  {
            $pricescale = 100;
        } elseif (($stock == 'ETH') && ($money == 'USDT')) {
            $pricescale = 100;
        } elseif (($stock == 'LTC') && ($money == 'USDT')) {
            $pricescale = 10000;
        }else{
            $pricescale = 100000000;
        }

        $arr = array(
            "currency_code" => $money,
            "description"=> $stock."/". $money,
            "exchange"=> Yii::$app->config->info('WEB_APP_NAME'),
            "full_name"=> $stock,
            "has_daily"=> true,
            "has_intraday"=> true,
            "has_no_volume"=> false,
            "has_weekly_and_monthly"=> true,
            "industry"=> Yii::$app->config->info('WEB_APP_NAME'),
            "listed_exchange"=>Yii::$app->config->info('WEB_APP_NAME'),
            "minmov"=> 1,
            "name"=> $stock,
            "pricescale"=> $pricescale,
            "sector"=> $stock,
            "session"=> "24x7",
            "ticker"=> $stock.$money,
            "timezone"=> "Asia/Hong_Kong",
            "type"=> Yii::$app->config->info('WEB_APP_NAME'),
            "volume_precision"=> 4,
            "supported_resolutions"=> ["1", "5", "15", "30", "60", "1D", "1W", "1M"],
        );
        die(json_encode($arr));
    }
     public function actionTime()
    {
        die(time()."");
    }  

    public function actionHistory()
    {
        $m = $_GET['symbol'];
        $from = $_GET['from'];
        $to = $_GET['to'];
        $interval = $_GET['resolution'];
        $rpc_method = 'market.kline';
        if($interval=="1D"){
            $interval = 60*24;
        }
        if ($interval=="1W") {
             $interval = 60*24*7;
        }
        if ($interval=="1M") {
            $interval = 60*24*30;
        }
        $rpc = new VesRPC();
        $rpc_params = [$m, (int)$from, (int)$to, (int)$interval*60];
        $rpc_ret = $rpc->do_rpc($rpc_method, $rpc_params);
        if ($rpc_ret['code'] == 0) {
            die(json_encode($rpc_ret['data']));
        } else {
            $ret = $rpc_ret['data'];
        }
        $c = [];
        $h = [];
        $l = [];
        $o = [];
        $t = [];
        $v = [];
        if (!empty($ret)) {
            foreach ($ret as $k => $val) {
                $t[$k] =  $val[0];
                $o[$k] =  $val[1];
                $c[$k] =  $val[2];
                $h[$k] =  $val[3];
                $l[$k] =  $val[4];
                $v[$k] =  $val[5];
                //array_push($t, $v[0]);//time
                //array_push($o, $v[1]);//open
                //array_push($c, $v[2]);//close
                //array_push($h, $v[3]);//highest
                //array_push($l, $v[4]);//lowest
                //array_push($v, $v[5]);//volume
            }
        }

        $z = ['c'=>$c,'h'=>$h,'l'=>$l,'o'=>$o,'t'=>$t,'v'=>$v,'s'=>'ok'];
        die(json_encode($z));
    }  
   
}
