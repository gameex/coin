<link rel="stylesheet" type="text/css" href="/resource/frontend/css/index.css">
<link rel="stylesheet" type="text/css" href="/resource/frontend/css/uc.css">
<div id="main" >
<div class="main_box" style="position: relative;">
	<div id="" class="currency_list_c">
		<p class="chose_c" data-i18n="d_1">Choose Coins</p>

		<ul class="currencyList" id="currencyList">

		</ul>
	</div>
	<div class="raise right bg_w clearfix " >

		<div class="ybc_list changetop coinAddress" style="display: none;">
					<div class="ybcoin">
				        <h2 class="left" style="padding-left:0"><font class= "DlCoinType"></font><font data-i18n="d_2"> Wallet</font></h2>
						<div class="clear"></div>
				    </div>
					<div class="clearfix">
					    <div class="userinbtc"> 
					    	<p class="inBTtext"><font data-i18n="d_3_1">This is your </font><font class= "DlCoinType"></font><font data-i18n="d_3_2"> Wallet address, please send your </font><font class= "DlCoinType"></font><font data-i18n="d_3_3"> Deposit to this address</font></p>
                            <button class="dianji" id="shengcheng" style="display: none;">Click to generate address</button>
					    	<div class="outwallet" type="ture">
					    	<span class="addrUrl addressqdr" ></span>
					    	<button class="addCope" id="copy" data-clipboard-text="">Copy Address</button>
					    	</div>
															
					        <div class="addQrcode">
					        	
					        	<div class="qrImage">
					        	<span class="memo">Scan the code</span>
								</div>
				        	</div>
			        	</div>
					</div>
									    <div class="ybc_list instructions">
				        <div class="ybcoin explains">
				            <h2 class="" data-i18n="d_6">Notice：</h2>
				            <div class="clear"></div>
				        </div>
				        							<p>1. <font data-i18n="trade_29">After Deposit</font> <b>12</b> <font data-i18n="trade_30">Tx confirm auto arrives</font></p>
		        			<p>2. <font data-i18n="trade_31">This address is your only address that can be deposit multiple times.</font></p>
                             <p>3. <font>Send only </font> <b><font class= "DlCoinType"></font></b> <font>to this deposit address. Sending any other coin or token to this address may result in the loss of your deposit.</font></p>
<!-- 		        			<p>3. <font data-i18n="trade_32">最小充币数量：</font>10.0 USDT</p>
 -->				    </div>
				    <p class="zend_help" style="line-height: 20px;color: #1048F8;text-decoration: none;margin-top: 10px;display: block;text-align: right;padding-right: 40px;"><a target="_blank" style="color: #1048F8;text-decoration: none;" href="<?= Yii::$app->config->info('WEB_LINK_REQUEST') ?>" data-i18n="[href]about_28"><font data-i18n="d_23">What's the problem? Immediate feedback</font></a></p>
				</div>	
				<div class="ybc_list ybc_make" style="display: none;border-bottom:0;">
					<div class="ybcoin">
						<h2 class="newchange" data-i18n="d_9">Coin record</h2>
						<ul class="time" id="time" >
							<li class="choseActive" data-time="1" data-i18n="u_e_12">Within 3 days</li>
							<li data-time="2" data-i18n="u_e_3">Within 1 month</li>
							<li data-time="3" data-i18n="u_e_4">Within 3 months</li>
							<div class="clear"></div>
						</ul>
					</div>	
					<div class="raise_list changetab">
						<table style="width:100%;"  align="center" border="0" cellpadding="0" cellspacing="0">
								<thead>
									<tr>
										<th >Type</th>
										<th >Coins</th>
										<th >Amount</th>
										<th >Time</th>
										<th >Detail</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
					</div>
							<div id="oImg" style="display: none">
								<img src="/resource/frontend/img/noData.png" alt="">
								<p>NO date</p>
							</div>
							<div class="allpage">
								<div class="pagecnt">
									<font data-i18n="c_20">total</font>&nbsp;<span id="allcount"></span>&nbsp;<font data-i18n="c_21">records</font>，<font data-i18n="c_20">total</font>&nbsp;<span id="allPage"></span>&nbsp;<font data-i18n="c_22">Page</font>，<font data-i18n="c_23">At present is</font>&nbsp;<span id="pages"></span>&nbsp;<font data-i18n="c_24">page</font>
								</div>
								<div class="jumpbtn">						
									<button id="firstPage" data-i18n="c_25">Home</button>
									<button id="upPage" data-i18n="c_26">Last page</button>
									<button id='nextPage'  data-i18n="c_27">Next page</button>
									<button id='endPage'  data-i18n="c_28">End page</button>
									<div class="jumpchoice">
										<font data-i18n="c_29">Jump to</font><input id="choicePage" value="" type="text"><font data-i18n="c_24">page</font>&nbsp;<button class="jumpPage" data-i18n="c_30">jump</button>
									</div>
								</div>
							</div>
					<input type="hidden" id="currency_id" value="104" page="1">
				</div>

	</div>
	<div class="norealName right" style="display: none;">
			<p class="norealNameTxt">You have not been authenticated by your real name and cannot buy or sell.<a href="/uc/verified">Go to authenticated.</a></p>
	</div>
	<div class="clear"></div>
