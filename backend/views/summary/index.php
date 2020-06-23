<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = '链上资金列表';
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
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">关键字类型</label>
                            <div class="col-sm-8">
                                <div class="row row-fix tpl-category-container">
                                    <select class="form-control tpl-category-parent" name="key_type">
                                        <option value="">请选择</option>
                                        <option value="uid">用户ID</option>
                                        <option value="coin_symbol">币种标识</option>
                                        <option value="addr">地址</option>
                                    </select>                                
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">关键字</label>
                            <div class="col-sm-8 input-group">
                                <input type="text" class="form-control" name="keyword" value="" placeholder="请输入关键字">
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

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>列表</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>用户信息</th>
                                <th>币种名称</th>
                                <th>余额</th>
                                <th>地址</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($member_wallets as $wallet){ ?>
                            <tr>
                                <td><?= $wallet['uid'] ?> / <?= $wallet['nickname'] ?></td>
                                <td><?= substr($wallet['coin_symbol'], 1, -1) ?></td>
                                <td><?= $wallet['balance'] ?></td>
                                <td style="line-height: 35px"><?= $wallet['addr'] ?></td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" data-amount="<?=$wallet['balance']?>" data-type="system" data-id="<?= $wallet['id'] ?>" data-symbol="<?= substr($wallet['coin_symbol'], 1, -1) ?>" data-toggle="modal" data-target="#myModal">汇总<i class="fa fa-long-arrow-right fa-fw"></i>系统账户</button>
                                    <button type="button" class="btn btn-info btn-sm" data-amount="<?=$wallet['balance']?>" data-type="personal" data-id="<?= $wallet['id'] ?>" data-symbol="<?= substr($wallet['coin_symbol'], 1, -1) ?>" data-toggle="modal" data-target="#myModal">汇总<i class="fa fa-long-arrow-right fa-fw"></i>个人账户</button>
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
                    <?php if(!$member_wallets){ ?>
                        <div class="alert alert-warning">
                            暂无数据！
                        </div>
                    <?php } ?>
                    <div class="text-warning">
                        <i class="fa fa-exclamation-triangle"></i>提示：
                        列表汇总中只展示余额不为0的钱包详情！
                    </div>
                </div>
                    
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= Url::to(['update-balance'])?>" method="post" class="form-horizontal" role="form" id="form">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">温馨提示</h4>
                </div>
                <div class="modal-body">
                    确认将
                    余额：<input type="text" style="width: 300px;margin-bottom:10px;" name="value_amount" class="value_amount" value=""><br>
                    汇总到<i class="fa fa-long-arrow-right fa-fw"></i>
                    <span class="summary_type"></span>
                    <span class="summary_addr"></span>
                </div>
                <div class="modal-footer">
                    <!-- 提交区 -->
                    <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary summary_yes">确认</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#myModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            // 获取数据
            var value_amount = button.data('amount');console.log(value_amount);
            var type            = button.data('type');
            var id              = button.data('id');
            var symbol          = button.data('symbol');
            var system_wallet   = <?= json_encode($system_wallet) ?>;
            var personal_wallet = <?= json_encode($personal_wallet) ?>;

            var modal = $(this);
            modal.find('.value_amount').val(value_amount);
            if (type == 'system') {
                modal.find('.summary_type').text('系统账户');
                if (typeof(system_wallet[symbol]) == 'undefined') {
                    modal.find('.summary_addr').text('（系统账户中没有该币种账户！）');
                }else{
                    modal.find('.summary_addr').text('（'+system_wallet[symbol]+'）');
                }
            }else{
                modal.find('.summary_type').text('个人账户');
                if (typeof(personal_wallet[symbol]) == 'undefined') {
                    modal.find('.summary_addr').text('（个人账户中没有该币种账户！）');
                }else{
                    modal.find('.summary_addr').text('（'+personal_wallet[symbol]+'）');
                }
                
            }

            modal.find('.summary_yes').click(function(){
                var index = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
                $.ajax({
                    url: '<?= Url::to(['summary-assets'])?>',
                    type: 'POST',
                    data: {
                        'id': id,
                        'type': type,
                        'symbol': symbol,
                        'value_amount':  modal.find('.value_amount').val(),
                    },
                    dataType: 'json',
                    success: function(data){
                        layer.close(index);
                        if (data.code == 200) {
                            layer.alert(data.message, {icon: 1}, function(){
                                window.location.reload();
                            });
                            // console.log('success:'+JSON.stringify(data));
                        }else{
                            // layer.alert('生成'+type+'地址失败:'+data.message, {icon: 2});
                            layer.alert(data.message, {icon: 2}, function(){
                                //window.location.reload();
                            });
                        }
                    },
                    error: function(e){
                        layer.close(index);
                        layer.alert('失败', {icon: 2}, function(){
                            console.log('error:'+JSON.stringify(e));
                            //window.location.reload();
                        });
                    }
                });
            });
        });
    });
</script>
