<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = '订单管理';
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
                                    <a href="<?= Url::to(['order','type'=>200])?>" class="btn <?php if($type == 200){ ?>btn-primary<?php }else{ ?>btn-white<?php } ?>">全部</a>
                                    <a href="<?= Url::to(['order','type'=>1])?>" class="btn <?php if($type == 1){ ?>btn-primary<?php }else{ ?>btn-white<?php } ?>">币种</a>
                                    
                                    <a href="<?= Url::to(['order','type'=>2])?>" class="btn <?php if($type == 2){ ?>btn-primary<?php }else{ ?>btn-white<?php } ?>">卖家ID</a>
                                    <a href="<?= Url::to(['order','type'=>3])?>" class="btn <?php if($type == 3){ ?>btn-primary<?php }else{ ?>btn-white<?php } ?>">买家ID</a>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">关键字</label>
                            <div class="col-sm-8 col-xs-12 input-group m-b">
                                <input type="hidden" class="form-control" name="type" value="<?= $type?>" />
                                <?php if($type==200 || $type == 1 || $type == 2 || $type == 3){?>
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
                    <h5>OTC订单列表</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>币种</th> 
                            <th>类型</th>           
                            <th>卖家ID</th>
                            <th>买家ID</th>
                            <th>价格</th>
                            <th>数量</th>
                            <th>总价</th>
                            <th>订单时间</th>
                            <th>订单状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($models as $model){ ?>
                            <tr>
                            	<td><?= $model['id']?></td>
                                <td><?= $model['coin_name']?></td>
                                <td><?= $side[$model['side']]?></td>
                                <td><?= $model['seller_uid']?></td>
                                <td><?= $model['buyer_uid']?></td>
                                <td><?= $model['price_usd']?></td>
                                <td><?= $model['amount']?></td>
                                <td><?= $model['total_price_usd']?></td>
                                <td><?= $model['order_time']?></td>
                                <td><?= $status[$model['status']]?></td>
                                <td><?php if($model['status'] == 2 || ($model['status']>1 && (in_array($model['id'],$appeal_order_ids) && $appeals[$model['id']] == 1))){?>
                                        <a href="#" data-id="<?=$model['id']?>" class="disable"><span class="btn btn-danger btn-sm">取消交易</span></a>&nbsp;
                                    <?php }?>
                                    <!-- 新增放币操作 -->
                                    <?php if($model['status'] == 3){ ?>
                                        <a href="<?= Url::to(['put-money','id'=>$model['id']])?>"><span class="btn btn-info btn-sm">放币</span></a>&nbsp;
                                    <?php } ?>
                                </td>
                                <!--<td>
                                    <a href="<?= Url::to(['edit','id'=>$model['id']])?>""><span class="btn btn-info btn-sm">修改</span></a>&nbsp;
                                    <a href="<?= Url::to(['delete','id'=>$model['id']])?>"  onclick="rfDelete(this);return false;"><span class="btn btn-warning btn-sm">删除</span></a>&nbsp
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
            text: "真的要取消交易吗！",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "取消交易",
            closeOnConfirm: false
        },function () {
            $.ajax({
                url:"disable",
                type:"POST",
                data:{id:id,status:0},
                success : function(result) {
                    result = $.parseJSON(result)
                    if(result.code == 200){
                        rfSuccess('取消交易', result.message);
                    }else{
                        rfError('取消交易', result.message);
                    }
                }
            });
        });
    });
</script>