<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;
use dosamigos\datetimepicker\DateTimePicker;

$this->title = '以太坊';
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
                    <form action="<?= Url::to(['index'])?>" method="get" class="form-horizontal" role="form" id="form">
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
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">关键字</label>
                            <div class="col-sm-8 input-group">
                                <input type="text" class="form-control" name="keyword" value="<?= $keyword?:''?>" placeholder="请输入关键字">
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
                                        <option value="">请选择分类</option>
                                        <option value="0" <?= $key_type==0?'selected':''?>>用户ID</option>
                                        <option value="1" <?= $key_type==1?'selected':''?>>发起方地址</option>
                                        <option value="2" <?= $key_type==2?'selected':''?>>接受方地址</option>
                                    </select>                                
                                </div>
                            </div>
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
                    <h5>以太坊交易记录</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>交易类型</th>
                            <th>交易TxHash</th>
                            <th>发起方(ID / 昵称)</th>
                            <th>接收方</th>
                            <th>交易数量</th>
                            <th>货币标识</th>
                            <th>交易状态</th>
                            <th>块高度</th>
                            <th>时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($models as $model){ ?>
                            <tr>
                                <td><?= $transaction_type[$model['type']] ? $transaction_type[$model['type']] : $transaction_type[0] ?></td>
                                <td>
                                    <?= $model['tx_hash'] ? \yii\helpers\Html::a($model['tx_hash'],['main/redirect','url'=>'https://etherscan.io/tx/'.$model['tx_hash']], ['target'=>'_blank']) : 'fail reason: '.$model['rpc_response'] ?>
                                </td>
                                <td title="<?= $model['from']?>">
                                    <?= \yii\helpers\Html::a($model['member_id'].' / '.$model['nickname'], ['main/redirect','url'=>'https://etherscan.io/address/'.$model['from']], ['target'=>'_blank']) ?>
                                </td>
                                <td>
                                    <?= \yii\helpers\Html::a($model['to'], ['main/redirect','url'=>'https://etherscan.io/address/'.$model['to']], ['target'=>'_blank']) ?>
                                </td>
                                <td><?= $model['value_dec']?></td>
                                <td><?= $model['coin_symbol']?></td>
                                <td><?= $type[$model['tx_status']]?></td>
                                <td><?= $model['block']?></td>
                                <td><?= Yii::$app->formatter->asDatetime($model['updated_at'])?></td>
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