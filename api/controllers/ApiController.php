<?php

namespace api\controllers;

use Yii;
use jinglan\ves\VesRPC;
use common\jinglan\Reward;


class ApiController extends ApibaseController
{
    public $modelClass = '';

    public function init(){
        parent::init();
    }

    public function actionTest(){

      // $usd_price = Reward::coin_usd_price("LET");

     //  Reward::recharge(1002184,"BTC",0.001);

     //  Reward::order(1002184,"BTC",0.001);
  
     //  Reward::withdraw(1002184,"BTC",0.001);
     
    }

    public function actionSummary(){
        $rpc = new VesRPC();
        $rpc_method = 'market.summary';
        $rpc_params = [];        
        $rpc_ret = $rpc->do_rpc($rpc_method, $rpc_params);
        if($rpc_ret['code'] == 0){
            $this->error_message('failed#1');
        }else{
            //var_dump($rpc_ret);die();
            $summary_data = $rpc_ret['data'];
        }
        if (is_array($summary_data)){
            foreach ($summary_data as $key => &$value) {
                $rpc_method2 = 'market.status_today';
                $rpc_params2 = [$value['name']];        
                $rpc_ret2 = $rpc->do_rpc($rpc_method2, $rpc_params2);
                if($rpc_ret2['code'] == 1){
                    $value = array_merge($value,$rpc_ret2['data']);
                }            
            }
           $this->success_message($summary_data);
        }else{
            $this->error_message('failed#2');
        }
    }
}