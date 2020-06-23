<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = '个人钱包管理';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];
?>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>个人钱包列表</h5>
                    <div class="ibox-tools">
                        <a class="btn btn-primary btn-xs" href="<?= Url::to(['edit'])?>">
                            <i class="fa fa-plus"></i>  新增钱包
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>币种名称</th>
                                <th>余额</th>
                                <th>地址</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($personal_account as $wallet){ ?>
                            <tr>
                                <td><?= $wallet['symbol'] ?></td>
                                <td><?= $wallet['balance']??'' ?></td>
                                <td><?= $wallet['addr']??'' ?></td>
                                <td><?= isset($wallet['created_at'])?date('Y-m-d H:i:s', $wallet['created_at']):'' ?></td>
                                <td>
                                    <a href="<?= Url::to(['edit','id'=>$wallet['id']])?>"><span class="btn btn-info btn-sm">编辑</span></a>
                                    <a href="<?= Url::to(['delete','id'=>$wallet['id']])?>"><span class="btn btn-warning btn-sm">删除</span></a>
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
