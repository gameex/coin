<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = '日志记录';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];
?>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>查询</h5>
                </div>
                <div class="ibox-content">
                    <form action="<?= Url::to(['index'])?>" method="get" class="form-horizontal" role="form" id="form">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">关键字</label>
                            <div class="col-sm-8 input-group">
                                <input type="text" class="form-control" name="keyword" value="" placeholder="请输入关键字">
                                <span class="input-group-btn">
                                    <button class="btn btn-white"><i class="fa fa-search"></i> 搜索</button>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">关键字类型</label>
                            <div class="col-sm-8">
                                <div class="row row-fix tpl-category-container">
                                    <select class="form-control tpl-category-parent" name="key_type">
                                        <option value="">请选择</option>
                                        <option value="member_id">用户ID</option>
                                        <option value="type">日志类型【1：充值 / 10：转出】</option>
                                        <option value="coin_symbol">币种类型</option>
                                        <option value="addr">地址</option>
                                        <option value="detial_type">流向【exchange | chain | otc】</option>
                                    </select>                                
                                </div>
                            </div>
                        </div>
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
                            <th>类型</th>
                            <th>ID / 用户</th>
                            <th>币种</th>
                            <th>地址</th>
                            <th>变化值</th>
                            <th>余额</th>
                            <th>手续费</th>
                            <th>流向</th>
                            <th>时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($models as $model){ ?>
                            <tr>
                                <td><?= $model['id'] ?></td>
                                <td><?= in_array($model['type'], array_keys($log_type)) ? $log_type[$model['type']] : $log_type[0] ?></td>
                                <td><?= $model['member_id'].' / '.$model['nickname'] ?></td>
                                <td><?= $model['coin_symbol'] ?></td>
                                <td><?= $model['addr'] ?></td>
                                <td><?= $model['change'] ?></td>
                                <td><?= $model['balance'] ?></td>
                                <td><?= $model['fee'] ?></td>
                                <td><?= in_array($model['detial_type'], array_keys($detial_type)) ? $detial_type[$model['detial_type']] : $detial_type['other'] ?></td>
                                <td><?= date('Y-m-d H:i:s', $model['ctime']) ?></td>
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