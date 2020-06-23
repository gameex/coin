<?php
use yii\helpers\Url;
use api\models\ExchangeCoins;

$this->title = '成交记录';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];

$market_list = ExchangeCoins::getMarketName();
?>
<link href="/resource/frontend/css/selectFilter.css" rel="stylesheet">
<link href="/resource/frontend/css/uc.css" rel="stylesheet">
<div id="main">
	<div class="main_box">
	<?php echo $this->render('left.php'); ?>
	<div class="raise right bg_w clearfix">
            <div class="ybc_list">
				<div class="ybcoin clearfix">
					<h2 class="left" data-i18n="u_i_1">Mining records </h2>
				</div>
                                    <form action="<?= Url::to(['wak'])?>" method="get" class="form-inline" role="form" id="form">
                                      <ul class="ibox-content ul_select">
                                          <li class="li_select">
                                              <div class="form-group">
                                                  <label for="" data-i18n="u_c_2" class="control-label">Trade sector</label>
                                                  <select class="form-control tpl-category-parent" name="market">
                                                      <option value="">Please choose</option>
                                                      <?php foreach ($market_list as $key => $value) { ?>
                                                          <option value="<?= $value?>" <?= $market == $value ? "selected":'' ?>><?= $value?></option>

                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </li>
                                          <li class="li_select">
                                              <div class="form-group">
                                                  <label for="" data-i18n="u_c_2" class="control-label">Returns the entry</label>
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
                 <div class="raise_list">
					<table style="width:100%;" id="Transaction" align="center" border="0" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
									<th>Trading market</th>
	                                <th>Number</th>
	                                <th>Type</th>
	                                <th>Price</th>
	                                <th>Time</th>
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
	                                            <td><?= $value['type'] == 'sell' ? 'sell('.$value['amount'].')' : 'buy('.$value['amount'].')' ?></td>
	                                            <td><?= $value['price'] ?></td>
	                                            <td><?= date('Y-m-d H:i:s', $value['time']) ?></td>
	                                        </tr>
	                                    <?php } ?>
	                                <?php }else{ ?>
	                                    <div class="alert alert-warning" role="alert">
	                                        No data yet!
	                                    </div>
	                                <?php } ?>
	                                    
	                            <?php } ?>
						</tbody>
					</table>
				</div>
				<div id="oImg" style="display: none">
					<img src="/resource/frontend/img/noData.png" alt="">
					<p>no data</p>
				</div>
			</div>
			
		</div>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>


<script defer="defer" src="/resource/frontend/js/selectFilter.js"></script>