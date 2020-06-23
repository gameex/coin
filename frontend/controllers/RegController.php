<?php
namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use jianyan\basics\backend\modules\sys\models\LoginForm;

/**
 * Index controller
 */
class RegController extends IController
{


    
    public function actions()
    {
        return [
            // 验证码
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'maxLength' => 4,        // 最大显示个数
                'minLength' => 4,        // 最少显示个数
                'padding'   => 5,        // 间距
                'height'    => 32,       // 高度
                'width'     => 100,      // 宽度
                'offset'    => 7,        // 设置字符偏移量
                'backColor' => 0xffffff, // 背景颜色
                'foreColor' => 0x1ab394, // 字体颜色
            ]
        ];
    }  

    /**
     * 系统首页
     * @return string
     */
    public function actionIndex()
    {
          $header['title']= "Register"." - ".Yii::$app->config->info('WEB_SITE_TITLE') ;
      $header['keywords']= Yii::$app->config->info('WEB_SITE_KEYWORD');
      $header['descripition']= Yii::$app->config->info('WEB_SITE_DESCRIPTION');  
         $view = Yii::$app->view;
      $view->params['header']=$header;

        return $this->render('index');
    }





}
