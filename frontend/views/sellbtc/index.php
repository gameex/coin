<link href="/resource/frontend/css/app.css" rel="stylesheet">
<link href="/resource/frontend/css/selectFilter.css" rel="stylesheet">
<style type="text/css">
	.otc-btn-container .btn-loading {
	  border-radius: 3px;
	  color: white;
	  background-color: #638BD4;
	  cursor: pointer;
	  min-height: 33px;
	  min-width: 80px;
	  font-size: 14px;
	  font-weight: 600;
	  display: inline-block;
	  border: transparent;
	  height: 100%;
	  width: 100%;
	  outline: none;
	  position: relative;
	}
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
		color:#666;
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
	.trade-responsive .trade-content .table-list .user.operation{
		    padding-left: 68px;
	}
	.ivu-tooltip{
		opacity: 0;
	}
	.ybc_list .ul_select .active{
		color:#368AE5;
	}
	#oImg img{
		width:172px;height:180px;margin-top: 40px;width:auto;margin-left:calc(50% - 86px);
	}
	#oImg p{
		width:100%; height:30px;text-align: center;line-height: 30px;font-size: 16px;margin-top: 30px;color:#888888;
	}
  .nav li a.active{
  	height:50px;
  }
  .nav li a{
  	height:60px;
  }
</style>

<div id="main" style="min-height: 689px;padding-bottom: 100px;">
	<div class="main_box">
		<div class="trade-responsive ivu-col ivu-col-span-xs-24 ivu-col-span-lg-14 ybc_list"><!----> 
			<ul class="ul_select" style="margin-top: 40px;">
				<li class="li_select">
					<label data-i18n="u_c_2" class="ChooseType">购买</label>
					<div id="mySelect_1">
						<div class="filter-text">
							<input class="filter-title filter-int" type="text" readonly="" placeholder="全部">
							<i class="icon icon-filter-arrow ico-int"></i>
						</div>
						<ul class="filter-list coin_symbol" id="timeMarket">
							
						</ul>
					</div>
				</li>
				<li class="li_select">
					<label data-i18n="u_c_4" class="ChooseType">出售</label>
					<div id="mySelect_3">
						<div class="filter-text">
							<input class="filter-title filter-int" type="text" readonly="" placeholder="全部">
							<i class="icon icon-filter-arrow"></i>
						</div>
						<ul class="filter-list coin_symbol" id="timeSell">
							
						</ul>
					</div>
				</li>
			</ul>
            <div class="trade-content" style="margin-top: 25px;">
            	<div class="table-list font14 font-gray">
            		<span class="user" style="width:18%;">商家(30日成单 | 30日完成率)</span> 
            		<!-- <span class="user credit">数量</span>  -->
            		<span class="user limit">限额</span> 
            		<span class="user price" style="border:0;height:auto;">单价</span> 
            		<div class="user operation">
            			<p class="spec">
            				<span class="payways">支付方式</span>
            				<span style="display: inline-block;width: 80px;text-align: center;">操作</span>
            			</p>
            		</div>
            	</div> 
            
		        <div class="otc-trade-list">
		        	<div class="trade-list" id="tradeList">
		        		
		        	</div>
		        </div>
		        <div id="oImg" style="display: none">
					<img src="/resource/frontend/img/noData.png" alt="">
					<p>暂无数据</p>
				</div>
    		</div> 
		</div>
	</div>
	<div class="clear"></div>
