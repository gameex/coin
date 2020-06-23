<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = '收款类型';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];
?>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>类型列表</h5>
                    <div class="ibox-tools">
                        <a class="btn btn-primary btn-xs" href="<?= Url::to(['edit'])?>">
                            <i class="fa fa-plus"></i>  新增收款类型
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>名称</th>
                            <th>类型标识</th>
                            <th>图标</th>
                            <th>是否需要付款码</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($data as $model){ ?>
                            <tr>
                                <td><?= $model->id?></td>
                                <td><?= $model->name?></td>
                                <td><?= $model->proceeds_type?></td>
                                <td><img src="<?= $model->icon?>" style="width: 86px"></td>
                                <td><?= $model->is_qrcode == 1 ? '需要' : '不需要'?></td>
                                <td><span style="color:<?= $status_color[$model->status]?>;"><?= $status[$model->status]?></span></td>
                                <td>
                                    <?php if($model->status == 0){?>
                                        <a href="#" data-id="<?=$model->id?>" class="enable"><span class="btn btn-info btn-sm">启用</span></a>&nbsp;
                                    <?php }else{?>
                                        <a href="#" data-id="<?=$model->id?>" class="disable"><span class="btn btn-danger btn-sm">禁用</span></a>&nbsp;
                                    <?php }?>
                                    <a href="<?= Url::to(['edit','id'=>$model->id])?>""><span class="btn btn-info btn-sm">修改</span></a>&nbsp;
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
