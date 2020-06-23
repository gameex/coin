<?php
namespace frontend\controllers;

use Yii;
use jianyan\basics\common\models\sys\ArticleSingle;
use common\models\Article;

/**
 * Index controller
 */
class RiskController extends IController
{
    /**
     * 系统首页
     * @return string
     */
    public function actionIndex()
    {
        $id = 18;
        $where['id'] = $id;
        $select = 'title,content';
        $content = ArticleSingle::find()->select($select)->where($where)->asArray()->one();
        if (empty($content)) {
                $content['title'] = '您要访问的内容不存在';
                $content['content'] = '您要访问的内容不存在';
        }
         $header['title']= "风险提示"." - ".Yii::$app->config->info('WEB_SITE_TITLE') ;
	  $header['keywords']= Yii::$app->config->info('WEB_SITE_KEYWORD');
	  $header['descripition']= Yii::$app->config->info('WEB_SITE_DESCRIPTION');  
         $view = Yii::$app->view;
	  $view->params['header']=$header;	         
        return $this->render('index', [
            'content'  => $content,
        ]);
    }
}
