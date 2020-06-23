<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/11
 * Time: 14:36
 */

namespace api\controllers;

use common\jinglan\Chat;
use Yii;

class ChatController extends ApibaseController
{
    public $modelClass = '';

    public function init(){
        parent::init();
    }

    public function actionInit()
    {
        $request = Yii::$app->request;

        $access_token = $request->post('access_token');
        $uinfo = $this->checkToken($access_token);

        Chat::init($uinfo);
    }

    public function actionInitPc()
    {
        $request = Yii::$app->request;

        $access_token = $request->post('access_token');
        $uinfo = $this->memberToken($access_token);

        Chat::initPc($uinfo['id']);
    }

    public function actionHistory()
    {
        $request = Yii::$app->request;

        $access_token = $request->post('access_token');

        $uinfo = $this->checkToken($access_token);

        Chat::history($uinfo['id']);
    }
}