<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/jianyan74/rageframe-basics/yii/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../../api/config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../vendor/jianyan74/rageframe-basics/api/config/main.php'),
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../../common/config/main-local.php'),
    require(__DIR__ . '/../../api/config/main.php'),
    require(__DIR__ . '/../../api/config/main-local.php')
);

/**
 * 通过配置重写类
 * yii class Map Custom
 */
$yiiClassMap = require(__DIR__ . '/../../common/config/YiiClassMap.php');
if(is_array($yiiClassMap) && !empty($yiiClassMap)){
    foreach($yiiClassMap as $namespace => $filePath){
        Yii::$classMap[$namespace] = $filePath;
    }
}
require(__DIR__ . '/../../vendor/jinglan/autoload.php');

/**
 * 打印
 * @param $array
 */
function p($array){
    echo "<pre>";
    print_r($array);
}
function G($start,$end='',$dec=4) {
    static $_info       =   array();
    static $_mem        =   array();
    if(is_float($end)) { // 记录时间
        $_info[$start]  =   $end;
    }elseif(!empty($end)){ // 统计时间和内存使用
        if(!isset($_info[$end])) $_info[$end]       =  microtime(TRUE);
        if(TRUE && $dec=='m'){
            if(!isset($_mem[$end])) $_mem[$end]     =  memory_get_usage();
            return number_format(($_mem[$end]-$_mem[$start])/1024);
        }else{
            return number_format(($_info[$end]-$_info[$start]),$dec);
        }

    }else{ // 记录时间和内存使用
        $_info[$start]  =  microtime(TRUE);
        if(TRUE) $_mem[$start]           =  memory_get_usage();
    }
    return null;
}

/**
 * 添加rageframe的服务 ，Yii::$service  ,  将services的配置添加到这个对象。
 * 使用方法：Yii::$service->cms->article;
 * 上面的例子就是获取cms服务的子服务article。
 */
new jianyan\basics\services\Application($config['services']);
unset($config['services']);

(new yii\web\Application($config))->run();
