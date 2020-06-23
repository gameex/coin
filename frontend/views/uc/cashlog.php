<link href="/resource/frontend/css/selectFilter.css" rel="stylesheet">
<link href="/resource/frontend/css/uc.css" rel="stylesheet">
<div id="main">
	<div class="main_box">
	<?php echo $this->render('left.php'); ?>
	<div class="raise right bg_w clearfix">
            <div class="ybc_list">
				<div class="ybcoin clearfix">
					<h2 class="left" data-i18n="u_c_1">充提历史</h2>
				</div>

				<ul class="ul_select">
					<li class="li_select">
						<label for="" data-i18n="u_c_2">币种</label>
						<div id="mySelect_1">
							<div class="filter-text">
								<input class="filter-title filter-int" type="text" readonly="" placeholder="全部">
								<i class="icon icon-filter-arrow ico-int"></i>
							</div>
							<ul class="filter-list coin_symbol" style="">
								<li class="filter-selected" data-value="0"><a title="全部">全部</a></li>
							</ul>
						</div>
					</li>
					<li  class="li_select">
						<input class="filter-title" type="radio" name="moneylog" value="0" checked>充值<span style="width: 15px; display: inline-block;"></span>
						<input class="filter-title" type="radio" name="moneylog" value="1">提现
					</li>

					<li class="li_select sure" style="width:140px;height:38px;background:#368AE5;color:#ffffff;text-align: center;line-height: 38px;font-size: 16px;cursor: pointer;margin-right: 0;margin-left:13px;border-radius: 4px;">查询</li>
				</ul>
				<div class="raise_list">
					<table  style="width:100%;" id="Transaction" align="center" border="0" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<th data-i18n="u_c_3">类型</th>
								<th data-i18n="u_c_2">币种</th>
								<th data-i18n="u_c_5">资金</th>
								<th data-i18n="u_c_6">时间</th>
							</tr>
						</thead>
						<tbody id="finlist">

						</tbody>
					</table>
				</div>
				<div id="oImg" style="display: none">
					<img src="/resource/frontend/img/noData.png" alt="">
					<p>暂无数据</p>
				</div>
			</div>
			<div class="allpage" id="Page" style="display: none;">
				<div class="pagecnt">
					<font data-i18n="c_20">共</font>&nbsp;<span id="allcount">0</span>&nbsp;<font data-i18n="c_21">条记录</font>，<font data-i18n="c_20">共</font>&nbsp;<span id="allPage">0</span>&nbsp;<font data-i18n="c_22">页</font>，<font data-i18n="c_23">当前是</font>&nbsp;<span id="pages"> 1 </span>&nbsp;<font data-i18n="c_24">页</font>
				</div>
				<div class="jumpbtn">							
					<button id="firstPage" data-i18n="c_25">首页</button>
					<button id="prePage" data-i18n="c_26">上一页</button>
					<button id="nextPage" data-i18n="c_27">下一页</button>
					<button id="lastPage" data-i18n="c_28">末页</button>
					<div class="jumpchoice">
						<font data-i18n="c_29">跳转至</font><input type="text" id="choicePage" value=""><font data-i18n="c_24">页</font>&nbsp;<button id="jumpPage" data-i18n="c_30">跳转</button>
					</div>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>

