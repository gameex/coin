<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;
use dosamigos\datetimepicker\DateTimePicker;

$this->title = '提现申请';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];

$starttime = '';
$endtime = '';
$id = '';
if (!empty($_GET['starttime'])) {
    $starttime = $_GET['starttime'];
}
if (!empty($_GET['endtime'])) {
    $endtime = $_GET['endtime'];
}
if (!empty($_GET['id'])) {
    $id = $_GET['id'];
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
                    <form action="<?= Url::to(['withdraw'])?>" method="get" class="form-horizontal" role="form" id="form">
                        <input type="hidden" name="id" value="<?php echo "$id";?>">
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
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">币种</label>
                            <div class="col-sm-8">
                                <div class="row row-fix tpl-category-container">
                                    <select class="form-control tpl-category-parent" name="key_type">
                                        <option value="">请选择</option>
                                        <?php foreach($symbol_list as $k=>$v): ?>
                                                <option value="<?php echo $v; ?>"><?php echo $v; ?></option>
                                        <?php endforeach; ?>
                                    </select>                                
                                </div>
                            </div>
                        </div>


                        <!-- <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">关键字</label>
                            <div class="col-sm-8 input-group">
                                <input type="text" class="form-control" name="keyword" value="" placeholder="请输入关键字">
                                <span class="input-group-btn">
                                    <button class="btn btn-white"><i class="fa fa-search"></i> 搜索</button>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">关键字类型</label>
                            <div class="col-sm-8">
                                <div class="row row-fix tpl-category-container">
                                    <select class="form-control tpl-category-parent" name="key_type">
                                        <option value="">请选择</option>
                                        <option value="member_id">用户ID</option>
                                        <option value="coin_symbol">币种类型</option>
                                        <option value="addr">提现地址</option>
                                        <option value="status">审核状态</option>
                                    </select>
                                </div>
                            </div>
                        </div> -->

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">总计:</label><label class="col-xs-12 col-sm-2 col-md-3 control-label" style="text-align: left;"><?php echo empty($all_num)?0:$all_num; ?>个</label>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--  list begin  -->
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>申请列表</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>申请人</th>
                                <th>币种</th>
                                <th>提现金额</th>
                                <th>提现地址</th>
                                <th>Memo备注</th>
                                <th>状态</th>
                                <th>结果</th>
                                <th>申请时间</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($apply)){ ?>
                                <?php foreach ($apply as $key => $value) { ?>
                                    <tr>
                                        <td><?= $value['member_id'].' / '.$value['nickname'] ?></td>
                                        <td><?= $value['coin_symbol'] ?></td>
                                        <td><?=
                                            $value['value_dec'].($value['withdraw_fee']?('(手续费'.$value['withdraw_fee'].($value['current']?('|矿工费'.$value['current']):'').')'):'')
                                            ?>
                                        </td>
                                        <td>
                                            <?=
                                                $value['type'] == 0 ? $value['addr'] : $value['phone'].' / '.$value['truename'].' / '.$value['wallet_name'].' / '.$value['wallet_card']
                                            ?>
                                        </td>
                                        <td><?= $value['description'] ?></td>
                                        <td><?= $withdraw_status[$value['status']] ?></td>
                                        <td><?= $value['error_message'] ?: $value['tx_hash'] ?></td>
                                        <td><?= date('Y/m/d H:i', $value['created_at']) ?></td>
                                        <td>
                                            <a href="<?= Url::to(['apply-yes','id'=>$value['id']])?>" class="btn btn-info btn-sm <?= $value['status']!=1?'disabled':'' ?>">通过</a>&nbsp;
                                            <a href="<?= Url::to(['apply-no','id'=>$value['id']])?>" class="btn btn-warning btn-sm <?= $value['status']!=1?'disabled':'' ?>">拒绝</a>&nbsp;
                                            <a href="<?= Url::to(['del-apply','id'=>$value['id']])?>" class="btn btn-danger btn-sm <?= $value['status']!=1?'disabled':'' ?>">删除</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php }else{ ?>
                                <tr>
                                    <td colspan="7" class="text-center" style="padding-top: 15px">暂无数据！</td>
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
    <!--  list end  -->
</div>