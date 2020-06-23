<link href="/resource/frontend/css/selectFilter.css" rel="stylesheet">
<link href="/resource/frontend/css/uc.css" rel="stylesheet">
<div id="main">
	<div class="main_box">
	<?php echo $this->render('left.php'); ?>
		<div class="w950 right bg_w clearfix">
			<div class="ybc_list">
				<div class="ybcoin clearfix">
					<h2 class="left" data-i18n="u_d_2">Order management</h2>
				</div>
				<!-- <ul class="time" id="time" style="padding-bottom: 40px;">

				</ul> -->
				<ul class="ul_select">
					<li class="li_select">
						<label for="" data-i18n="u_c_2">Choose Pairs</label>
						<div id="mySelect_1">
							<div class="filter-text">
								<input class="filter-title filter-int" type="text" readonly="" >
								<i class="icon icon-filter-arrow ico-int"></i>
							</div>
							<ul class="filter-list" id="time" style="">

							</ul>
						</div>
					</li>
				</ul>
				<div class="raise_list">
					<table style="width:100%;" align="center" border="0" cellpadding="0" cellspacing="0">
				 		<thead>
							<tr>
								<th class="w162" data-i18n="u_d_3">Time</th>
								<th data-i18n="u_d_4">Pair</th>
								<th data-i18n="u_d_5">Type</th>
								<th data-i18n="u_d_6">Order amount</th>
								<th data-i18n="u_d_7">Price</th>
								<th data-i18n="u_d_8">Amount</th>
								<th data-i18n="u_d_9">Money</th>
								<th data-i18n="u_d_11">Operation</th>
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
        	<div class="allpage" id="Page" style="display: none;">
				<div class="pagecnt">
					<font data-i18n="c_20">Total</font>&nbsp;<span id="allcount">0</span>&nbsp;<font data-i18n="c_21">Record</font>，<font data-i18n="c_20">Total</font>&nbsp;<span id="allPage">0</span>&nbsp;<font data-i18n="c_22">Page</font>，<font data-i18n="c_23">At present is</font>&nbsp;<span id="pages"> 1 </span>&nbsp;<font data-i18n="c_24">Page</font>
				</div>
				<div class="jumpbtn">							
					<button id="firstPage" data-i18n="c_25">Home</button>
					<button id="prePage" data-i18n="c_26">Last page</button>
					<button id="nextPage" data-i18n="c_27">Next page</button>
					<button id="lastPage" data-i18n="c_28">End page</button>
					<div class="jumpchoice">
						<font data-i18n="c_29">Jump to</font><input type="text" id="choicePage" value=""><font data-i18n="c_24">Page</font>&nbsp;<button id="jumpPage" data-i18n="c_30">Jump to</button>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>
