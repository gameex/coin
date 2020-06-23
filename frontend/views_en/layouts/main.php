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
    <?=Html::cssFile('/resource/frontend/css/base.min.css')?>
    <?=Html::cssFile('/resource/frontend/css/header.css')?>
    <?=Html::cssFile('/resource/frontend/css/subpage.min.css')?>
    <?=Html::cssFile('/resource/frontend/css/user.min.css')?>
    <?=Html::cssFile('/resource/frontend/css/coin.min.css')?>
    <?=Html::cssFile('/resource/frontend/css/zcpc.min.css')?>
    <?=Html::cssFile('/resource/frontend/css/jb_style.css')?>
    <?=Html::cssFile('/resource/frontend/css/flexslider.css')?>
    <?=Html::cssFile('/resource/frontend/css/font-awesome.min.css')?>
    <?=Html::cssFile('/resource/frontend/css/light.css?v='.time())?>
    <?=Html::cssFile('/resource/frontend/css/night.css?v='.time())?>
    <script type="text/javascript" src="/resource/frontend/js/jquery.min.js"></script>
    <script type="text/javascript" src="/resource/frontend/js/layui.js"></script>
    <script type="text/javascript" src="/resource/frontend/js/http.js"></script>
    <script type="text/javascript" src="/resource/frontend/js/tool.js"></script>
</head>
	
<body id="<?php echo $_SESSION['mode']; ?>">
	
<?php $this->beginBody() ?>

<?php $this->beginContent('@app/views_en/layouts/header.php');?> 
	
<?php $this->endContent();?>
	
<?= $content ?>


<?php $this->beginContent('@app/views_en/layouts/footer.php');?> 
	
<?php $this->endContent();?>

<?php $this->endBody() ?>

<script type="text/javascript" src="/resource/frontend/js/header.js"></script>
</body>
</html>
<?php $this->endPage() ?>