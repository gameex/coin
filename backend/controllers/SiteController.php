<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use jianyan\basics\common\models\sys\ActionLog;
use jianyan\basics\backend\modules\sys\models\LoginForm;

/**
 * 站点控制器
 *
 * Class SiteController
 * @package backend\controllers
 */
class SiteController extends Controller
{
    /**
     * 默认布局文件
     *
     * @var string
     */
    public $layout  = "default";

    /**
     * 统一加载
     *
     * @return array
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            // 验证码
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'maxLength' => 5,        // 最大显示个数
                'minLength' => 5,        // 最少显示个数
                'padding'   => 5,        // 间距
                'height'    => 32,       // 高度
                'width'     => 100,      // 宽度
                'offset'    => 4,        // 设置字符偏移量
                'backColor' => 0xffffff, // 背景颜色
                'foreColor' => 0x1ab394, // 字体颜色
            ]
        ];
    }

    /**
     * 行为控制
     *
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error','captcha'],
                        'allow' => true,
                        'roles' => ['?'],// 游客
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],// 登录
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * 后台登陆
     *
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest)
        {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login())
        {
            // 插入日志
            Yii::$app->actionlog->addLog(ActionLog::ACTION_LOGIN,"manager");

            return $this->goHome();
        }else{
            return $this->render('login', [
                'model'  => $model,
            ]);
        }
    }

    /**
     * 退出登陆
     *
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        // 插入日志
        Yii::$app->actionlog->addLog(ActionLog::ACTION_LOGOUT,"manager");
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
