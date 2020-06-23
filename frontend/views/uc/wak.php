<?php
use yii\helpers\Url;
use api\models\ExchangeCoins;

$this->title = '成交记录';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];

$market_list = ExchangeCoins::getMarketName();
?>
<link href="/resource/frontend/css/selectFilter.css" rel="stylesheet">
<style>
	.ybc_list .ul_select {
		height: 40px;
		margin-bottom: 15px;
	}
	.ybc_list .ul_select .li_select {
		display: inline-block;
		vertical-align: middle;
		margin-right: 40px;

	}
	.ybc_list select {
		width: 122px;
	    height: 38px;
	    font-size: 14px;
	    color: #999;
	    text-align: center;

	}
	.ybc_list select option {
		height: 28px;
		/*color: #333;*/
	}
	.ybc_list .ul_select label {
		color: #999;
		font-size: 14px;
	}
	#mySelect_1,#mySelect_2,#mySelect_3 {
		width: 170px;
		position: relative;
		display: inline-block;
		vertical-align: middle;
	}
	.filter-list li a {
		text-decoration: none;
	}
	.navLift li .msgView .msglist a {
	     padding-top: 0px;
	}
	.sdmenu div a {
		text-align: left;
		padding-left: 40px;
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
	}

	.top_body .menu17 {
		display: none;
	}

	.top_body .menu18 {
		display: none;
	}
	#oImg img{
		width:172px;height:180px;margin-top: 40px;width:auto;margin-left:calc(50% - 86px);
	}
	#oImg p{
		width:100%; height:30px;text-align: center;line-height: 30px;font-size: 16px;margin-top: 30px;color:#888888;
	}
	select { text-align-last:center;}
	.ybc_list select{height:35px;}
	.ybc_list .ul_select .li_select{margin-right: 46px;}
</style>

<div id="main" style="padding: 16px 0 60px 0px">
	<div class="main_box">
	<?php echo $this->render('left.php'); ?>
	<div class="raise right clearfix">
            <div class="ybc_list">
				<div class="ybcoin clearfix">
					<h2 class="left" data-i18n="u_i_1">挖矿记录</h2>
				</div>
                                    <form action="<?= Url::to(['wak'])?>" method="get" class="form-inline" role="form" id="form">
                                      <ul class="ibox-content ul_select">
                                          <li class="li_select">
                                              <div class="form-group">
                                                  <label for="" data-i18n="u_c_2" class="control-label">交易区</label>
                                                  <select class="form-control tpl-category-parent" name="market">
                                                      <option value="">请选择</option>
                                                      <?php foreach ($market_list as $key => $value) { ?>
                                                          <option value="<?= $value?>" <?= $market == $value ? "selected":'' ?>><?= $value?></option>

                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </li>
                                          <li class="li_select">
                                              <div class="form-group">
                                                  <label for="" data-i18n="u_c_2" class="control-label">返回条目</label>
                                                  <span class="input-group">
                                                      <input type="number" class="form-control" style="width:122px;height:35px;text-align:center; line-height:38px; font-size:16px; cursor: pointer;margin-right: 0;margin-left:13px;border-style: solid; border-width: 1px;border-color:#C0C0C0;" name="limit" value="<?= $limit?>">
                                                  </span>
                                              </div>
                                          </li>
                                          <li class="li_select sure">
                                              <button class="btn btn-white" style="width:140px;height:38px;background:#368AE5;color:#ffffff;text-align: center;line-height: 38px;font-size: 16px;cursor: pointer;margin-right: 0;margin-left:13px;border-radius: 4px;"> 搜索</button>
                                          </li>
                                      </ul>
                                  </form>
				<table class="raise_list" style="border:1px solid #e1e1df;" id="Transaction" align="center" border="0" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
								<th>交易市场</th>
                                <th>数量</th>
                                <th>类型</th>
                                <th>价格</th>
                                <th>时间</th>
							<!-- <th data-i18n="u_c_3">类型</th>
							<th data-i18n="u_c_2">交易对</th>
							<th data-i18n="u_c_5">变动资金</th>
							<th data-i18n="u_c_6">变动时间</th>
							<th data-i18n="u_c_7">手续费</th> -->
						</tr>
					</thead>
					<tbody id="finlist">
							<?php if($code==1){ ?>
                                <?php if(count($data)){ ?>
                                    <?php foreach($data as $key => $value){ ?>
                                        <tr style="color: <?= $value['type'] == 'sell' ? '#009900' : '#E55600' ?>">
                                            <td><?= $market ?></td>
                                            <td><?= $value['amount'] ?></td>
                                            <td><?= $value['type'] == 'sell' ? '卖出('.$value['amount'].')' : '买入('.$value['amount'].')' ?></td>
                                            <td><?= $value['price'] ?></td>
                                            <td><?= date('Y-m-d H:i:s', $value['time']) ?></td>
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
				<div id="oImg" style="display: none">
					<img src="/resource/frontend/img/noData.png" alt="">
					<p>暂无数据</p>
				</div>
			</div>
			
		</div>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>


<script defer="defer" src="/resource/frontend/js/selectFilter.js"></script>