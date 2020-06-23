<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;
use dosamigos\datetimepicker\DateTimePicker;

$this->title = '用户信息';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];

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
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">搜索类型</label>
                            <div class="col-sm-8 col-lg-9 col-xs-12">
                                <div class="btn-group">
                                    <a href="<?= Url::to(['index','type'=>1])?>" class="btn <?php if($type == 1){ ?>btn-primary<?php }else{ ?>btn-white<?php } ?>">用户ID</a>
                                    <a href="<?= Url::to(['index','type'=>2])?>" class="btn <?php if($type == 2){ ?>btn-primary<?php }else{ ?>btn-white<?php } ?>">用户昵称</a>
                                    <a href="<?= Url::to(['index','type'=>3])?>" class="btn <?php if($type == 3){ ?>btn-primary<?php }else{ ?>btn-white<?php } ?>">手机号码</a>
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

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">总计:</label><label class="col-xs-12 col-sm-2 col-md-3 control-label" style="text-align: left;"><?php echo empty($all_num)?0:$all_num; ?>个</label>
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
                    <h5>用户信息</h5>
<!--                    <div class="ibox-tools">-->
<!--                        <a class="btn btn-primary btn-xs" href="--><?//= Url::to(['edit'])?><!--">-->
<!--                            <i class="fa fa-plus"></i>  创建用户-->
<!--                        </a>-->
<!--                    </div>-->
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
                            <!--<th>分成比例</th>-->
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($models as $model){ ?>
                            <tr>
                                <td><?= $model['id'] ?></td>
                                <td><?= $model['mobile_phone'] ?></td>
                                <td><span style="color:<?= $status_color[$model['verified_status']] ?>;"><?= $status[$model['verified_status']] ?></span></td>
                                <td><?= $model['last_ip'] ?></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <?php if ($model->status==0) { ?>
                                        <i class="fa fa-fw fa-lock" style="color:#E33545"></i>
                                    <?php }else{ ?>
                                        <i class="fa fa-fw fa-unlock" style="color:#28A745"></i>
                                    <?php } ?>
                                </td>
                                <!--<td><input type="number" data-id="<?= $model->id?>" data-type="proportion" class="form-control" value="<?= $model['proportion']?>" onblur="updateRate(this)" style="width: 100px">-->
                                </td>
                                <td>
                                    <?php if($model->verified_status == 1){?>
                                        <a href="javascript:;" data-name="<?=!empty($model->verified->real_name)?$model->verified->real_name:""?>" data-img="<?=!empty($model->verified->id_card_img)?$model->verified->id_card_img:""?>" data-img2="<?=!empty($model->verified->id_card_img2)?$model->verified->id_card_img2:""?>" data-toggle="modal" data-target="#myModal" class="real_name"><span class="btn btn-info btn-sm">实名信息</span></a>&nbsp;
                                    <?php }?>
                                    <a href="<?= Url::to(['user-detail','id'=>$model->id])?>" class="btn btn-info btn-sm">资产详情</a>

                                    <!-- 新增禁用 -->
                                    <?php if($model->status==0){ ?>
                                        <a href="<?= Url::to(['change-status','uid'=>$model->id, 'status'=>$model->status])?>" class="btn btn-primary btn-sm">启用</a>
                                    <?php }else{ ?>
                                        <a href="<?= Url::to(['change-status','uid'=>$model->id, 'status'=>$model->status])?>" class="btn btn-danger btn-sm">禁用</a>
                                    <?php } ?>

                                    <a href="<?= Url::to(['user-friend','id'=>$model->id])?>" class="btn btn-info btn-sm">推荐好友</a>
                                </td>
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
</div>
<!-- 模态框（Modal） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="width: 800px">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">实名认证</h4>
            </div>
            <div class="modal-body" style="text-align: center;">
                <img id="img1" src="" class="img-thumbnail" style="width: 350px">
                &nbsp;&nbsp;&nbsp;
                <img id="img2" src="" class="img-thumbnail" style="width: 350px">
            </div>
        </div>
    </div>
</div>

<script>
    $(".real_name").click(function(){
        var img1 = $(this).attr("data-img");
        var img2 = $(this).attr("data-img2");
        var name = $(this).attr("data-name");
        $("#img1").attr('src',img1);
        $("#img2").attr('src',img2);
        $("#myModalLabel").html(name);
    });

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
                        layer.alert('更新分成比例失败！', {icon: 2});
                        console.log(data.message);
                    }
                },
                error: function(e){
                    layer.alert('更新分成比例失败！', {icon: 2});
                    console.log(e);
                }
            });

        }else{
            layer.alert('汇率字段必须是数字！', {icon: 2});
        }
   }

</script>
