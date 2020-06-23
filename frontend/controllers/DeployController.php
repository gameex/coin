<?php
namespace frontend\controllers;

use Yii;
use jianyan\basics\common\models\sys\ArticleSingle;
use common\models\Article;

/**
 * About controller
 */
class DeployController extends IController
{
    /**
     * 申请上币
     * @return string
     */
    public function actionIndex()
    {

	      $header['title']= "申请上币" ." - ".Yii::$app->config->info('WEB_SITE_TITLE') ;
		  $header['keywords']= Yii::$app->config->info('WEB_SITE_KEYWORD');
		  $header['descripition']= Yii::$app->config->info('WEB_SITE_DESCRIPTION'); 
	         $view = Yii::$app->view;
		  $view->params['header']=$header;	  		                                 
                return $this->render('index');
    }
}
