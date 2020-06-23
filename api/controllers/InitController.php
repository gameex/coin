<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9
 * Time: 16:56
 */

namespace api\controllers;

use Yii;


class InitController extends ApibaseController
{
    public $modelClass = '';

    public function init(){
        parent::init();
    }

    public function actionInfo(){
        $url = Yii::$app->config->info('VIA_WEBSOCKET');
        $ret = array(
            'via_websocket_url' => $url,
        );
        $this->success_message($ret);
    }
}