<?php
namespace frontend\controllers;

use Yii;

/**
 * Download controller
 */
class DownloadController extends IController
{
    /**
     * 下载APP
     * @return string
     */
    public function actionIndex()
    {

	         $header['title']= "下载APP" ." -".Yii::$app->config->info('WEB_SITE_TITLE') ;
		  $header['keywords']= Yii::$app->config->info('WEB_SITE_KEYWORD');
		  $header['descripition']= Yii::$app->config->info('WEB_SITE_DESCRIPTION'); 
	         $view = Yii::$app->view;
		  $view->params['header']=$header;	  		                                 
                return $this->render('index');
    }
}
