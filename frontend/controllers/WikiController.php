<?php
namespace frontend\controllers;

use Yii;
use common\models\Article;

/**
 * Index controller
 */
class WikiController extends IController
{
    /**
     * 系统首页
     * @return string
     */
    public function actionIndex()
    {
           $header['title']= "数字货币百科"." - ".Yii::$app->config->info('WEB_SITE_TITLE') ;
	  $header['keywords']= Yii::$app->config->info('WEB_SITE_KEYWORD');
	  $header['descripition']= Yii::$app->config->info('WEB_SITE_DESCRIPTION');  
         $view = Yii::$app->view;
	  $view->params['header']=$header;	    	     	
        return $this->render('index');
    }
  
    public function actionWikiDetail()
    {
             $request = Yii::$app->request;
            $id = intval($request->get('id'));
            $where['id'] = $id;
            $select = 'title,content';
            $content = Article::find()->select($select)->where($where)->asArray()->one();
            if (empty($content)) {
                    $content['title'] = '您要访问的内容不存在';
                    $content['content'] = '您要访问的内容不存在';
            }   	
          $header['title']= $content['title']." - "."数字货币百科"." - ".Yii::$app->config->info('WEB_SITE_TITLE') ;
	  $header['keywords']= Yii::$app->config->info('WEB_SITE_KEYWORD');
	  $header['descripition']= Yii::$app->config->info('WEB_SITE_DESCRIPTION');  
         $view = Yii::$app->view;
	  $view->params['header']=$header;	    	     	
                return $this->render('detail', [
                    'content'  => $content,
                ]);
    }    
}