</div>
<div class="clear"></div>
</div>

<script type="text/javascript" src="/resource/frontend/js/jquery.qrcode.min.js"></script>
<script type="text/javascript" src="/resource/frontend/js/clipboard.min.js"></script>
<script>
var currency = '';
var bTime  = timerChou(3);
var eTime = timerChou(0);
//var recharge(1,10,bTime,eTime);
var daysIndex = 0;
var pageIndex = 1;//页码；
var beginPage = 1;//开始页；
 $(function() {
   
 	var has = window.location.hash;
	http.post('bargain/balance', {}, function(res) {
    List = res.data.list;
	
	$.each(List, function(index,r) {
		  var reg = new RegExp(r.name);
			    var ac = '' 
			    if(reg.test(has)){
			    	//console.log(r.main);
			    	ac = r.name
			    }
 
		$('#currencyList').append(
		'<li class="'+r.name+' '+(ac==r.name?"active":"")+'" onclick="changeMoney($(this),'+index+')">'+
		'<a href="#'+r.name+'">'+
		'<img src="'+r.icon+'" alt="" class="biIcon">'+r.name+'</a>'+
		'</li>')
    //    var getpathname = window.location.pathname;
    //   var getname=getpathname.split("/uc/recharge");
    //    var currency=getname.join(""); 
	});
 })
	http.post('bargain/market', {}, function(res) {
    data = res.data;
    $.each(data, function(index,r) {
	var reg = new RegExp(r.main);
	var ac = '' 
	if(reg.test(has)){
	ac = r.main
	currency = r.main;
	}
    rechargeList(1,10,bTime,eTime,pageIndex,currency);
        $('.DlCoinType').html(currency)
	})
   })	
 })
	var count = null;
	function rechargeList(page,size,Btime,Etime,pageIndex,currency){
		pageIndex = pageIndex || 1;
    	http.post('withdraw/recharge-details',{transaction_type:1,page:page,page_size:size,begin_time:Btime,end_time:Etime,coin_symbol:currency},function(res){
      		count = res.data.total;
      		var Img = document.getElementById('oImg');
      		$('#allcount').empty().append(res.data.total);
      		$('#allPage').empty().append(Math.ceil(res.data.total/10));
            if(count > 10){
				$(".allpage").show();
				Img.style.display = 'none'
			}else{
				$(".allpage").hide();
				Img.style.display = 'block';
			};
      		$('#pages').empty().append(pageIndex);
      		$('.changetab tbody').empty();
    		$.each(res.data.data, function(index,r) {
      			//console.log(r)
    			var t = r['ctime']
    			var timer = tool.timerChuo(t);
    			if(r !=null){
    				Img.style.display = 'none'
    			}
    			$('.changetab tbody').append('<tr><td data-i18n="u_d_14">Deposits</td><td width="275" data-i18n="d_10">'+r.coin_symbol+'</td>'+
											'<td  data-i18n="d_11">'+r.change+'</td>'+
											'<td data-i18n="e_16">'+timer+'</td>'+
											'<td data-i18n="e_17">'+r.detial_type+'</td>'+
											'</tr>')    		});

    	})
    }
    
    
    $('#time li').click(function(){
    	$(this).addClass('choseActive').siblings().removeClass('choseActive');
    	var index = $(this).index();
    	daysIndex = index;
    	switch(index){
    		case 0://3天
    			 bTime  = timerChou(3);
    			 rechargeList(1,10,bTime,eTime,pageIndex,currency);
    			 pageIndex = 1;
    			 beginPage = 1;
    		break;
    		case 1://一个月；
    			bTime = timerChou(30.4);
    			rechargeList(1,10,bTime,eTime,pageIndex,currency);
    			pageIndex = 1;
    			beginPage = 1;
    		break;
    		case 2://三个月
    			bTime = timerChou(3*30.4);
    			rechargeList(1,10,bTime,eTime,pageIndex,currency);
    			pageIndex = 1;
    			beginPage = 1;
    		break;
    	}
    })
  	/*分页*/
  	
  	$('#nextPage').click(function(){//下一页
  		if((10*pageIndex)<=count){
  			pageIndex++;
  			beginPage +=1;
  			rechargeList(beginPage,10,bTime,eTime,pageIndex);
  		}else{
  			http.info('无数据')
  		}
  		$('#choicePage').val(' ');
  	})
    $('#upPage').click(function(){//上一页
    	if(pageIndex>0){
  			pageIndex--;
  			beginPage -=1;
  			rechargeList(beginPage,10,bTime,eTime,pageIndex);
  		}
  		$('#choicePage').val(' ');
    })
    $('#firstPage').click(function(){//首页
    	rechargeList(1,10,bTime,eTime,1);
    	$('#choicePage').val(' ');
    })
    $('#endPage').click(function(){//末页
    	var s = Math.ceil(count/10);
    	rechargeList(s,10,bTime,eTime,s);
    	$('#choicePage').val(' ');
    })
    $('.jumpPage').click(function(){//跳转
    	var s = $('#choicePage').val();
    	var r = Math.ceil(count/10);
    	if(r>=s){
    		rechargeList(s,10,bTime,eTime,s);
    	}else{
    		http.info('No data')
    	}
    	
    })

	function timerChou(sec){//sec 天计
    	return Math.floor(new Date()/1000 - sec*24*60*60);
    }
 	$('#currencyList').on('click','li',function(){
			//console.log()
			$(this).addClass('active').siblings().removeClass('active')
	})
	function recharge(type){
	 	http.post('withdraw/recharge-details',{transaction_type:type},function(res){
	 		//console.log(res)
	 		//$('#latelyRecored').empty()
	// 		<li><div><span>币类型:</span><span>'+r.coin_symbol+'</span></div>'+
	// 			'<div><span>充值状态:</span><span>'+(r.tx_status=='success'?"充值成功":"充值失败")+'</span></div>'+
	// 			'<div><span>充值数量:</span><span>'+r.value_dec+'</span></div>'+
	// 			'<div><span>充值时间:</span><span>'+timer+'</span></div>'+
	// 			'</li>'
	 		$.each(res.data.data, function(index,r) {
	 			//console.log(r['created_at'])
	 			var t = r['created_at']
	 			var timer = tool.timerChuo(t)
	 			$('#latelyRecored').append('<li>'+
						'<div style="text-align: center;margin-top: 10%;"><img style="width: 80px;height: 80px;" src="'+r.img_addr+'" alt="" /></div>'+
						'<p style="text-align: center;margin-top: 10px;font-weight: bolder;font-size: 18px;">'+(r.tx_status=='success'?"Deposits success":(r.tx_status=='fail'?"充值失败":"充值等待..."))+'</p>'+
						'<p style=" text-align:  center; margin-top: 10px; font-size:  15px;color: #665;">Amount:'+
							r.value_dec
						+'</p>'+
						'<p style="text-align:  center;margin-top:  10px;font-size:  15px;color: #665;">Time:'+
							timer
						+'</p>'+
					'</li>')

	 		});

	 	})
	 }
 	recharge(2)
	var ndata = null;
	var ha = window.location.hash
	http.post('bargain/balance', {
	 		asset_type:''
	 	}, function(res) {
		data = res.data;
		ndata = data;
		$.each(data.list,function(index,r){
			var reg = new RegExp(r.name);
			if(reg.test(ha)){
				$('.DlCoinType').html(data.list[index].name)
				if(data.list[index].addr != ''){
					$('.dianji').hide();
			 		$('.outwallet').show();
			 		$('.qrImage').show();
			 		$('.memo').show();

					$('.addressqdr').html(data.list[index].addr)
					$('#copy').attr('data-clipboard-text',data.list[index].addr)
					$('.qrImage').qrcode({width:120,height: 120,text: ndata.list[index].addr});

			 		if(data.list[index].memo!=''){
			 			$('.memo').html('<span style="white-space:nowrap;overflow:hidden;position:relative;font-size:14px;width:300px;height:30px;;font-weight:600;color:#e20f0f;text-align:left;left:-40px;top:-10px;">Deposits this coin must mark Memo/Tag:'+data.list[index].memo+'</span)');
			 		}
			 		else{
			 			$('.memo').html('<span style="white-space:nowrap;overflow:hidden;position:relative;font-size:14px;width:300px;height:30px;;font-weight:600;color:#555;left:30px;top:-10px;">Scan the code</span)');
			 		}					
				}else{
	 				$('.dianji').show();
	 				$(".dianji").attr("onclick","changeMoney(this,"+index+",1);");		
			 		$('.outwallet').hide();
			 		$('.qrImage').hide();
			 		$('.memo').hide();

			 		$('.addressqdr').html('');
			 		$('#copy').attr('data-clipboard-text','');
			 		$('.qrImage').qrcode({width:120,height:120,text:''});	 	 	 							
				}
	 			

			}
		})
	});
	var singalInfo = null;
	http.post('user/get-info',{},function(n){
		singalInfo = n.data
		if(/^\#[a-zA-Z]+$/g.test(ha)){
			reSattus();
		}
	 		$('.coinAddress').css('display','block');
	 		$('.raise').css('display','block');
	 		$('.norealName ').css('display','none');
	 		$('.ybcoin').css('display','block');
	 		$('.ybc_make').css('display','block');
	},function(){
	 		$('.coinAddress').css('display','block');
	 		$('.raise').css('display','block');
	 		$('.norealName ').css('display','none');
	 		$('.ybcoin').css('display','block');
	 		$('.ybc_make').css('display','block');
	})
	function reSattus(){
		//singalInfo.status=2
		if(singalInfo===null){
	 		$('.coinAddress').css('display','block');
	 		$('.raise').css('display','block');
	 		$('.norealName ').css('display','none');
	 		$('.ybcoin').css('display','block');
	 		$('.ybc_make').css('display','block');
	 	}else if(singalInfo.status==1){//审核中
	 		$('.coinAddress').css('display','block');
	 		$('.raise').css('display','block');
	 		$('.norealName ').css('display','none');
	 		$('.ybcoin').css('display','block');
	 		$('.ybc_make').css('display','block');
	 	}else if(singalInfo.status==2){//审核通过
	 		$('.coinAddress').css('display','block');
	 		$('.raise').css('display','block');
	 		$('.norealName ').css('display','none');
	 		$('.ybcoin').css('display','block');
	 		$('.ybc_make').css('display','block');
	 	}
	} 
  
