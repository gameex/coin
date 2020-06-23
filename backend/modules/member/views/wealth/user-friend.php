<?php
use yii\helpers\Url;
use dosamigos\datetimepicker\DateTimePicker;
use yii\widgets\LinkPager;

$this->title = '推荐好友';
$this->params['breadcrumbs'][] = ['label' => '用户信息', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title];

$starttime = '';
$endtime = '';
if (!empty($_GET['starttime'])) {
    $starttime = $_GET['starttime'];
}
if (!empty($_GET['endtime'])) {
    $endtime = $_GET['endtime'];
}
?>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>查询</h5>
                </div>
                <div class="ibox-content">
                    <form action="" method="get" class="form-horizontal" role="form" id="form">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">开始时间</label>
                            <div class="col-sm-8 input-group">
                                    <?= DateTimePicker::widget([
                                        'value' => $starttime, 
                                        'id' => 'starttime',
                                        'name' => 'starttime',//当没有设置model时和attribute时必须设置name
                                        'language' => 'zh-CN',
                                        'size' => 'ms',
                                        'clientOptions' => [
                                            'autoclose' => true,
                                            'format' => 'yyyy-mm-dd hh:ii:ss',
                                            'todayBtn' => true
                                        ]
                                    ]);?> 
                                <span class="input-group-btn">
                                    <button class="btn btn-white"><i class="fa fa-search"></i> 搜索</button>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">结束时间</label>
                            <div class="col-sm-8 input-group">
                                    <?= DateTimePicker::widget([
                                        'value' => $endtime, 
                                        'id' => 'endtime',
                                        'name' => 'endtime',//当没有设置model时和attribute时必须设置name
                                        'language' => 'zh-CN',
                                        'size' => 'ms',
                                        'clientOptions' => [
                                            'autoclose' => true,
                                            'format' => 'yyyy-mm-dd hh:ii:ss',
                                            'todayBtn' => true
                                        ]
                                    ]);?> 
                                <span class="input-group-btn">
                                    <button class="btn btn-white"><i class="fa fa-search"></i> 搜索</button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>用户资产</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>头像</th>
                                <th>昵称</th>
                                <th>手机号码</th>
                                <th>实名认证</th>
                                <th>最后登陆IP</th>
                                <th>最后登陆时间</th>
                                <th>注册时间</th>
                                <th>状态</th>
                                <th>总收益</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($models as $model){ ?>
                            <tr>
                                <td><?= $model->id?></td>
                                <td class="feed-element">
                                    <?php if($model->head_portrait){ ?>
                                        <img src="<?= $model->head_portrait; ?>" class="img-circle">
                                    <?php }else{ ?>
                                        <img src="/resource/backend/img/default-head.png" class="img-circle">
                                    <?php } ?>
                                </td>
                                <td><?= $model->nickname?></td>
                                <td><?= $model->username?></td>
                                <td><span style="color:<?= $status_color[$model->verified_status]?>;"><?= $status[$model->verified_status]?></span></td>
                                <td><?= $model->last_ip?></td>
                                <td><?= Yii::$app->formatter->asDatetime($model->last_time)?></td>
                                <td><?= Yii::$app->formatter->asDatetime($model->created_at)?></td>
                                <td>
                                    <?php if ($model->status==0) { ?>
                                        <i class="fa fa-fw fa-lock" style="color:#E33545"></i>
                                    <?php }else{ ?>
                                        <i class="fa fa-fw fa-unlock" style="color:#28A745"></i>
                                    <?php } ?>
                                </td>
                                <td>
                                    0.000
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="ibox-content">
                总收益 : 0.000.
            </div>



        </div>
    </div>
</div>
<!-- Modal -->


