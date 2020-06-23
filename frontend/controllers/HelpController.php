<?php
namespace frontend\controllers;

use Yii;
use jianyan\basics\common\models\sys\ArticleSingle;
use common\models\Article;

/**
 * Index controller
 */
class HelpController extends IController
{
    /**
     * 帮助页面
     * @return string
     */
    public function actionIndex()
    {
        $all_title = ArticleSingle::find()->select('id,title')->where('status = 1')->asArray()->all();

        $request = Yii::$app->request;
        $id = intval($request->get('id'));
        if (empty($id)) {
            $id=-1;
        }
        $where['id'] = $id;
        $select = 'title,content';
        $content = ArticleSingle::find()->select($select)->where($where)->asArray()->one();
        if (empty($content)) {
            $content['title'] = '您要访问的内容不存在';
            $content['content'] = '您要访问的内容不存在';
        }





        $header['title']= $content['title'] ." -".Yii::$app->config->info('WEB_SITE_TITLE') ;
        $header['keywords']= Yii::$app->config->info('WEB_SITE_KEYWORD');
        $header['descripition']= Yii::$app->config->info('WEB_SITE_DESCRIPTION'); 
        $view = Yii::$app->view;
        $view->params['header']=$header;	  		                                 
        return $this->render('index', [
            'content'  => $content,
            'all_title'  => $all_title,
        ]);
    }
}
