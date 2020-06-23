<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'zh-CN',
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'index',//默认控制器
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'common\models\member\Member',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
            'loginUrl' => ['site/login'],
            'idParam' => '__user',
            'as afterLogin' => 'common\behaviors\AfterLogin',
        ],
        'session' => [
            // 这是用于在前台登录的会话cookie的名称
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        /** ------ 路由配置 ------ **/
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,  //这个是生成路由 ?r=site/about--->/site/about
            'showScriptName' => false,
            //'suffix' => '.html',//静态
            'rules' =>[
				'help/<id:\d+>' => 'help/index',
				'notice/<id:\d+>' => 'article/index',
				'trade/<money:\w+>/<stock:\w+>' => 'trade/index',
				'wiki/<id:\d+>' => 'wiki/wiki-detail',
                'config' => 'tradeview/config',
                'symbols' => 'tradeview/symbols',
                'time' => 'tradeview/time',
                'history' => 'tradeview/history',
            ],
        ],
        /** ------ 语言切换 ------ **/
        'i18n'=>[
            'translations'=>[
                '*'=>[
                    'class'=>'yii\i18n\PhpMessageSource',
                    'fileMap'=>[
                        'comment' =>  'comment.php',
                        'en_US' =>  'en_US.php',
                    ],
                ]

            ],
        ],
    ],
    'controllerMap' => [
        //插件渲染默认控制器
//        'addons' => [
//            'class' => 'jianyan\basics\common\controllers\AddonsBaseController',
//        ],
        'file' => [
            'class' => 'jianyan\basics\common\controllers\FileBaseController',
        ]
    ],
    'params' => $params,
];
