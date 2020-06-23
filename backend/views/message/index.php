<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = '消息列表';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];
?>
<div class="wrapper wrapper-content animated fadeInRight">
    <!--  list begin  -->
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>消息列表</h5>
                    <div class="ibox-tools">
                        <a class="btn btn-primary btn-xs" href="<?= Url::to(['edit'])?>">
                            <i class="fa fa-plus"></i>  新增消息
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>标题</th>
                            <th>内容</th>
                            <th>类型</th>
                            <th>用户</th>
                            <th>时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($models as $model){ ?>
                            <tr>
                                <td><?= $model->id?></td>
                                <td><?= $model->title?></td>
                                <td><?= $model->content?></td>
                                <td><?= $type[$model->type]?></td>
                                <td><?= empty($model->user) ? '' : $model->user->nickname?></td>
                                <td><?= Yii::$app->formatter->asDatetime($model->add_time)?></td>
                                <td>
                                    <a href="<?= Url::to(['edit','id'=>$model->id])?>""><span class="btn btn-info btn-sm">修改</span></a>&nbsp;
                                    <a href="<?= Url::to(['delete','id'=>$model->id])?>"  onclick="rfDelete(this);return false;"><span class="btn btn-warning btn-sm">删除</span></a>&nbsp
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-12">
                            <?= LinkPager::widget([
                                'pagination'        => $Pagination,
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