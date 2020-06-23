<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
// use yii\bootstrap\Nav;
// use yii\bootstrap\NavBar;
// use yii\widgets\Breadcrumbs;
// use frontend\assets\AppAsset;
// use common\widgets\Alert;

//AppAsset::register($this);

?>
<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" >
    <title><?= $this->params['header']['title']?></title>
    <meta name="keywords" content="<?= $this->params['header']['keywords']?>"/>
    <meta name="description" content="<?= $this->params['header']['descripition']?>"/>  
    <META HTTP-EQUIV="pragma" CONTENT="no-cache"> 
	<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate"> 
	<META HTTP-EQUIV="expires" CONTENT="0">

</head>
	
<body>
	

	
<?= $content ?>



</body>
</html>
<?php $this->endPage() ?>