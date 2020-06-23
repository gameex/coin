<?php
namespace frontend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use common\controllers\BaseController;

/**
 * 前台基类控制器
 *
 * Class IController
 * @package frontend\controllers
 */
class IController extends BaseController
{
    /**
     * csrf验证
     * @var bool
     */
    public $enableCsrfValidation = false;

    /**
     * @throws NotFoundHttpException
     */
    public function init()
    {
        if (!session_id()){
            session_start();
        }
        //站点关闭信息
        if(Yii::$app->config->info('SYS_SITE_CLOSE') != 1)
        {
            throw new NotFoundHttpException('您访问的站点已经关闭');
        }

        if (!empty($_GET['language'])) {
            if($_GET['language'] == 'en'){
                $_SESSION['language'] = 'en';
            }
            if($_GET['language'] == 'zh'){
                $_SESSION['language'] = 'zh';
            }
        }
        if (!empty($_GET['mode'])) {
            if($_GET['mode'] == 'night'){
                $_SESSION['mode'] = 'night';
                $_SESSION['mode_choose'] = 1;

            }
            if($_GET['mode'] == 'day'){
                $_SESSION['mode'] = 'day';
                $_SESSION['mode_choose'] = 1;
            }
        }
        $is_night = 0;
        if (empty($_SESSION['mode_choose'])) {
            if($is_night == 1){
                $_SESSION['mode'] = 'night';
            }else{
                $_SESSION['mode'] = 'day';
            }
        }

        if (empty($_SESSION['language']) || $_SESSION['language'] == 'zh') {
            $this->module->setViewPath($this->module->getBasePath().'/views');
            parent::init();
        }else{
            $this->module->setViewPath($this->module->getBasePath().'/views_en');
            parent::init();
        }
    }
}
