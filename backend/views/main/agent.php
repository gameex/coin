<?php
$this->title = '首页';
$this->params['breadcrumbs'][] = ['label' => $this->title];

use yii\helpers\Html;
use yii\web\Session;

?>

<?= Html::cssFile('/resource/backend/css/total.css') ?>

<div class="wrapper wrapper-content">
    <div class="row">
		<div class="col-sm-4">
		    <div class="ibox">
		        <div class="ibox-content" style="border-bottom: 2px solid rgba(91,114,135,0.5);">
		            <h4></h4>
		            <h1 class="no-margins" style="height: 80px;">欢迎使用</h1>
		            <div class="stat-percent font-bold text-navy"> </div>
		            <small> </small>
		            <img src="/resource/backend/img/img_houtaiyonghu.png" alt="" style="position: absolute;top:0;right:60px;">
		        </div>
		    </div>
		</div>
    </div>
</div>
