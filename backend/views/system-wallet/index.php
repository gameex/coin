<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = '系统钱包管理';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];
?>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>系统钱包列表</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <!-- <th>网络</th> -->
                            <th>币种名称</th>
                            <th>余额</th>
                            <th>账户名称</th>
                            <th>地址</th>
                            <th>创建时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($sys_wallets as $wallet){ ?>
                            <tr>
                                <td><?= $wallet['symbol'] ?></td>
                                <td><?= $wallet['balance']??'' ?></td>
                                <td><?= $wallet['account_name']??'' ?></td>
                                <td><?= $wallet['addr']??'' ?></td>
                                <td><?= isset($wallet['created_at'])?date('Y-m-d H:i:s', $wallet['created_at']):'' ?></td>
                                <td>
                                    <?php if(empty($wallet['addr'])){ ?>
                                        <a href="<?= Url::to(['generate','coin_id'=>$wallet['coin_id']]) ?>"><span class="btn btn-info btn-sm">生成</span></a>
                                    <?php }else{ ?>
                                        <a href="<?= Url::to(['delete','id'=>$wallet['id']])?>"><span class="btn btn-warning btn-sm">删除</span></a>
                                        <a href="<?= Url::to(['update-balance','id'=>$wallet['id']])?>" class="btn btn-info btn-sm"><i class="fa fa-refresh fa-fw"></i>更新余额</a>
                                    <?php } ?>

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
                    <!-- 提示信息 -->
                    <div class="text-warning">
                        <i class="fa fa-exclamation-triangle"></i>温馨提示：
                        由于资金实时变动，余额更新速度可能会出现延迟，可点击<b><i>&nbsp;更新余额&nbsp;</i></b>获取链上最新余额信息！
                    </div>
                </div>
                    
            </div>
        </div>
    </div>
</div>