</div>
<script>
	var currency = ''
	$(document).ready(function() {
		var time = document.getElementById('timeMarket');
		var timeSell = document.getElementById('timeSell');
		var ChooseType = document.querySelectorAll('.ChooseType');
  		var arr = [];
  		var side=1;
  		marketFct(time);
  		marketFct(timeSell);
    	function marketFct(obj){
    		obj.innerHTML='';
			arr = [];
        	http.post('otc/coin-list', {
			}, function(res) {
				var arr = res.data;
				for(var k = 0;k<arr.length;k++){
					var oLi = document.createElement('li');
					if(k == 0){
						oLi.innerHTML = '<a>'+arr[k].coin_name+'</a>'
						oLi.className = 'filter-selected'
						oLi.id = k;
						oLi.setAttribute("data-value",k)
						obj.appendChild(oLi);
						currency = arr[k].coin_name;
						$('#mySelect_1 .filter-int').val(currency);
						$('#mySelect_3 .filter-int').val(currency);
						if(obj.id == 'timeMarket'){
							ChooseType[0].className = 'ChooseType active'
							ChooseType[1].className = 'ChooseType'
							init(side,currency);
						}
					}else{
						oLi.innerHTML = '<a>'+arr[k].coin_name+'</a>'
						oLi.id = k;
						oLi.setAttribute("data-value",k)
						obj.appendChild(oLi);
					}
				}
			});
        };
    	$("#timeMarket").on("click","li",function (){
			var index=$(this).index();
			$(this).addClass('filter-selected').siblings().removeClass('filter-selected');
			var str =  $(this).find('a').html();
			$('#mySelect_1 .filter-int').val(str);
			side = 1;
			ChooseType[0].className = 'ChooseType active'
			ChooseType[1].className = 'ChooseType'
			init(side,str);
		});
		$("#timeSell").on("click","li",function (){
			var index=$(this).index();
			$(this).addClass('filter-selected').siblings().removeClass('filter-selected');
			var str =  $(this).find('a').html();
			$('#mySelect_3 .filter-int').val(str);
			side = 2;
			ChooseType[1].className = 'ChooseType active'
			ChooseType[0].className = 'ChooseType'
			init(side,str);
		});
		$('#mySelect_1').click(function(){
			$(this).find('.filter-list').slideToggle(100);
			$(this).find('.filter-list').toggleClass('filter-open');
			$(this).find('.icon-filter-arrow').toggleClass('filter-show');
		});
		$('#mySelect_3').click(function(){
			$(this).find('.filter-list').slideToggle(100);
			$(this).find('.filter-list').toggleClass('filter-open');
			$(this).find('.icon-filter-arrow').toggleClass('filter-show');
		});
		var tradeList = document.getElementById('tradeList');
		function init(side,currency){
			$("#tradeList").html('');
        	http.post('otc/market-list', {
        		'side':side,
        		'coin_name':currency
			}, function(res) {
				var arr = res.data;
				var alipay_enable = '';
				var Img = document.getElementById('oImg');
				if(arr.length == 0){
					Img.style.display = 'block';
				}else{
					Img.style.display = 'none'
					for(var i=0;i<arr.length;i++){
						var oDIv = document.createElement('div');
						oDIv.className = 'trade-list-in'
						oDIv.innerHTML = '<div class="user-list average">'+
			        						'<img class="avatar-container" src="'+arr[i].head_portrait+'" alt="" style="height: 36px;width: 36px;">'+
			        					'</div> '+
			        					'<div class="info-wrapper">'+
			        						'<div class="name width20 spe-width">'+
			        							'<a href="javascript:;" class="">'+
			        								'<span>'+
					        							'<strong>'+arr[i].nickname+'&nbsp;('+arr[i].deal_count+' | '+arr[i].deal_rate+'%)</strong> '+
					        							'<div class="icon-tips-hover ivu-tooltip">'+
					        								'<div class="ivu-tooltip-rel">'+
					        									'<i data-v-22ecfae0="" class="merchant-level merchant-level3"></i>'+
					        								'</div> '+
					        							'</div>'+
					        						'</span>'+
					        					'</a>'+
					        				'</div> '+
					        				'<div class="amount width20 average">'+arr[i].min_num+'-'+arr[i].max_num+' '+arr[i].coin_name+'</div> '+
					        				'<div class="price average" style="border:0;height:auto;">'+arr[i].price_usd+' CNY</div> '+
					        				'<div class="operation average">'+
					        					'<div class="way">'+
					        						'<div class="icon-hover ivu-tooltip" style="opacity:'+arr[i].card_enable+'">'+
					        							'<div class="ivu-tooltip-rel"><div data-v-22ecfae0="">'+
					        								'<img src="https://file.eiijo.cn/common/images/pay-icon/zh-bank.svg">'+
					        							'</div>'+
					        						'</div> '+
					        					'</div>'+
					        					'<div class="icon-hover ivu-tooltip" style="opacity:'+arr[i].alipay_enable+'">'+
					        						'<div class="ivu-tooltip-rel"><div data-v-22ecfae0="">'+
					        							'<img src="https://file.eiijo.cn/common/images/pay-icon/alipay.svg">'+
					        						'</div>'+
					        					'</div> '+
					        				'</div>'+
					        				'<div class="icon-hover ivu-tooltip" style="opacity:'+arr[i].wechat_enable+'">'+
					        					'<div class="ivu-tooltip-rel">'+
					        						'<div>'+
					        							'<img src="https://file.eiijo.cn/common/images/pay-icon/wechat.svg">'+
					        						'</div>'+
					        					'</div> '+
					        				'</div>'+
			        					'</div> '+
					        			'<div class="trade-btn-control">'+
					        				'<div>'+
					        					'<div class="otc-btn-container">'+
					        						'<button class="btn-loading">出售'+arr[i].coin_name+''+
					        							'<div class="icon-container" style="display: none;">'+
					        								'<i class="loading-icon ivu-icon ivu-icon-load-c"></i>'+
					        							'</div>'+
					        						'</button>'+
					        					'</div>'+
					        				'</div>'+
					        			'</div>'
					    tradeList.appendChild(oDIv);
					}
				}
			});
        };
        $("#tradeList").on("click",".btn-loading",function(ev){
        	http.info('请前往app操作')
			// http.confirmTip('确认撤销？',function(index,layero,n){
			// 	http.info('请去app出售')
			// 	n.close(index);
			// })
  		});
	});
</script>
<script src="/resource/frontend/js/selectFilter.js"></script>