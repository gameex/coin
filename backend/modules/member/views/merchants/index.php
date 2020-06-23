<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = '商家认证';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];
?>
<div class="wrapper wrapper-content animated fadeInRight">
    <!--  search begin  -->
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>查询</h5>
                </div>
                <div class="ibox-content">
                    <form action="" method="get" class="form-horizontal" role="form" id="form">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">搜索类型</label>
                            <div class="col-sm-8 col-lg-9 col-xs-12">
                                <div class="btn-group">
                                    <a href="<?= Url::to(['index','type'=>1])?>" class="btn <?php if($type == 1){ ?>btn-primary<?php }else{ ?>btn-white<?php } ?>">id</a>
                                    <a href="<?= Url::to(['index','type'=>2])?>" class="btn <?php if($type == 2){ ?>btn-primary<?php }else{ ?>btn-white<?php } ?>">描述</a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">关键字</label>
                            <div class="col-sm-8 col-xs-12 input-group m-b">
                                <input type="hidden" class="form-control" name="type" value="<?= $type?>" />
                                <input type="text" class="form-control" name="keyword" value="<?= $keyword?>" />
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
    <!--  search end  -->
    <!--  list begin  -->
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>认证列表</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>用户ID</th>
                            <th>用户昵称</th>
                            <th>描述</th>
                            <th>提交时间</th>
                            <th>处理时间</th>
                            <th>认证状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($models as $model){ ?>
                            <tr>
                                <td><?= $model->id?></td>
                                <td><?= $model->uid?></td>
                                <td><?=$model->member['nickname']?></td>
                                <td><?= $model->describe?></td>
                                <td><?= Yii::$app->formatter->asDatetime($model->created_at)?></td>
                                <td><?= Yii::$app->formatter->asDatetime($model->updated_at)?></td>
                                <td><span style="color:<?= $status_color[$model->status]?>;"><?= $status[$model->status]?></span></td>
                                <td>
                                    <?php $auth = in_array($model->status,[2,3]) ? 'operated' : 'operation';?>
                                    <a href="#" data-id="<?=$model->id?>" data-type="<?=$auth?>" data-status="<?=$model->status?>" data-img="<?=$model->image?>" data-video="<?=$model->video?>" data-toggle="modal" data-target="#myModal" class="real_name"><span class="btn btn-info btn-sm"><?=$auth == 'operated' ? '查看' : '审核';?></span></a>&nbsp;
                                    <!--
                                    <a href="<?= Url::to(['delete','id'=>$model->id])?>"  onclick="rfDelete(this);return false;"><span class="btn btn-warning btn-sm">删除</span></a>&nbsp-->

                                    <a href="<?= Url::to(['/member/member/user-detail','id'=>$model->uid])?>" ><span class="btn btn-warning btn-sm">资产详情</span></a>&nbsp
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
<!-- 模态框（Modal） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="width: 800px">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">商家认证</h4>
            </div>
            <div class="modal-body" style="text-align: center;">
                <img id="img1" src=""  style="width: 350px">
                <!-- &nbsp;&nbsp;&nbsp; -->
                <video id="video" src="" controls="controls"  style="width: 350px">
            </div>
            <div class="modal-footer">
                <button type="button" id="default" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" id="fail" class="btn btn-danger">认证失败</button>
                <button type="button" id="success" class="btn btn-primary">认证通过</button>
            </div>
        </div>
    </div>
</div>
<!-- /.modal -->
<script>
    $(".real_name").click(function(){
        var id = $(this).attr("data-id");
        var name = $(this).attr("data-name");
        var img1 = $(this).attr("data-img");
        var video = $(this).attr("data-video");
        var type = $(this).attr("data-type");
        var status = $(this).attr("data-status");
        $("#img1").attr('src',img1);
        $("#video").attr('src',video);
        $("#myModalLabel").html(name);
        if(type == 'operation') {
            $("#fail").attr('data-id', id);
            $("#success").attr('data-id', id);
        }else{
            $("#fail").remove();
            $("#success").remove();
            $("#default").removeClass('btn-default');
            if(status == 2) {
                $("#default").html('已通过');
                $("#default").addClass('btn-primary');
            }else{
                $("#default").html('已拒绝');
                $("#default").addClass('btn-danger');
            }
        }
    });
    $(function() {
        $('#myModal').on('hide.bs.modal',
            function() {
                window.location.reload()
            })
    });
    $("#fail").click(function(){
        var id = $(this).attr("data-id");
        $.ajax({
            url:"examine",
            type:"POST",
            data:{id:id,type:'fail'},
            success : function(result) {
                result = $.parseJSON(result)
                if(result.code == 200){
                    rfSuccess('认证不通过', result.message);
                }else{
                    rfError('认证不通过', result.message);
                }
            }
        });
    });
    $("#success").click(function(){
        var id = $(this).attr("data-id");
        $.ajax({
            url:"examine",
            type:"POST",
            data:{id:id,type:'success'},
            success : function(result) {
                result = $.parseJSON(result)
                if(result.code == 200){
                    rfSuccess('认证通过', result.message);
                }else{
                    rfError('认证通过', result.message);
                }
            }
        });
    });
</script>
