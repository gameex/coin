<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = '广告管理';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];
?>
<div class="wrapper wrapper-content animated fadeInRight">
    <!-- search begin   -->
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
                                    <a href="<?= Url::to(['market','type'=>200])?>" class="btn <?php if($type == 200){ ?>btn-primary<?php }else{ ?>btn-white<?php } ?>">全部</a>
                                    <a href="<?= Url::to(['market','type'=>1])?>" class="btn <?php if($type == 1){ ?>btn-primary<?php }else{ ?>btn-white<?php } ?>">币种</a>
                                    <a href="<?= Url::to(['market','type'=>2])?>" class="btn <?php if($type == 2){ ?>btn-primary<?php }else{ ?>btn-white<?php } ?>">发布者ID</a>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">关键字</label>
                            <div class="col-sm-8 col-xs-12 input-group m-b">
                                <input type="hidden" class="form-control" name="type" value="<?= $type?>" />
                                <?php if($type==200 || $type == 1 || $type == 2){?>
                                    <input type="text" class="form-control" name="keyword" value="<?= $keyword?>" />
                                <?php }?>
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
    <!-- search end   -->
    <!--  list begin  -->
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>OTC广告列表</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>币种</th> 
                            <th>类型</th>
                            <th>发布者ID</th>               
                            <th>最小限额</th>
                            <th>最大限额</th>
                            <th>价格</th>
                            <th>成交数</th>
                            <th>发布时间</th>
                            <th>广告状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($models as $model){ ?>
                            <tr>
                                <td><?= $model['id']?></td>
                                <td><?= $model['coin_name']?></td>
                                 <td><?= $side[$model['side']]?></td>
                                 <td><?= $model['uid']?></td>
                                <td><?= $model['min_num']?></td>
                                <td><?= $model['max_num']?></td>
                                <td>$<?= $model['price_usd']?></td>
                                <td><?= $model['deal_count']?></td>
                                <td><?= Yii::$app->formatter->asDatetime($model['publish_time'])?></td>
                                <td><?= $status[$model['status']]?></td>
                                <td><?php if($model['status'] == 1){?>
                                        <a href="#" data-id="<?=$model['id']?>" class="disable"><span class="btn btn-danger btn-sm">下架</span></a>&nbsp;
                                    <?php }?>
                                </td>
                                <!--<td>
                                    <a href="<?= Url::to(['edit','id'=>$model['id']])?>""><span class="btn btn-info btn-sm">修改</span></a>&nbsp;
                                    <a href="<?= Url::to(['edit','id'=>$model['id']])?>"  onclick="rfDelete(this);return false;"><span class="btn btn-warning btn-sm">删除</span></a>&nbsp
                                </td>-->
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
<script>
    
    $(".disable").click(function(){
        var id = $(this).attr("data-id");
        swal({
            title: "确定吗？",
            text: "真的要下架吗！",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "下架！",
            closeOnConfirm: false
        },function () {
            $.ajax({
                url:"enable",
                type:"POST",
                data:{id:id,status:0},
                success : function(result) {
                    result = $.parseJSON(result)
                    if(result.code == 200){
                        rfSuccess('下架', result.message);
                    }else{
                        rfError('下架', result.message);
                    }
                }
            });
        });
    });
</script>
