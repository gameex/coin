<?php
namespace frontend\controllers;

use Yii;
use jianyan\basics\common\models\sys\ArticleSingle;
use common\models\Article;

/**
 * About controller
 */
class AboutController extends IController
{
    /**
     * 关于我们
     * @return string
     */
    public function actionIndex()
    {

	         $header['title']= "关于我们" ." -".Yii::$app->config->info('WEB_SITE_TITLE') ;
		  $header['keywords']= Yii::$app->config->info('WEB_SITE_KEYWORD');
		  $header['descripition']= Yii::$app->config->info('WEB_SITE_DESCRIPTION'); 
	         $view = Yii::$app->view;
		  $view->params['header']=$header;	  		                                 
                return $this->render('index');
    }
}
