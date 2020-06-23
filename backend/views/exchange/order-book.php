<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;
use api\models\ExchangeCoins;

$this->title = '订单统计';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];

$market_list = ExchangeCoins::getMarketName();
?>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>筛选条件</h5>
                </div>
                <div class="ibox-content">
                    <form action="<?= Url::to(['order-book'])?>" method="get" class="form-inline" role="form" id="form">
                        <div class="form-group">
                            <label for="" class="control-label">交易区</label>
                            <select class="form-control tpl-category-parent" name="market">
                                <option value="">请选择</option>
                                <?php foreach ($market_list as $key => $value) { ?>
                                    <option value="<?= $value?>" <?= $market == $value ? "selected":'' ?>><?= $value?></option>

                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="" class="control-label" style="margin-left: 15px">类型</label>
                            <select class="form-control tpl-category-parent" name="side">
                                <option value="">请选择</option>
                                <!-- <option value="1">Ask</option>
                                <option value="2">Bid</option> -->
                                <option value="1" <?= $side == 1 ? "selected":''?>>卖</option>
                                <option value="2" <?= $side == 2 ? "selected":''?>>买</option>
                            </select> 
                        </div>

                        <div class="form-group">
                            <label for="" class="control-label" style="margin-left: 15px">起始条目</label>
                            <input type="number" class="form-control" name="offset" value="<?= $offset?>">
                        </div>

                        <div class="form-group" style="height: 34px">
                            <label for="" class="control-label" style="margin-left: 15px">返回条目</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="limit" value="<?= $limit?>">
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
    <!--  list begin  -->
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>订单列表</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>交易区</th>
                                <th>用户</th>
                                <th>交易类型</th>
                                <th>数量</th>
                                <th>剩余</th>
                                <th>成交数量</th>
                                <th>成交金额</th>
                                <th>taker费率</th> 
                                <th>maker费率</th> 
                                <th>成交费率</th>
                                <th>价格</th>
                                <th>时间</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($code==1){ ?>
                                <?php if(count($data['orders'])){ ?>
                                    <?php foreach($data['orders'] as $key => $value){ ?>
                                        <tr>
                                            <td><?= $value['id'] ?></td>
                                            <td><?= $value['market'] ?></td>
                                            <td><?= $value['user'] ?></td>
                                            <td><?= $value['side']==1 ? '卖' : '买' ?></td>
                                            <td><?= $value['amount'] ?></td>
                                            <td><?= $value['left'] ?></td>
                                            <td><?= $value['deal_stock'] ?></td>
                                            <td><?= $value['deal_money'] ?></td>
                                            <td><?= $value['taker_fee'] ?></td>
                                            <td><?= $value['maker_fee'] ?></td>
                                            <td><?= $value['deal_fee'] ?></td>
                                            <td><?= $value['price'] ?></td>
                                            <td><?= '委托时间：'.date('Y/m/d H:i', $value['ctime']).'<br>更新时间：'.date('Y/m/d H:i', $value['mtime']) ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php }else{ ?>
                                    <div class="alert alert-warning" role="alert">
                                        暂时没有数据！
                                    </div>
                                <?php } ?>
                                    
                            <?php } ?>
                        </tbody>
                    </table>
                    <?php if($code==0){ ?>
                        <div class="alert alert-danger" role="alert">
                            数据获取失败：<?= $msg ?>
                        </div>
                    <?php } ?>
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