<link href="/resource/frontend/css/selectFilter.css" rel="stylesheet">
<link href="/resource/frontend/css/uc.css" rel="stylesheet">
<div id="main">
	<div class="main_box">
		<div class="bg_w clearfix">
			<div class="ybc_list">
				<div class="ybcoin clearfix">
					<h2 class="left" data-i18n="u_e_1">我的推荐</h2>
				</div>				
				<div class="raise_list">
					<table  style="width:100%;" id="Transaction" align="center" border="0" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<th data-i18n="u_e_5">用户ID</th>
								<th data-i18n="u_e_6">用户邮箱</th>
								<th data-i18n="u_d_5">用户手机号</th>
								<th data-i18n="u_e_7">总充值(等值USDT）</th>
								<th data-i18n="u_e_8">总下单(等值USDT）</th>
								<th data-i18n="u_e_9">总提现(等值USDT）</th>
								<th data-i18n="u_e_10">注册时间</th>
							</tr> 
						</thead>
						<tbody id="entrust">

						</tbody>
					</table>
				</div>
				<div id="oImg" style="display: none">
					<img src="/resource/frontend/img/noData.png" alt="">
					<p>暂无数据</p>
				</div>
			</div>
			<div class="allpage" id="Page" style="display: block;">
				<div class="pagecnt">
					<font data-i18n="c_20"></font>&nbsp;<span id="allcount"></span>&nbsp;<font data-i18n="c_21"></font><font data-i18n="c_20"></font>&nbsp;<span id="allPage"></span>&nbsp;<font data-i18n="c_22"></font><font data-i18n="c_23"></font>&nbsp;<span id="pages"></span>&nbsp;<font data-i18n="c_24"></font>
				</div>
				<div class="jumpbtn">							
					<button id="prePage" data-i18n="c_26">上一页</button>
					<button id="nextPage" data-i18n="c_27">下一页</button>
				</div>
			</div>
		</div>
	</div>
	<div class="clear"></div>
</div>
<script type="text/javascript">
	http.createWebSocket();
	var type = 1;
	var StartTime = '';
	var EndTime = '';
	var typeMarket = 0;
	var market = '';
	var marketType = '';
	var currency='';
	var page=1;
	var allPage=$("#allPage").html();
	var allcount = '';
	$(document).ready(function() {
		var time = document.getElementById('timeMarket');
  		var arr = [];
  		marketFct()
    	function marketFct(){
        	http.post('agent/invite-info', {
			}, function(res) {
				var List = res.data;
				time.innerHTML='';
				arr = [];
				if(page>1 && res.result.records.length == 0){
					$('#nextPage').css({'color':'#bdbdbd'});
					$("#nextPage").attr('disabled',true);
					http.info('已经是最后一页')
					page = page-1;
				}else{
					$("#entrust").html('');
					$("#nextPage").attr('disabled',false);
					$('#nextPage').css({'color':'#1e62ac'});
				}
				
				if(res.result) {
					var List = res.result.records;
					var entrust = document.getElementById('entrust');
					var Img = document.getElementById('oImg');
					if(List.length == 0){
						if(page>1){
							$("#Page").show();
						}else{
							Img.style.display = 'block';
							$("#Page").hide();
							$("#jumpbtn").hide();
						}
						
					}else{
						Img.style.display = 'none';
						allcount = (page)*10;
						allPage = page;
						$("#Page").show();
						page=page;
						for(var i=0;i<List.length;i++){
							var oTr = document.createElement('tr');
							var time = timestampToTime(List[i].ftime)
							if(List[i].side == 1){
								oTr.innerHTML = '<td>'+List[i].id+'</td>'+
											'<td>'+currency+'</td>'+
											'<td style="color:#090;">卖出</td>'+
											'<td>'+time+'</td>'+
											'<td>'+List[i].price+'</td>'+
											'<td>'+List[i].deal_stock+'</td>'+
											'<td>'+List[i].deal_money+'</td>'
							}else{
								oTr.innerHTML = '<td>'+List[i].id+'</td>'+
											'<td>'+currency+'</td>'+
											'<td style="color:#e62512;">买入</td>'+
											'<td>'+time+'</td>'+
											'<td>'+List[i].price+'</td>'+
											'<td>'+List[i].deal_stock+'</td>'+
											'<td>'+List[i].deal_money+'</td>'
							}
							entrust.appendChild(oTr);
						}
					}
				}
			});
        };
    	


  		$("#prePage").on("click",function(){
			page--;
			if(page<1){
				page=1;
				return false;
			}
			init(marketType,currency,page,type);
  		});
  		$("#nextPage").on("click",function(){
			page++;
			if(page>page){
				page=allPage;
				return false;
			}
  		});

	});
</script>
<script defer="defer" src="/resource/frontend/js/selectFilter.js"></script>