$('#shengcheng').click(function(){
    $(this).attr("disabled","disabled"); 
    http.info('Generating, please wait')
    setTimeout(function(){
    location.reload();
               },2000)   
});

  	function changeMoney(ev,i,f){
  		var f = arguments[2] ? arguments[2] : 0;//设置参数f的默认值为0

	 	reSattus();
	 	//console.log(ndata.list[i].name);
	 	http.post('withdraw/generate-address',{coin_symbol:ndata.list[i].name,generate:f},function(res){
	 		if(res.code == 200 && res.data.addr != '-'){
	 			$('.dianji').hide();
		 		$('.outwallet').show();
		 		$('.qrImage').show();
		 		$('.memo').show();

		 		$('.addressqdr').html(res.data.addr);
		 		$('#copy').attr('data-clipboard-text',res.data.addr);
		 		$('.qrImage').qrcode({width:120,height: 120,text:res.data.addr});	
		 		if(res.data.memo!=''){
		 			$('.memo').html('<span style="white-space:nowrap;overflow:hidden;position:relative;font-size:14px;width:300px;height:30px;;font-weight:600;color:#e20f0f;text-align:left;left:-40px;top:-10px;">Deposits this coin must mark Memo/Tag:'+res.data.memo+'</span)');
		 		}
		 		else{
		 			$('.memo').html('<span style="white-space:nowrap;overflow:hidden;position:relative;font-size:14px;width:300px;height:30px;;font-weight:600;color:#555;left:30px;top:-10px;">Scan the code</span)');
		 		}
	 		   }else{
	 			if(res.code == 200){
	 				$('.dianji').show();
	 				$(".dianji").attr("onclick","changeMoney(this,"+i+",1);");
	 				//$(".dianji").attr("onclick","changeMoney(" + ev + i + 1 + ");");
	 			}else{
	 				//alert(res.message);
	 			//	$('.raise_list').hide();
	 				$('.dianji').hide();
	 				$('.coinAddress').hide();
	 				$('.ybcoin').hide();
	 			//	$('.changetab').hide();
	 				$('#oImg').show();
	 				$('#oImg p').html(res.message);
	 			}
		 		$('.outwallet').hide();
		 		$('.qrImage').hide();
		 		$('.memo').hide();

		 		$('.addressqdr').html('');
		 		$('#copy').attr('data-clipboard-text','');
		 		$('.qrImage').qrcode({width:120,height:120,text:''});	 	 			
	 			
	 		}
	 	})
		
		
		$('.qrImage canvas').remove();
		currency = ndata.list[i].name
		rechargeList(1,10,bTime,eTime,pageIndex,currency);
		$('.DlCoinType').html(ndata.list[i].name);
	}
		var clipboard =  new ClipboardJS('#copy', {
			text: function(trigger) {
				 return trigger.getAttribute('data-clipboard-text');
			}
		});
		clipboard.on('success', function(e) {
			 http.info('Copy success')
	});
	var f = false
  	$('#qrImage').click(function(){
  		if(f==false){
  			$('.qrImage').fadeIn();
  			f=true
  		}else{
  			$('.qrImage').fadeOut();
  			f=false
  		}

  	})
</script>