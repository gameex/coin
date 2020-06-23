<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '币种列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- 审核模态框（Modal） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="width: 500px">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">兑换审核</h4>
            </div>

            <div class="modal-body" style="text-align: center;">
                <div id="coin_text_modal" >

                </div>
            </div>
        </div>
    </div>
</div>
<!-- 审核模态框（Modal） end-->
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
                                    <a href="<?= Url::to(['index','type'=>200])?>" class="btn <?php if($type == 200){ ?>btn-primary<?php }else{ ?>btn-white<?php } ?>">全部</a>
                                    <a href="<?= Url::to(['index','type'=>1])?>" class="btn <?php if($type == 1){ ?>btn-primary<?php }else{ ?>btn-white<?php } ?>">币种名称</a>
                                    <a href="<?= Url::to(['index','type'=>2])?>" class="btn <?php if($type == 2){ ?>btn-primary<?php }else{ ?>btn-white<?php } ?>">币种</a>
                                    <a href="<?= Url::to(['index','type'=>3])?>" class="btn <?php if($type == 3){ ?>btn-primary<?php }else{ ?>btn-white<?php } ?>">状态</a>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">关键字</label>
                            <div class="col-sm-8 col-xs-12 input-group m-b">
                                <input type="hidden" class="form-control" name="type" value="<?= $type?>" />
                                <?php if($type==200 || $type == 1 ){?>
                                    <input type="text" class="form-control" name="keyword" value="<?= $keyword?>" />
                                <?php }elseif ($type == 2){?>
                                    <select name="keyword" id="" class="form-control">
                                        <option value="" <?=$keyword == '' ? 'selected' : ''?>>全部</option>
                                        <?php foreach($symbol as $v){?>
                                            <option value="<?=$v->symbol?>" <?=$keyword == $v->symbol ? 'selected' : ''?>><?=$v->symbol?></option>
                                        <?php }?>
                                    </select>
                                <?php }elseif ($type == 3){?>
                                    <select name="keyword" id="" class="form-control">
                                        <option value="" <?=$keyword == '' ? 'selected' : ''?>>全部</option>
                                        <option value="1" <?=$keyword == 1 ? 'selected' : ''?>>启用</option>
                                        <option value="500" <?=$keyword == 500 ? 'selected' : ''?>>禁用</option>

                                    </select>
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
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>币种列表</h5>
                    <div class="ibox-tools">
                        <a class="btn btn-primary btn-xs" href="<?= Url::to(['edit'])?>">
                            <i class="fa fa-plus"></i>  新增币种
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <!-- <th>#</th> -->
                            <th>币种</th>
                            <th>名称</th>
                            <th>图标</th>
                            <!-- <th>父级币种</th> -->
                            <!-- <th>类型</th> -->
                            <th>单位</th>
                            <th>合约地址</th>
                            <th>代币区分</th>
                            <th>小数位数</th>
                            <th>最小提现数</th>
                            <th>提现手续费</th>
                            <th>排序</th>
                            <th>美元汇率</th>
                            <th>人民币汇率</th>
                            <th>充值状态</th>
                            <th>提现状态</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($data as $model){ ?>
                            <tr>
                                <!-- <td><?= $model->id?></td> -->
                                <td><?= $model->symbol?></td>
                                <td><?= $model->coin_name?></td>
                                <td><img src="<?= $model->icon?>" style="width: 45px;height: 45px"></td>
                                <td><?= $model->unit?></td>
                                <td><?= $model->ram_token_addr ? \yii\helpers\Html::a($model->ram_token_addr, ['main/redirect','url'=>'https://etherscan.io/token/'.$model->ram_token_addr], ['target'=>'_blank']) : '' ?></td>

                                <td><?= $ram_status[$model->ram_status]?></td>
                                <td><?= $model->ram_token_decimals?></td>
                                <td><?= $model->limit_amount?></td>
                                <td><?= $model->withdraw_fee?></td>
                                <td><input type="number" data-id="<?= $model->id?>" class="form-control" value="<?= $model['listorder']?>" onblur="orderUpdate(this)" style="width: 70px"></td>

                                <td><input type="number" data-id="<?= $model->id?>" data-type="usd" class="form-control" value="<?= $model['usd']?>" onblur="updateRate(this)" style="width: 100px"></td>

                                <td><input type="number" data-id="<?= $model->id?>" data-type="cny" class="form-control" value="<?= $model['cny']?>" onblur="updateRate(this)" style="width: 100px"></td>

                                <td>
                                    <?php if($model->recharge_enable == 1){?>
                                        <i class="fa fa-check" style="color: #28A745"></i>
                                    <?php }else{?>
                                        <i class="fa fa-close" style="color: #E33545"></i>
                                    <?php }?>                                    
                                </td>

                                <td>
                                    <?php if($model->withdraw_enable == 1){?>
                                        <i class="fa fa-check" style="color: #28A745"></i>
                                    <?php }else{?>
                                        <i class="fa fa-close" style="color: #E33545"></i>
                                    <?php }?>                                     
                                </td>

                                <td>
                                    <?php if($model->enable == 1){?>
                                        <i class="fa fa-check" style="color: #28A745"></i>
                                    <?php }else{?>
                                        <i class="fa fa-close" style="color: #E33545"></i>
                                    <?php }?>
                                </td>
                                <td>
                                    <a href="#" data-id="<?=$model->id?>" data-name="<?=$model->coin_name?>"  data-text="<?=$model->coin_text?>" data-toggle="modal" data-target="#myModal" class="coin_text"><span class="btn btn-info btn-sm">简介</span></a>
                                    <a href="<?= Url::to(['edit','id'=>$model->id])?>""><span class="btn btn-info btn-sm">修改</span></a>

                                    <a href="<?= Url::to(['delete','id'=>$model->id])?>"  onclick="rfDelete(this);return false;"><span class="btn btn-warning btn-sm">删除</span></a>
                                    <?php if($model->enable == 0){?>
                                        <a href="#" data-id="<?=$model->id?>" class="enable"><span class="btn btn-info btn-sm">启用</span></a>&nbsp;
                                    <?php }else{?>
                                        <a href="#" data-id="<?=$model->id?>" class="disable"><span class="btn btn-danger btn-sm">禁用</span></a>&nbsp;
                                    <?php }?>
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
</div>




<script>
    function orderUpdate($data){
        var update_id     = $data.getAttribute('data-id');
        var update_orders =  Number($data.value); 
        // console.log(update_id+'----'+update_orders);
        if (!isNaN(update_orders)){
            $.ajax({
                type:"post",
                url:"<?= Url::to(['change-order'])?>",
                dataType: "json",
                data: {id:update_id,orders:update_orders},
                success: function(data){
                    if(data.code == 200) {
                        window.location.reload();
                        // layer.alert('更新排序字段成功!', {icon: 1}, function(){
                        //     window.location.reload();
                        // });
                    }else{
                        layer.alert('更新排序字段失败！', {icon: 2});
                        console.log(data.message);
                    }
                },
                error: function(e){
                    layer.alert('更新排序字段失败！', {icon: 2});
                    console.log(e);
                }
            });

        }else{
            layer.alert('排序字段必须是数字！', {icon: 2});
        }
   }

   // 更新费率
   function updateRate($data)
   {
        var update_id   = $data.getAttribute('data-id');
        var update_rate =  Number($data.value); 
        var update_type = $data.getAttribute('data-type');
        if (!isNaN(update_rate)){
            $.ajax({
                type:"post",
                url:"<?= Url::to(['change-rate'])?>",
                dataType: "json",
                data: {id:update_id,rate:update_rate,type:update_type},
                success: function(data){
                    if(data.code == 200) {
                        window.location.reload();
                        // layer.alert('更新排序字段成功!', {icon: 1}, function(){
                        //     window.location.reload();
                        // });
                    }else{
                        layer.alert('更新汇率失败！', {icon: 2});
                        console.log(data.message);
                    }
                },
                error: function(e){
                    layer.alert('更新汇率失败！', {icon: 2});
                    console.log(e);
                }
            });

        }else{
            layer.alert('汇率字段必须是数字！', {icon: 2});
        }
   }

    $(".coin_text").click(function () {
        var textarea_html = $(this).attr('data-text');

        var coin_name = $(this).attr('data-name');
        $("#myModalLabel").html(coin_name);
        $("#coin_text_modal").html(textarea_html)
    })


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
