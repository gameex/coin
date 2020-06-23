<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;
use dosamigos\datetimepicker\DateTimePicker;

$this->title = '转入查询';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];

$starttime = '';
$endtime = '';
$id = '';
if (!empty($_GET['starttime'])) {
    $starttime = $_GET['starttime'];
}
if (!empty($_GET['endtime'])) {
    $endtime = $_GET['endtime'];
}
if (!empty($_GET['id'])) {
    $id = $_GET['id'];
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
                    <form action="<?= Url::to(['member/evidence'])?>" method="get" class="form-horizontal" role="form" id="form">
                        <input type="hidden" name="id" value="<?php echo "$id";?>">
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

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">币种</label>
                            <div class="col-sm-8">
                                <div class="row row-fix tpl-category-container">
                                    <select class="form-control tpl-category-parent" name="key_type">
                                        <option value="">请选择</option>
                                        <?php foreach($symbol_list as $k=>$v): ?>
                                                <option value="<?php echo $v; ?>"><?php echo $v; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!--<div class="form-group">-->
                        <!--    <label class="col-xs-12 col-sm-2 col-md-2 control-label">总计:</label><label class="col-xs-12 col-sm-2 col-md-3 control-label" style="text-align: left;"><?php echo empty($all_num)?0:$all_num; ?>个</label>-->
                        <!--</div>-->


                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--  list begin  -->
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>日志记录</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>币种</th>
                            <th>用户ID </th>
                            <th>充值数量</th>
                            <th>支付凭证</th>
                            <th>充值时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($models as $model){ ?>
                            <tr>
                                <td><?= $model['id'] ?></td>
                                <td><?= $model['coin_name'] ?></td>
                                <td><?= $model['user_id'] ?></td>
                                <td><?= $model['coin_num'] ?></td>
                                <td><img style="width:100px;height:100px" src="<?=$model['recharge_img']?>" alt=""></td>
                                <td><?= $model['created_at'] ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-12">
                            <?= LinkPager::widget([
                                'pagination'        => $pagination,
                                'maxButtonCount'    => 5,
                                'firstPageLabel'    => "首页",
                                'lastPageLabel'     => "尾页",
                                'nextPageLabel'     => "下一页",
                                'prevPageLabel'     => "上一页",
                            ]);?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--  list end  -->
</div>