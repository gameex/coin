<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;
use backend\assets\HeadJsAsset;

$this->title = '资产详情';
$this->params['breadcrumbs'][] = ['label' => '用户信息', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title];

HeadJsAsset::register($this);
$this->registerJsFile("/resource/backend/js/bootstrap.min.js?v=4.0.0");
?>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>基本信息</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-4">
                            <p><span style="display: inline-block;min-width: 80px;">ID：</span><?= $user->id ?></p>
                            <p><span style="display: inline-block;min-width: 80px;">昵称：</span><?= $user->nickname ?></p>
                            <!-- <p><span style="display: inline-block;min-width: 80px;">角色：</span><?= $user->type==10?'管理员':'普通会员' ?></p> -->
                            <p><span style="display: inline-block;min-width: 80px;">邮箱：</span><?= $user->email ?></p>
                            <p><span style="display: inline-block;min-width: 80px;">手机号：</span><?= $user->mobile_phone ?></p>
                            <p><span style="display: inline-block;min-width: 80px;">注册时间：</span><?= date('Y-m-d H:i:s', $user->created_at) ?></p>
                        </div>
                        <div class="col-md-4">
                            <p><span style="display: inline-block;min-width: 80px;">用户状态：</span><?= $user->status==0?'禁用':'正常' ?></p>
                            <p><span style="display: inline-block;min-width: 80px;">实名认证：</span><?= $user->verified_status==1?'<i class="fa fa-check fa-fw" style="color:#28A745"></i>已认证':'<i class="fa fa-warning fa-fw" style="color:#FFC157"></i>未认证' ?></p>
                            <p><span style="display: inline-block;min-width: 80px;">绑定手机：</span><?= $user->mobile_phone_status==1?'<i class="fa fa-check fa-fw" style="color:#28A745"></i>已绑定':'<i class="fa fa-warning fa-fw" style="color:#FFC157"></i>未绑定' ?></p>
                        </div>
                        <div class="col-md-4">
                            <p><span style="display: inline-block;min-width: 80px;">访问次数：</span><?= $user->visit_count ?></p>
                            <p><span style="display: inline-block;min-width: 80px;">最近登录：</span><?= date('Y-m-d H:i:s', $user->last_time) ?></p>
                            <p><span style="display: inline-block;min-width: 80px;">最近IP：</span><?= $user->last_ip ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>用户资产</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>图标</th>
                                <th>币种类型</th>
                                <th>可用</th>
                                <th>冻结</th>
                                <th>地址</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($balance_all)){ ?>
                                <?php foreach($balance_all as $key => $value){ ?>
                                    <tr>
                                        <th><img src="<?= $value['icon'] ?>" style="width: 45px;height: 45px"></th>
                                        <td><?= $value['name'] ?></td>
                                        <td>
                                            <span style="display: inline-block;min-width: 80px;">总计：</span><?= $value['available'] ?><br>
                                            <span style="display: inline-block;min-width: 80px;">交易所：</span><?= $value['exchange_available'] ?><br>
                                            <span style="display: inline-block;min-width: 80px;">银行：</span><?= $value['bank_balance'] ?>
                                        </td>
                                        <td>
                                            <span style="display: inline-block;min-width: 80px;">交易所：</span><?= $value['exchange_freeze'] ?><br>
                                            <span style="display: inline-block;min-width: 80px;">OTC：</span><?= $value['oct_freeze'] ?><br>
                                            <span style="display: inline-block;min-width: 80px;">提现：</span><?= $value['withdraw_freeze'] ?><br>
                                            <span style="display: inline-block;min-width: 80px;">提现中：</span><?= $value['top_up_freeze'] ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php }else{ ?>
                                <tr>
                                    <td colspan="6" class="text-center">暂无数据！</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>交易记录</h5>
                </div>
                <div class="ibox-content">
                    <form action="<?= Url::to(['user-detail'])?>" method="get" class="form-inline" role="form" id="form">
                        <div class="form-group">
                            <input type="hidden" value="<?= $user->id ?>" name="id">
                            <select class="form-control tpl-category-parent" name="key_type">
                                <option value="">请输入关键字类型</option>
                                <option value="type">日志类型【1：充值 / 10：转出】</option>
                                <option value="coin_symbol">币种类型</option>
                                <option value="addr">地址</option>
                                <option value="detial_type">流向【exchange：交易所/chain：链上/system：系统】</option>
                            </select>   
                        </div>

                        <div class="form-group" style="height: 34px">
                            <!-- <label for="" class="control-label" style="margin-left: 15px">返回条目</label> -->
                            <div class="input-group">
                                <input type="text" class="form-control" name="keyword" placeholder="请输入关键字">
                                <span class="input-group-btn">
                                    <button class="btn btn-white"><i class="fa fa-search"></i> 搜索</button>
                                </span>
                            </div>
                        </div>
                        
                    </form><hr>

                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <!-- <th>#</th> -->
                                <th>交易类型</th>
                                <th>币种</th>
                                <th>货币地址</th>
                                <th>变化值</th>
                                <th>余额</th>
                                <th>手续费</th>
                                <th>流向</th>
                                <th>时间</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($balance_logs)){ ?>
                                <?php foreach($balance_logs as $key => $value){ ?>
                                    <tr>
                                        <!-- <td><?= $value->id ?></td> -->
                                        <td><?=  in_array($value->type, array_keys($transaction_type)) ? $transaction_type[(int)$value->type] : $transaction_type[0] ?></td>
                                        <td><?= $value->coin_symbol ?></td>
                                        <td><?= $value->addr ?></td>
                                        <td><?= $value->change ?></td>
                                        <td><?= $value->balance ?></td>
                                        <td><?= $value->fee ?></td>
                                        <td><?=  in_array($value->detial_type, array_keys($detial_type)) ? $detial_type[$value->detial_type] : $detial_type['default'] ?></td>
                                        <td><?= date('Y-m-d H:i:s', $value->ctime) ?></td>
                                    </tr>
                                <?php } ?>
                            <?php }else{ ?>
                                <tr>
                                    <td colspan="8" class="text-center">暂无数据！</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-12">
                            <?= LinkPager::widget([
                                'pagination'        => $pages,
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
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= Url::to(['update-balance'])?>" method="post" class="form-horizontal" role="form" id="form">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">充币</h4>
                </div>
                <div class="modal-body">
                    <!-- 表单区 -->
                    <div class="form-group">
                        <label for="" class="control-label col-sm-2">币种</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control coin_name" name="coin_name" placeholder="币种" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label col-sm-2">充值地址</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control addr" name="addr" placeholder="充值地址" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label col-sm-2 type_desc">充值金额</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control value" name="value" placeholder="请输入金额">
                        </div>
                    </div>
                    <!-- 用户id -->
                    <input type="hidden" value="0" name="member_id" class="member_id">
                    <!-- 资产变动类型【1：充值，10：扣除】 -->
                    <input type="hidden" value="1" name="change_type" class="change_type">
                </div>
                <div class="modal-footer">
                    <!-- 提交区 -->
                    <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">确认</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#myModal').on('show.bs.modal', function (event) 
    {
        var button = $(event.relatedTarget);
        // 获取数据
        var title     = button.data('title');
        var coin      = button.data('coin');
        var addr      = button.data('addr');
        var member_id = button.data('user');
        var money     = button.data('money');

        var modal = $(this);
        modal.find('.modal-title').text(title);
        if (title == '充币') {
            modal.find('.type_desc').text('充值金额');
            modal.find('.change_type').val(1);
        }
        if (title == '扣币') {
            modal.find('.type_desc').text('扣除金额');
            modal.find('.change_type').val(10);
        }
        if (title == '冻结') {
            modal.find('.type_desc').text('冻结金额');
            modal.find('.change_type').val(21);
        }
        if (title == '解冻') {
            modal.find('.type_desc').text('解冻金额');
            modal.find('.change_type').val(22);
        }        
        modal.find('.coin_name').val(coin);
        modal.find('.addr').val(addr);
        modal.find('.member_id').val(member_id);
    })
    // 生成地址
    function generate_address(type)
    {
        $.ajax({
            url: '<?= Url::to(['generate-address'])?>',
            type: 'POST',
            data: {
                'user_id': '<?= $user->id?>',
                'coin_symbol': type,
                'generate': 1,
                'chain_network': 'main_network',
            },
            dataType: 'json',
            success: function(data){
                if (data.code == 200) {
                    layer.alert('生成'+type+'地址成功', {icon: 1}, function(){
                        window.location.reload();
                    });
                    // console.log('success:'+JSON.stringify(data));
                }else{
                    // layer.alert('生成'+type+'地址失败:'+data.message, {icon: 2});
                    layer.alert(data.message, {icon: 2});
                    console.log('error:'+JSON.stringify(data));
                }
            },
            error: function(e){
                layer.alert('生成'+type+'地址失败', {icon: 2});
                console.log('error:'+JSON.stringify(e));
            }
        });
    }
</script>