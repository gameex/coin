<link href="/resource/frontend/css/uc.css" rel="stylesheet">  
<div id="main">
	<div class="main_box">
		<?php echo $this->render('left.php'); ?>
		<div class="raise right bg_w clearfix">
			<div class="ybc_list">
				<div class="ybcoin" id="mycoin">
					<div class="userAssets">
						<font data-i18n="u_b_1">我的资产</font>
						<span><font data-i18n="u_a_11">总资产</font>：<b><font data-i18n="u_b_4" class="ws_mount"></font></b></span>
						<span><font data-i18n="u_a_11">参考累计盈亏</font>：<b><font data-i18n="u_b_4" class="ws_mount2"></font></b></span>
						<span><font data-i18n="u_a_11">盈亏比例</font>：<b><font data-i18n="u_b_4" class="ws_mount3"></font></b></span>
					</div>
					<div class="clear">
					</div>
				</div>
				<div class="raise_list">
					<table  id="myRaise" align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%">
					<thead>
				<tr class="list_head">
					<th class="assets01" data-i18n="u_b_5">
						币种
					</th>
					<th class="assets02" data-i18n="u_b_6">
						总额
					</th>
					<th class="assets02" data-i18n="u_b_7">
						可用
					</th>
					<th class="assets02" data-i18n="u_b_8">
						冻结
					</th>
					<th class="assets02" data-i18n="u_b_9">
						CNY估值
					</th>
					<th class="assets03" data-i18n="u_d_11">
						操作
					</th>
					</tr>
					</thead>
					<tbody class="tBody">
					</tbody>
					</table>
				</div>
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
<script type="text/javascript">
	$(document).ready(function() {
		init();
		/* 初始化 */
		function init() {			
			bandEvent();
		}
		/* 事件绑定 */
        function bandEvent() {
        	http.post('/bargain/balance', {
        		asset_type:''
			}, function(res) {
				if(res.code == 200){
					var List = res.data.list;
					console.log(List)
					$('.ws_mount').html('约￥'+((res.data.total_money*6.88).toFixed(2))+'&nbsp;&nbsp;CNY');
					$('.ws_mount2').html('约￥'+((res.data.total_change*6.88).toFixed(2))+'&nbsp;&nbsp;CNY');
					if (parseFloat(res.data.total_change) > 0) {
						$profit_loss_rate = '+'+(parseFloat(res.data.total_change)/parseFloat(res.data.total_recharge)*100).toFixed(2) + '%';
					}else{
						$profit_loss_rate = '-'+(parseFloat(res.data.total_change)/parseFloat(res.data.total_recharge)*100).toFixed(2) + '%';
					}
					$('.ws_mount3').html($profit_loss_rate);


					var tbody = document.querySelector('.tBody');
					var Img = document.getElementById('oImg');
					if(List.length == 0){
						Img.style.display = 'block';
					}else{
						Img.style.display = 'none';
						for(var i=0;i<List.length;i++){
							var oTr = document.createElement('tr');
							/*if(List[i].name == 'USDT'){
								oTr.innerHTML = '<td class="usertdtitle assets01">'+List[i].name+'</td>'+				
												'<td class="assets02">'+List[i].available+'</td>'+
											    '<td class="assets02">'+List[i].total_freeze+'</td>'+
											    '<td class="assets02">'+List[i].total_amount+'</td>'+
											    '<td class="assets02">'+List[i].money+'</td>'+
											    '<td class="cost interest assets03">'+
										      	'<a href="/uc/recharge#'+List[i].name+'"style="color:#2861f6;background:transparent;" data-i18n="u_b_15">充币</a>&nbsp;&nbsp;'+
												'<a href="/uc/withdraw#'+List[i].name+'" style="color: #2861f6;background:transparent;" data-i18n="u_b_16">提现</a>'+
												'</td>';
							}else{*/
								if(List[i].recharge_enable == 1){
									recharge_str = '<a href="/uc/recharge#'+List[i].name+'"style="color:#2861f6;background:transparent;" data-i18n="u_b_15">充币</a>&nbsp;&nbsp;';
								}else{
									recharge_str = '<a href="javascript:void(0);'+List[i].name+'"style="color:#848484;background:transparent;" data-i18n="u_b_15">暂停</a>&nbsp;&nbsp;';
								}								
								if(List[i].withdraw_enable == 1){
									withdraw_str = '<a href="/uc/withdraw#'+List[i].name+'" style="color: #2861f6;background:transparent;" data-i18n="u_b_16">提现</a>';
								}else{
									withdraw_str = '<a href="javascript:void(0);'+List[i].name+'" style="color:#848484;background:transparent;" data-i18n="u_b_16">暂停</a>';
								}
								oTr.innerHTML = '<td class="usertdtitle assets01" style="text-align:left;">'+'<img src="'+List[i].icon+'" alt="" class="biIcon" >'+List[i].name+'</td>'+						    
												'<td class="assets02">'+((List[i].total_amount-0).toFixed(8))+'</td>'+
											    '<td class="assets02">'+((List[i].available-0)).toFixed(8)+'</td>'+
											    '<td class="assets02">'+((List[i].total_freeze-0)).toFixed(8)+'</td>'+
											    '<td class="assets02">'+((List[i].money*6.88).toFixed(2))+'</td>'+
											    '<td class="cost interest assets03">'+
										      	recharge_str +
												withdraw_str +
									//			'<a href="/trade/USDT/' + List[i].name + '" style="color: #2861f6;margin-left:10px;background:transparent;" data-i18n="u_b_16">交易</a>'+
												'</td>';
							//}
							oTr.className = 'list_con2';
							tbody.appendChild(oTr);
						}
					}
				}
			});
        	$('.langhover').hover(function() {
				$('.multlang_cont').show();
			}, function() {
				$('.multlang_cont').hide();
			});

			$('.regMult').hover(function() {
				$('.multlang_cont').show();
			}, function() {
				$('.multlang_cont').hide();
			});

			$('.multlang_cont').hover(function() {
				$(this).show();
			}, function() {
				$(this).hide();
			});
        }
	});
</script>