<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = '收款信息';
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
                    <form action="" method="get" class="form-horizontal" role="form" id="form">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">搜索类型</label>
                            <div class="col-sm-8 col-lg-9 col-xs-12">
                                <div class="btn-group">
                                    <a href="<?= Url::to(['index','type'=>1])?>" class="btn <?php if($type == 1){ ?>btn-primary<?php }else{ ?>btn-white<?php } ?>">收款类型</a>
                                    <a href="<?= Url::to(['index','type'=>2])?>" class="btn <?php if($type == 2){ ?>btn-primary<?php }else{ ?>btn-white<?php } ?>">收款账号</a>
                                    <a href="<?= Url::to(['index','type'=>3])?>" class="btn <?php if($type == 3){ ?>btn-primary<?php }else{ ?>btn-white<?php } ?>">开户行</a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 control-label">关键字</label>
                            <div class="col-sm-8 col-xs-12 input-group m-b">
                                <input type="hidden" class="form-control" name="type" value="<?= $type?>" />
                                <?php if($type == 2){?>
                                    <input type="text" class="form-control" name="keyword" value="<?= $keyword?>" />
                                <?php }elseif($type==3){?>
                                    <select name="keyword" class="form-control">
                                        <option value="" <?=$keyword == '' ? 'selected' : ''?>>全部</option>
                                        <option value="中国工商银行" <?=$keyword == '中国工商银行' ? 'selected' : ''?>>中国工商银行</option>
                                        <option value="中国建设银行"  <?=$keyword == '中国建设银行' ? 'selected' : ''?>>中国建设银行</option>
                                        <option value="招商银行"  <?=$keyword == '招商银行' ? 'selected' : ''?>>招商银行</option>
                                    </select>
                                <?php }else{?>
                                    <select name="keyword" class="form-control">
                                        <option value="" <?=$keyword == '' ? 'selected' : ''?>>全部</option>
                                        <option value="alipay" <?=$keyword == 'alipay' ? 'selected' : ''?>>支付宝</option>
                                        <option value="wxpay"  <?=$keyword == 'wxpay' ? 'selected' : ''?>>微信</option>
                                        <option value="bank"  <?=$keyword == 'bank' ? 'selected' : ''?>>银行卡</option>
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
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>收款信息</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>用户ID</th>
                            <th>昵称</th>
                            <th>收款类型</th>
                            <th>账号</th>
                            <th>收款码</th>
                            <th>持卡人</th>
                            <th>开户行</th>
                            <th>icon</th>
                            <th>创建时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($models as $model){ ?>
                            <tr>
                                <td><?= $model->member_id?></td>
                                <td><?= $model->user->nickname?></td>
                                <td><?= $proceeds_type[$model->proceeds_type]?></td>
                                <td><?= $model->account?></td>
                                <td><img src="<?= $model->qrcode?>" style="width: 86px"></td>
                                <td><?= $model->username?></td>
                                <td><?= $model->bank_name?></td>
                                <td><img src="<?= $model->icon?>" style="width: 86px"></td>
                                <td><?= $model->ctime?></td>
                                <td>
                                    <a href="<?= Url::to(['delete','id'=>$model->id])?>"  onclick="rfDelete(this);return false;"><span class="btn btn-warning btn-sm">删除</span></a>&nbsp
                                </td>
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
