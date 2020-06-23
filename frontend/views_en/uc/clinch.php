<link href="/resource/frontend/css/selectFilter.css" rel="stylesheet">
<link href="/resource/frontend/css/uc.css" rel="stylesheet">
<div id="main">
	<div class="main_box">
		<?php echo $this->render('left.php'); ?>
		<div class="raise right bg_w clearfix">
			<div class="ybc_list">
				<div class="ybcoin clearfix">
					<h2 class="left" data-i18n="u_e_1">My deal</h2>
				</div>
				<ul class="ul_select">
					<li class="li_select">
						<label for="" data-i18n="u_c_2">Currency</label>
						<div id="mySelect_1">
							<div class="filter-text">
								<input class="filter-title filter-int" type="text" readonly="" placeholder="Whole">
								<i class="icon icon-filter-arrow ico-int"></i>
							</div>
							<ul class="filter-list coin_symbol" id="timeMarket">
								
							</ul>
						</div>
					</li>
					<li class="li_select">
						<label for="" data-i18n="u_c_4">time</label>
						<div id="mySelect_3">
							<div class="filter-text">
								<input class="filter-title" type="text" readonly="" placeholder="pleace select">
								<i class="icon icon-filter-arrow"></i>
							</div>
							<select name="filter" style="cursor: pointer;">
								<option value="1" selected="">Within a day</option>
								<option value="2">Within a month</option>										
								<option value="3">Within three months</option>							
							</select>
						</div>
					</li>
				</ul>
				<div class="raise_list">
					<table  style="width:100%;" id="Transaction" align="center" border="0" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<th style="width: 180px;" data-i18n="u_e_5">Order number</th>
								<th data-i18n="u_e_6">Pairs</th>
								<th data-i18n="u_d_5">Type</th>
								<th style="width: 180px;" data-i18n="u_e_7">Time</th>
								<th data-i18n="u_e_8">Price</th>
								<th data-i18n="u_e_9">Amount</th>
								<th data-i18n="u_e_10">Volume</th>
							</tr> 
						</thead>
						<tbody id="entrust">

						</tbody>
					</table>
				</div>
				<div id="oImg" style="display: none">
					<img src="/resource/frontend/img/noData.png" alt="">
					<p>No data</p>
				</div>
			</div>
			<div class="allpage" id="Page" style="display: block;">
				<div class="pagecnt">
					<font data-i18n="c_20"></font>&nbsp;<span id="allcount"></span>&nbsp;<font data-i18n="c_21"></font><font data-i18n="c_20"></font>&nbsp;<span id="allPage"></span>&nbsp;<font data-i18n="c_22"></font><font data-i18n="c_23"></font>&nbsp;<span id="pages"></span>&nbsp;<font data-i18n="c_24"></font>
				</div>
				<div class="jumpbtn">							
					<button id="prePage" data-i18n="c_26">Last page</button>
					<button id="nextPage" data-i18n="c_27">Next page</button>
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
        	http.post('bargain/market', {
			}, function(res) {
				var List = res.data;
				time.innerHTML='';
				arr = [];
				for(var i=0;i<List.length;i++){
					for(var j=0;j<List[i].list.length;j++){
						arr.push({currency:List[i].list[j].stock+"/"+List[i].list[j].money,marketType:List[i].list[j].name});
					}
				}
				for(var k = 0;k<arr.length;k++){
					var oLi = document.createElement('li');

					if(k == 0){
						oLi.innerHTML = '<a>'+arr[k].currency+'</a>'
						oLi.className = 'filter-selected'
						oLi.id = k;
						oLi.setAttribute("data-value",k)
						time.appendChild(oLi);
						marketType = arr[k].marketType;
						currency = arr[k].currency;
						page = 1;
						$('.filter-int').val(currency);
						init(marketType,currency,page,type);
					}else{
						oLi.innerHTML = '<a>'+arr[k].currency+'</a>'
						oLi.id = k;
						oLi.setAttribute("data-value",k)
						time.appendChild(oLi);
					}
				}
			});
        };
    	$("#timeMarket").on("click","li",function (){
			var index=$(this).index();
			typeMarket=event.target.id;
			$(this).addClass('filter-selected').siblings().removeClass('filter-selected');
			var str =  $(this).find('a').html();
			marketType = str.replace('/','');
			$('.filter-int').val(str);
			page = 1;
			init(marketType,str,page,type);
		});
		$('#mySelect_1').click(function(){
			$(this).find('.filter-list').slideToggle(100);
			$(this).find('.filter-list').toggleClass('filter-open');
			$(this).find('.icon-filter-arrow').toggleClass('filter-show');
		});
		$('#mySelect_3').selectFilter({
			callBack : function(val) {
				type = $(this).children('option:selected').val();
				page = 1;
				init(marketType,currency,page,type)
			}
		});
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
			init(marketType,currency,page,type);
  		});
		// init(type);
		/* 初始化 */
		function init(market,currency,page,type) {
			console.log(page)
			var token = localStorage.getItem('access_token');
			// var token = 'ec6rpbYMDRogd8i2OpKGQPr_m-hJGwWs_1535525197';
			http.sendData({
				"id": 30,
				"method": "server.auth",
				"params": [token + '|web', 'web']
				// "params": ['TxpUMccv-7oYfMyfpce0SCxBxLYqWdxJ_1533713782|ios', 'ios']
			})
			if(type == 1){
				var today = new Date();
				StartTime = parseInt((today.getTime() + 1000*60*60*24*(-3))/1000);
				EndTime = parseInt((today.getTime() + 1000*60*60*24*0)/1000);
			}else if(type == 2){
				var day = new Date(new Date().setHours(0, 0, 0, 0));
				var today = new Date();
				StartTime = parseInt((day.setDate(1))/1000);
				EndTime = parseInt((today.getTime() + 1000*60*60*24*0)/1000);
			}else if(type == 3){
			    var resultDate,year,month,date,hms;
			    var currDate = new Date();
			    year = currDate.getFullYear();
			    month = currDate.getMonth()+1;
			    date = currDate.getDate();
			    hms = currDate.getHours() + ':' + currDate.getMinutes() + ':' + (currDate.getSeconds() < 10 ? '0'+currDate.getSeconds() : currDate.getSeconds());
			    switch(month)
			    {
			      case 1:
			      case 2:
			      case 3:
			        month += 9;
			        year--;
			        break;
			      default:
			        month -= 3;
			        break;
			    }
			    month = (month < 10) ? ('0' + month) : month;
			    resultDate = year + '-'+month+'-'+date+' ' + hms;
			    var date = new Date(resultDate);
				StartTime = parseInt((date.getTime())/1000);
				var today = new Date();
				EndTime = parseInt((today.getTime() + 1000*60*60*24*0)/1000);
			}
			window.revieceData30 = function(res) {
				if(res.result && res.result.status == 'success') {
					http.sendData({
						"id": 32,
						"method": "order.history",
						"params": [market, StartTime, EndTime, 10*(page-1), 10,0]
					})
				}
			}
			function timestampToTime(timestamp) {
		        var date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
		        var Y = date.getFullYear() + '-';
		        var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
		        var D = date.getDate() + ' ';
		        var h = date.getHours() + ':';
		        var m = date.getMinutes() + ':';
		        var s = date.getSeconds();
		        return Y+M+D+h+m+s;
		    }
			window.revieceData32 = function(res) {
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
											'<td style="color:#e62512;">SELL</td>'+
											'<td>'+time+'</td>'+
											'<td>'+List[i].price+'</td>'+
											'<td>'+List[i].deal_stock+'</td>'+
											'<td>'+List[i].deal_money+'</td>'
							}else{
								oTr.innerHTML = '<td>'+List[i].id+'</td>'+
											'<td>'+currency+'</td>'+
											'<td style="color:#090;">BUY</td>'+
											'<td>'+time+'</td>'+
											'<td>'+List[i].price+'</td>'+
											'<td>'+List[i].deal_stock+'</td>'+
											'<td>'+List[i].deal_money+'</td>'
							}
							entrust.appendChild(oTr);
						}
					}
				}
			}
		}
	});
</script>
<script defer="defer" src="/resource/frontend/js/selectFilter.js"></script>