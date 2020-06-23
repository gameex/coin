<link href="/resource/frontend/css/trade.css" rel="stylesheet">
<div id="coinOrder" class="clearfix" currency_mark="BTC" currency_id="30" currency_trade="USDT" base_id="104" gasetting="0" rmbrate="7.00689" fee_type="base" style="padding: 10px 10px 0px; height: 1000px;" basermb="6.4" baseusd="1.00">
	<div class="orderLift">
		<div class="total_hover">
		</div>
		<div class="total_top_new">
		</div>
      <div class='newBoxB'>
		<div class="favorite">
			<ul class="favorite_view" id='favorite_view'>
				<li trade="Fav" class="FavLi"><span class="collection"><font data-i18n="a_optional">自选</font></span></li>
			</ul>
<!--			<div class="search">
				<i></i>
				<input type="text" name="search" placeholder="搜索" id="search_input">
			</div> -->
		</div> 
     
		<div class="currency_list_new" style="position: relative;">
			<div class="tradeNav">
				<ul>
					<li class="TradingOn ascBtn" style="text-align: center;" ascvalue="currency_mark" type="init" >
						<font>交易对</font>
						<dev class="amp"><span class="asc"><i></i></span><span class="des"><i></i></span></dev>
					</li>
					<li class="newPirce ascBtn" ascvalue="new_price" style="text-align: center;" type="init" >
						<font>价格</font>
						<dev class="amp"><span class="asc"><i></i></span><span class="des"><i></i></span></dev>
					</li>
					<li class="newChange ascBtn" ascvalue="24H_change_rate" type="init" >
						<font>涨幅</font>
						<dev class="amp"><span class="asc"><i></i></span><span class="des"><i></i></span></dev>
					</li>
				</ul>
			</div>
			<div class="curView_New">
				<div class="tradView tradViewFav tradViewSearch clearfix" style="display: none;">
					<div class="tradMain">
				<!--		<div class="tradTop">
							<h2 data-i18n="trade_07">主区</h2>
						</div>-->
						<div class="curList_new">
							<table class="List_mark_new" border="0" cellpadding="0" cellspacing="0">
								<tbody>

								</tbody>
							</table>
						</div>
					</div>

				</div>

				<div class="tradView tradViewContent tradViewSearch clearfix">
					<div class="tradMain">
					<!--	<div class="tradTop">
							<h2 data-i18n="trade_07">主区</h2>
						</div>-->
						<div class="curList_new">
							<table class="List_mark_new" border="0" cellpadding="0" cellspacing="0">
								<tbody>
								</tbody>
							</table>
						</div>
					</div>

				</div>
			
			</div>
		</div>
      </div>
		<div class="latest_list_new">
			<h3 data-i18n="b_4">最新成交</h3>
			<div class="record_new">
				<table class="latest_list_new" align="center" border="0" cellpadding="0" cellspacing="0">
					<thead>
						<tr height="28">
							<th class="header" width="90" data-i18n="b_5">时间</th>
							<th class="header" width="100" data-i18n="b_6">成交价格</th>
							<th class="header" width="106" data-i18n="b_7">成交量</th>
							<th class="header"></th>
						</tr>
					</thead>
				</table>
				<div class="coinorderlist" style="height: 400px;">
					<table id="coinorderlistNew" border="0" cellpadding="0" cellspacing="0">
						<tbody>

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="orderCenter">
		<!--普通k线区-->
		<div class="k_img k-box" id="k-cus-box">
			<div id="graphbox" style="width:100%;height: 410px;margin:0px auto 30px;position: relative;" currency_id="30" currency_mark="btc">
				<div id="digifinex_chart">
					<iframe id="tradingview_782fd" name="tradingview_782fd" src="" frameborder="0" allowtransparency="true" scrolling="no" allowfullscreen="" style="display: block; width: 100%; height: 925px;">

	   				</iframe>
				</div>
			</div>
		</div>

		<!--普通k线区end-->
		<div class="my_record_new">
			<div class="myRecord clearfix">
				<h2> <font data-i18n="b_8">当前委托</font><a href="javscript:;" id="cancelOrder" style='padding:3px 15px;border-radius: 21px;font-size: 12px;float: right;'>全部撤销</a></h2>
				<table class="latestList" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
					<thead>
						<tr height="30">
							<!--<th class="header" width="15.14%"></th>-->
							<th class="header" width="20%" data-i18n="b_10">交易对</th>
							<th class="header" width="20%" data-i18n="b_11">委托时间</th>
							<th class="header" width="20%" data-i18n="b_l2">委托价格/成交金额</th>
							<th class="header" width="20%" data-i18n="b_13">委托数量/已成交数量</th>
							<th class="header" width="20%" data-i18n="b_14">操作</th>
						</tr>
					</thead>
				</table>
				<div class="mycointrustlist">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tbody id="mycointrustlist_new">

						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="record_history_new">
			<div class="myRecordHistory clearfix">

				<h2><font data-i18n="b_27">历史委托</font><a href="/uc/entrusted" data-i18n="a_lookmore" style='padding:3px 15px;border-radius: 21px;font-size: 12px;'>查看更多</a></h2>

				<table class="historyList" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
					<thead>
						<tr height="30">
							<!--<th class="header" width="15.14%"></th>-->
							<th class="header" width="20%" data-i18n="b_10">交易对</th>
							<th class="header" width="20" data-i18n="b_11">委托时间</th>
							<th class="header" width="20%"ata-i18n="b_l2">委托价格/成交金额</th>
							<th class="header" width="20%" data-i18n="b_13">委托数量/已成交数量</th>
							<th class="header" width="20%" data-i18n="b_15">状态</th>
						</tr>
					</thead>
				</table>
				<div class="myHistoryList" style="max-height: 209px;">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tbody id="myHistoryList_new"></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="orderRight">
		<div class="transact_new">
			<h2 data-i18n="b_16">交易挂单</h2>
			<div class="coinlist">
				<table class="coinlist_new" align="center" border="0" cellpadding="0" cellspacing="0">
					<thead>
						<tr height="18">
							<th class="header" width="55" data-i18n="b_17">序号</th>
							<th class="header headerPrice" width="105">
								<font data-i18n="b_18">价格</font></th>
							<th class="header headerNum" width="104" data-i18n="">
								<font data-i18n="b_13">数量</font></th>
							<th class="header headerNum" width="107">
								<font data-i18n="b_19">总计</font></th>
						</tr>
					</thead>
					<tbody id="sellCoin">
					</tbody>
                  </table>
                  <table class="PriceNew_active1">
                  <tbody class="new_price1" style="margin:0 auto;"></tbody><tbody class="shiftRmb1"><tbody style="background:url(/resource/frontend/img/tongxun.png);width:18px; height:13px;margin-left: 330px;margin-top: -22px;position: absolute;"alt="行情更新正常" title="行情更新正常"></tbody>
                  </table>
                 <table class="coinlist_new" align="center" border="0" cellpadding="0" cellspacing="0">
					<tbody id="buyCoin" >
					</tbody>
				</table>
			</div>
		</div>

		<div class="sellform_new">
			<div class="sellHeader">
				<span class="buy_head head active" left="0" data-i18n="b_20">买入</span>
				<span class="sell_head head" left="-380" data-i18n="b_21">卖出</span>
			</div>
			<div class="sell_buy_view">

				<div class="formareaView">
					<div class="formarea buy">
						<ul class="buyform">
							<li class="inputting">
								<font data-i18n="b_18" class="b_18_en">价格</font>
								<label class="sell_record">
                <input type="password" name="" style="z-index: -99999;position: absolute;visibility: hidden;">
                <input  style="color:#333"  name="buyprice"  class="numberin_new" id="coinpricein_new" currency_digit_num="2" currency_buy_fee="0.2" type="text" maxlength="15">
                <span class="unit"><i></i>&nbsp;&nbsp;</span>
              </label>
								<span class="unit2"><i></i></span>
							</li>
							<li class="inputting">
								<span class="numTiter" data-i18n="b_13">数量</span>
								<label class="sell_num">
			                      <input value="0.00000000"   style="color:#333" name="buynum" class="numberin_new" id="numberin_new" currency_digit_num="4" currency_buy_fee="0.2" type="text" maxlength="20">
			                      <span class="unit"></span>
			                      <span class="mainUnit"><font data-i18n="b_28">最大买入量</font><i>1</i></span>
			                    </label>
							</li>
							<li class="range rangeSELL">
								<span class="calibration" barback="#E62512"></span>
                              	<input type="hidden" class="single-slider" value="0" style="display: none;">
								<div class="slider-container theme-green" style="width: 255px;">
									<div class="bar01 bar" getlift="0"></div>
									<div class="bar02 bar" getlift="64"></div>
									<div class="bar03 bar" getlift="128"></div>
									<div class="bar04 bar" getlift="191"></div>
									<div class="bar05 bar" getlift="256"></div>
									<div class="back-bar">
										<div class="selected-bar" style="background: rgb(230, 37, 18); width: 1px; left: 0px;"></div>
										<div class="pointer low" style="display: none;"></div>
										<div class="pointer-label" style="display: none;">123456</div>
										<div class="pointer high" style="left: -6px;"></div>
										<!--<div class="pointer-label">0%</div>-->
										<div class="hover-label" style="color: rgb(230, 37, 18);"></div>
										<div class="clickable-dummy"></div>
									</div>
									<div class="scale"></div>
								</div>
							</li>
							<li class="inputting">
								<font data-i18n="b_22" class="b_18_en">金额</font>
								<label class="sell_record">
                <input type="password" name="" style="z-index: -99999;position: absolute;visibility: hidden;">
                <input value="0.00000000"   name="buyAmount" id="coinAmount_new" currency_digit_num="2" currency_buy_fee="0.2" type="text">
                <span class="unit"></span>
              </label>
							</li>
							
						</ul>
						<p class="sellform">
							<button id="trustbtnin"  class="submit" ><font data-i18n="b_20">买入</font>&nbsp;</button>
						</p>
					</div>

					<div class="formarea sell">
						<ul class="buyform">
							<li class="inputting">
								<font data-i18n="b_18" class="b_18_en">价格</font>
								<label for="price" class="buy_record">
                  <input value="6782.81"  style="color:#333" class="buyinput" id="coinpriceout_new" class='numberout_new'  currency_digit_num="2" currency_sell_fee="0.2" onblur="if(value==&#39;&#39;){value=&#39;6782.81&#39;;}" type="text" maxlength="20">
                   <span class="unit"><i></i>&nbsp;&nbsp;</span>
              </label>
								<span class="unit2"><i></i></span>
							</li>
							<li class="inputting">
								<span class="numTiter" data-i18n="b_13">数量</span>
								<label class="buy_num">
                <input value="0.00000000" style="color:#333" name="numberout" class="numberout_new" id="numberout_new"  currency_digit_num="4" currency_sell_fee="0.2" type="text" maxlength="20">
                <span class="unit"></span>
                <span class="mainUnit"><font data-i18n="b_29">最大卖出量</font><i>0.00000000</i></span>
              </label>
							</li>
							<li class="range rangeBUY">
								<span class="calibration" barback="#009900"></span>
                              	<input type="hidden" class="single-slider_sell" value="0" style="display: none;">
								<div class="slider-container theme-green" style="width: 255px;">
									<div class="bar01 bar" getlift="0"></div>
									<div class="bar02 bar" getlift="64"></div>
									<div class="bar03 bar" getlift="128"></div>
									<div class="bar04 bar" getlift="191"></div>
									<div class="bar05 bar" getlift="256"></div>
									<div class="back-bar">
										<div class="selected-bar" style="width: 1px; left: 0px;"></div>
										<div class="pointer low" style="display: none;"></div>
										<div class="pointer-label" style="display: none;">123456</div>
										<div class="pointer high" style="left: -6px;"></div>
										<!--<div class="pointer-label">0%</div>-->
										<div class="hover-label"></div>
										<div class="clickable-dummy"></div>
									</div>
									<div class="scale"></div>
								</div>
							</li>
							<li class="inputting">
								<font data-i18n="b_22" class="b_18_en">金额</font>
								<label class="sell_record">
                <input type="password" name="" style="z-index: -99999;position: absolute;visibility: hidden;">
                <input value="0.00000000" name="buyAmount"  id="coinpriceAmount_new" currency_digit_num="2" currency_sell_fee="0.2" type="text">
                <span class="unit"></span>
              </label>
							</li>
						</ul>
						<p class="sellform2">
							<button class="submit" id="trustbtnout" ><font data-i18n="b_21">卖出</font>&nbsp;</button>
						</p>
					</div>
				</div>

			</div>
		
			<div class="noLogin">
				<a href="/login" class="login_hosting" data-i18n="b_30">登录后才能交易</a>
			</div>
			
		</div>

		<div class="assets buyformarea" style="height: 63px;">
			<h2 data-i18n="b_23">余额</h2>
			<div class="balance_view">
				<table class="balance_new" align="center" border="0" cellpadding="0" cellspacing="0">
					<thead>
						<tr height="24">
							<th class="header" width="82" data-i18n="b_24">币种</th>
							<th class="header" width="134" data-i18n="b_25">总额</th>
							<th class="header" width="154" data-i18n="b_26">可用</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<input type="hidden" value="比特币" id="cname">
<script src="/resource/frontend/js/http.js?v=1.2"></script>
<script type="text/javascript">
http.createWebSocket()
var stock = "<?php echo $stock; ?>";
var money = "<?php echo $money; ?>";
if($("body#night").length>0){
	var chartUrl = "/chartinglibrary/index_black.html" + "?stock="+stock+"&money="+money;
}else{
	var chartUrl = "/chartinglibrary/index.html" + "?stock="+stock+"&money="+money;
}
$("#tradingview_782fd").attr("src",chartUrl);
</script>
<script src="/resource/frontend/js/trade.js?v=1.2"></script>