<script type="text/javascript">
	var type = 1;
	var transaction_type = '';
	var StartTime = '';
	var EndTime = '';
	var coin_symbol = '';

	var page=1;
	var allPage=$("#allPage").html();
	var allcount = '';
	var pageSize = 10;

	finParam = {
		type: 1,
		currency_id: 0,
		finance_type: 0
	};
	$(document).ready(function() {
		init(type);
		formData(page,pageSize,coin_symbol,transaction_type,StartTime,EndTime)
		/* 初始化 */
		function init(type) {
			var token = localStorage.getItem('access_token');
			if(type == 1){
				var today = new Date();
				StartTime = 0;
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
	    
	    
	    //获取币种列表
		http.post('withdraw/coin-list', {
			page:0,
			page_size:1000
		}, function(res) {
			var List = res.data.data;
			var filter = document.querySelector('.filter-list');
			for(var i=0;i<List.length;i++){
				var li = document.createElement('li');
				li.innerHTML = '<a>'+List[i].symbol+'</a>'
				li.setAttribute("data-value", i+1)
				filter.appendChild(li);
			}
		});
	    $("#firstPage").on("click",function(){
			if(allPage == 1 || page==1){
				return false;
			}
			page=1;
			formData(page,pageSize,coin_symbol,transaction_type,StartTime,EndTime)
  		});
  		$("#prePage").on("click",function(){
			page--;
			if(page<1){
				page=1;
				return false;
			}
			formData(page,pageSize,coin_symbol,transaction_type,StartTime,EndTime)
  		});
  		$("#nextPage").on("click",function(){
			page++;
			if(page>allPage){
				page=allPage;
				return false;
			}
			formData(page,pageSize,coin_symbol,transaction_type,StartTime,EndTime)
  		});
  		$("#lastPage").on("click",function(){
			if(allPage == 1){
				return false;
			}
			page = allPage
			formData(page,pageSize,coin_symbol,transaction_type,StartTime,EndTime)
  		});
  		$("#jumpPage").on("click",function(){
			var jumpPages=$("#choicePage").val();
			if(jumpPages<1 || Number(jumpPages)>Number(allPage)){
				return false;
			}
			page = jumpPages
			formData(page,pageSize,coin_symbol,transaction_type,StartTime,EndTime)
  		});
  		function getLocalTime(nS) {     
		   return new Date(parseInt(nS) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');     
		}
    	function formData(page,pageSize,coin_symbol,transaction_type,StartTime,EndTime){
			http.post('withdraw/recharge-details', {
        		page:page,
        		page_size:pageSize,
        		coin_symbol:coin_symbol,
        		transaction_type:transaction_type,
        		begin_time:StartTime,
        		end_time:EndTime
			}, function(res) {
				$("#finlist").html('');
				List = []
				if(res.code == 200){
					allcount = res.data.total;
					allPage = res.data.page_count;
					if(allcount>pageSize){
						$("#Page").show();
					}else{
						$("#Page").hide();
					};
					$("#allcount").html(allcount);
					$("#allPage").html(allPage);
					$("#pages").html(page);
					page=page;
					var List = res.data.data;
					var tbody = document.getElementById('finlist');
					var Img = document.getElementById('oImg');
					if(List.length == 0){
						Img.style.display = 'block';
					}else{
						Img.style.display = 'none'
						for(var i=0;i<List.length;i++){
							var oTr = document.createElement('tr');
							var time = timestampToTime(List[i].created_at)

							oTr.innerHTML = '<td>'+List[i].detial_type+'</td>'+						    
										'<td>'+List[i].coin_symbol+'</td>'+
									    '<td>'+List[i].change+'</td>'+
									    '<td>'+ getLocalTime(List[i].ctime) +'</td>'
							// oTr.className = 'list_con2';
							tbody.appendChild(oTr);
							
						}
					}
				}
			});
		}

    	function formData2(page,pageSize,coin_symbol,transaction_type,StartTime,EndTime){
			http.post('withdraw/apply-log', {
        		page:page,
        		page_size:pageSize,
        		coin_symbol:coin_symbol,
        		transaction_type:transaction_type,
        		begin_time:StartTime,
        		end_time:EndTime
			}, function(res) {
				$("#finlist").html('');
				List = []
				if(res.code == 200){
					allcount = res.data.total;
					allPage = res.data.page_count;
					if(allcount>pageSize){
						$("#Page").show();
					}else{
						$("#Page").hide();
					};
					$("#allcount").html(allcount);
					$("#allPage").html(allPage);
					$("#pages").html(page);
					page=page;
					var List = res.data.data;
					var tbody = document.getElementById('finlist');
					var Img = document.getElementById('oImg');
					if(List.length == 0){
						Img.style.display = 'block';
					}else{
						Img.style.display = 'none'
						for(var i=0;i<List.length;i++){
							var oTr = document.createElement('tr');
							var time = timestampToTime(List[i].created_at)

							oTr.innerHTML = '<td>提现</td>'+	
										'<td>'+List[i].coin_symbol+'</td>'+
									    '<td>'+List[i].value_dec+'</td>'+
									    '<td>'+ List[i].created_at +'</td>'
							// oTr.className = 'list_con2';
							tbody.appendChild(oTr);
							
						}
					}
				}
			});
		}

		/* 事件绑定 */
		$('.filter-int').val("全部");
		$(".coin_symbol").on("click","li",function (){
			var valText =  $(this).find('a').html();
			$('.filter-int').val(valText);
			$(this).addClass('filter-selected').siblings().removeClass('filter-selected');
			coin_symbol = valText;
		});
		$('#mySelect_1').click(function(){
			$(this).find('.filter-list').slideToggle(100);
			$(this).find('.filter-list').toggleClass('filter-open');
			$(this).find('.icon-filter-arrow').toggleClass('filter-show');
		})
		$('#mySelect_2').selectFilter({
			callBack : function(val) {
				transaction_type = val;
			}
		});
		$('#mySelect_3').selectFilter({
			callBack : function(val) {
				type = val;
				init(type)
			}
		});
		$(".sure").on("click",function (){
			if($("input:radio[name='moneylog']:checked").val() == 0){
				formData(page,pageSize,coin_symbol,transaction_type,StartTime,EndTime)
			}else{
				formData2(page,pageSize,coin_symbol,transaction_type,StartTime,EndTime)
			};
		});
	});
</script>
<script defer="defer" src="/resource/frontend/js/selectFilter.js"></script>