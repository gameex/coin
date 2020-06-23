<?php
use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--[if lt IE 8]>
    <meta http-equiv="refresh" content="0;ie.html" />
    <![endif]-->
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <script>var _hmt = _hmt || [];</script>
</head>
<body>
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
<script>
_hmt.push(['_setCustomVar', 1, 'visitor', 's--m-a-r--t-c-o--i-n', 1]);
(function() {
  var hm = document.createElement("script");
  hm.src = "https://hm.baidu.com/hm.js?e313543170dfb3c4281a87c0f28bd4fd";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>
</body>
</html>
<?php $this->endPage() ?>
