<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;
use dosamigos\datetimepicker\DateTimePicker;

$this->title = '列表';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];

$starttime = '';
$endtime = '';
if (!empty($_GET['starttime'])) {
    $starttime = $_GET['starttime'];
}
if (!empty($_GET['endtime'])) {
    $endtime = $_GET['endtime'];
}
$status=['已失效','正常','已下架'];
$type=[2=>'扣币',3=>'增币'];
?>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>列表</h5>
                    <div class="ibox-tools">
                        <a style="display:none" class="btn btn-primary btn-xs" href="<?= Url::to(['edit'])?>">
                            <i class="fa fa-plus"></i>  新增套餐
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>会员id</th>
                            <th>类型</th>
                            <th>币种  </th>
                            <th>变化值</th>
                            <th>变化后余额</th>
                            <th>认购时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($data as $model){ ?>
                            <tr>
                                <td><?= $model['id'] ?></td>
                                <td><?= $model['member_id'] ?></td>
                                <td><?= $type[$model['type']] ?></td>
                                <td><?= $model['coin_symbol'] ?></td>
                                <td><?= $model['change'] ?></td>
                                <td><?= $model['balance'] ?></td>
                                <td><?= date('Y-m-d H:i:s',$model['ctime']) ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
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