</div>
<script type="text/javascript">
	http.createWebSocket();
	
	$(document).ready(function() {
		var time = document.getElementById('time');
  		var arr = [];
  		var page=1;
		var allPage=$("#allPage").html();
		var allcount = '';
		var marketType = '';
		var currency='';
  		market()
    	function market(){
        	http.post('bargain/market', {
			}, function(res) {
				var List = res.data;
				time.innerHTML='';
				arr = [];
				if(res.code == 200){
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
							oLi.setAttribute("data-value",k)
							oLi.id = k;
							time.appendChild(oLi);
							marketType = arr[k].marketType;
							currency = arr[k].currency;
							$('.filter-int').val(currency);
							page = 1
							init(marketType,currency,page);
						}else{
							oLi.innerHTML = '<a>'+arr[k].currency+'</a>'
							oLi.id = k;
							oLi.setAttribute("data-value",k)
							time.appendChild(oLi);
						}
					}
				}
			});
        };
    	$("#time").on("click","li",function (){
			var index=$(this).index();
			type=event.target.id;
			$(this).addClass('filter-selected').siblings().removeClass('filter-selected');
			var str =  $(this).find('a').html();
			marketType = str.replace('/','');
			$('.filter-int').val(str);
			page = 1;
			init(marketType,str,page);
		});
		$('#mySelect_1').click(function(){
			$(this).find('.filter-list').slideToggle(100);
			$(this).find('.filter-list').toggleClass('filter-open');
			$(this).find('.icon-filter-arrow').toggleClass('filter-show');
		})

		$("#firstPage").on("click",function(){
			if(allPage == 1 || page==1){
				return false;
			}
			page=1;
			init(marketType,currency,page);
  		});
  		$("#prePage").on("click",function(){
			page--;
			if(page<1){
				page=1;
				return false;
			}
			init(marketType,currency,page);
  		});
  		$("#nextPage").on("click",function(){
			page++;
			if(page>allPage){
				page=allPage;
				return false;
			}
			init(marketType,currency,page);
  		});
  		$("#lastPage").on("click",function(){
			if(allPage == 1){
				return false;
			}
			page = allPage
			init(marketType,currency,page);
  		});
  		$("#jumpPage").on("click",function(){
			var jumpPages=$("#choicePage").val();
			if(jumpPages<1 || Number(jumpPages)>Number(allPage)){
				return false;
			}
			page = jumpPages
			init(marketType,currency,page)
  		});
		
		/* 初始化 */
		function init(market,currency,page) {	
			var token = localStorage.getItem('access_token');
			// var token = 'VXaUsIQC-MAMRSv_-6dwGnx_RaH3iacE_1534906709';
			http.sendData({
				"id": 30,
				"method": "server.auth",
				"params": [token + '|web', 'web']
				// "params": ['TxpUMccv-7oYfMyfpce0SCxBxLYqWdxJ_1533713782|ios', 'ios']
			})
			window.revieceData30 = function(res) {
				if(res.result && res.result.status == 'success') {
					http.sendData({
						"id": 31,
						"method": "order.query",
						"params": [market, 10*(page-1), 10]
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
			window.revieceData31 = function(res) {
				if(res.result) {
					allcount = res.result.total;
					allPage = Math.ceil(res.result.total/10);
					if(allcount>10){
						$("#Page").show();
					}else{
						$("#Page").hide();
					};
					$("#allcount").html(allcount);
					$("#allPage").html(allPage);
					$("#pages").html(page);
					page=page;
					var records = res.result.records;
					if(res.result) {
						$("#entrust").html('');
						List = []
						var List = res.result.records;
						var entrust = document.getElementById('entrust');
						var Img = document.getElementById('oImg');
						if(List.length == 0){
							Img.style.display = 'block';
						}else{
							Img.style.display = 'none';
							for(var i=0;i<List.length;i++){
								var oTr = document.createElement('tr');
								var all_price = List[i].amount*List[i].price;
								var time = timestampToTime(List[i].mtime)
								if(List[i].side == 1){
									oTr.innerHTML = '<td>'+time+'</td>'+
													'<td>'+currency+'</td>'+
													'<td style="color:#e62512;">SELL</td>'+
													'<td>'+List[i].amount+'</td>'+
													'<td>'+List[i].price+'</td>'+
													'<td>'+List[i].deal_stock+'</td>'+
													'<td>'+all_price+'</td>'+
													'<td class="undo"><a href="#" id="'+List[i].id+'" dataid="'+List[i].market+'">Cancel</a></td>'
								}else{
									oTr.innerHTML = '<td>'+time+'</td>'+
													'<td>'+currency+'</td>'+
													'<td style="color:#090;">BUY</td>'+
													'<td>'+List[i].amount+'</td>'+
													'<td>'+List[i].price+'</td>'+
													'<td>'+List[i].deal_stock+'</td>'+
													'<td>'+all_price+'</td>'+
													'<td class="undo"><a href="#" id="'+List[i].id+'" dataid="'+List[i].market+'">Cancel</a></td>'
								}
								entrust.appendChild(oTr);
							}
						}
					}
				}
			}
		}
		$("#entrust").on("click",".undo a",function(ev){
			var id = event.target.id;
			var marketId = $(this).attr("dataid")
			http.confirmTip('Confirm cancel?',function(index,layero,n){
				http.post('bargain/cancel-order', {
					market:marketId,
					order_id:id
				}, function(res) {
					if(res.code ==200){
						init(marketType,currency,page)
						http.info(res.message)
						n.close(index);
					}
				});
			})
  		});
	});
</script>
<script defer="defer" src="/resource/frontend/js/selectFilter.js"></script>