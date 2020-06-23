<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '系统推送';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>推送记录</h5>
                    <div class="ibox-tools">
                        <a class="btn btn-primary btn-xs" href="<?= Url::to(['edit'])?>">
                            <i class="fa fa-plus"></i>  添加推送
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>推送类型</th>
                            <th>推送标题</th>
                            <th>推送内容</th>
                            <th>推送状态</th>
                            <th>时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($data as $model){ ?>
                            <tr>
                                <td><?= $model->id?></td>
                                <td><?= $type[$model->type]?></td>
                                <td><?= $model->title?></td>
                                <td><?= $model->object?></td>
                                <td><span style="color:<?= $status_color[$model->status]?>;"><?= $status[$model->status]?></span></td>
                                <td><?= date('Y-m-d H:i:s', $model->add_time)?></td>
                                <td>
                                    <a href="<?= Url::to(['delete','id'=>$model->id])?>"  onclick="rfDelete(this);return false;"><span class="btn btn-warning btn-sm">删除</span></a>&nbsp;
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(".enable").click(function() {
        var id = $(this).attr("data-id");
        swal({
            title: "确定吗？",
            text: "真的要启用吗！",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "启用！",
            closeOnConfirm: false
        },function () {
            $.ajax({
                url: "enable",
                type: "POST",
                data: {id: id, status: 1},
                success: function (result) {
                    result = $.parseJSON(result)
                    if (result.code == 200) {
                        rfSuccess('启用', result.message);
                    } else {
                        rfError('启用', result.message);
                    }
                }
            });
        });
    });
    $(".disable").click(function(){
        var id = $(this).attr("data-id");
        swal({
            title: "确定吗？",
            text: "真的要禁用吗！",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "禁用！",
            closeOnConfirm: false
        },function () {
            $.ajax({
                url:"enable",
                type:"POST",
                data:{id:id,status:0},
                success : function(result) {
                    result = $.parseJSON(result)
                    if(result.code == 200){
                        rfSuccess('禁用', result.message);
                    }else{
                        rfError('禁用', result.message);
                    }
                }
            });
        });
    });
</script>
