<?php
namespace frontend\controllers;

use Yii;

/**
 * Index controller
 */
class IndexController extends IController
{
    /**
     * 系统首页
     * @return string
     */
    public function actionIndex()
    {
         $header['title']= Yii::$app->config->info('WEB_SITE_TITLE') ;
	  $header['keywords']= Yii::$app->config->info('WEB_SITE_KEYWORD');
	  $header['descripition']= Yii::$app->config->info('WEB_SITE_DESCRIPTION');
         $view = Yii::$app->view;
	  $view->params['header']=$header;	  
          return $this->render('index'); 
    }
}
