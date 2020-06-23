<link rel="stylesheet" type="text/css" href="/resource/frontend/css/index.css">
	<style type="text/css">
		.address {
			    padding: 34px 0 35px 25px;
			    font-size: 18px;
			    color: #368ae5;
			}
		.coinNet {
		    text-align: center;
		    font-size: 14px;
		}
		.coinNet a {
		    text-decoration: none;
		    display: inline-block;
		    height: 20px;
		    width: 50px;
		    vertical-align: middle;
		    background-color: #368ae5;
		    color: #fff;
		    border-radius: 2px;
		    margin: 0 5px;
		}
		.coinNet a:hover {
		    background-color: rgba(54,138,229,.6);
		}
		.qrImage {
		    height: 100px;
		    width: 100px;
		    margin: 25px auto 0;
		}
		.active{
			background-color: #fff;
		}
	</style>
<div id="main">
<div class="main_box" style="position: relative;">
	<div id="" class="currency_list_c">
		<p class="chose_c" data-i18n="d_1">Choose currencies</p>
		<ul class="currencyList" id="currencyList">
			<h2 data-i18n="trade_07">Main plate</h2>
			<!--<li class="USDT">
				<a href="./recharge.html/usdt_exchange"><img src="./recharge/5b28e353cd3bf.png" alt="" class="biIcon">USDT</a>
			</li>-->
		</ul>
	</div>
	<div class="raise right clearfix usdtbg" >

		<div class="coinAddress" style="display: none;">
			<p class="address" data-i18n="d_17">Deposit address</p>
			<div class="coinNet">
				<span class="addressqdr" id='addressqdr'></span>
				<!--<input type="text" disabled="disabled" class="addressqdr" name="addressqdr" id="addressqdr" value="" />-->
				<a  id="copy" href="javascript:;" data-clipboard-text="">Copy</a>
				<a href="javascript:;" id='qrImage'>QR code</a>
			</div>
			<div class="qrImage" style="display: none;">
				<!--<img src="./images/qrCode.png"/>-->
			</div>
			<p style="padding: 20px 0px 0 25px;">Check<span style="color: #368ae5;cursor: pointer;"> Deposit record</span>Track status</p>
			<div style="padding: 20px 0px 0 25px;">
				<p>Reminder:</p>
				<p>• Do not recharge any non-USDT assets at the above address, otherwise the assets will not be recovered.</p>
				<p>•USDT charging only supports the simple send method. The charging using other methods (send all) is temporarily unavailable. Please forgive me.。</p>
				<p>• After you recharge to the above address, you need the confirmation of the whole network node, one network confirmation and six network confirmation, then you can withdraw money.。</p>
				<p>• Minimum recharge amount: 100 USDT, less than the minimum amount of recharge will not be billed and can not be returned.</p>
				<p>• Your replenishment address will not change frequently, you can replenish; if there is any change, we will try to notify you through the website announcement or mail.</p>
				<p>• Make sure your computer and browser are secure to prevent information from being tampered with or leaked.</p>
			</div>
		</div>
		<div class="ybcoin" style="display: block;">
			<p class="lately" data-i18n="d_17">Recent Deposit</p>
			<ul class="latelyRecored marginB" id="latelyRecored">
				<!--<li>
					<div><img src="/resource/frontend/img/5a25057eca1d2.png" alt="" /></div>
					<p>
						订单充值成功
					</p>
					<p>
						数量：0.001ETH
					</p>
					<p>
						时间：2017-12-10 16：30
					</p>
				</li>-->
			</ul>
		</div>

	</div>
	<div class="norealName right" style="display: none;">
			<p class="norealNameTxt">You have not been authenticated by your real name and cannot buy or sell.<a href="/uc/verified">Certification</a></p>
	</div>
	<div class="clear"></div>
</div>
<div class="clear"></div>
</div>

<script type="text/javascript" src="/resource/frontend/js/jquery.qrcode.min.js"></script>
<script type="text/javascript" src="/resource/frontend/js/clipboard.min.js"></script>
<script>
 $(function() {
 	var has = window.location.hash;
	http.post('bargain/market', {}, function(res) {
	data = res.data;
	
	$.each(data, function(index,r) {
		  var reg = new RegExp(r.main);
			    var ac = '' 
			    if(reg.test(has)){
			    	//console.log(r.main);
			    	ac = r.main
			    }
		$('#currencyList').append(
		'<li class="'+r.main+' '+(ac==r.main?"active":"")+'" onclick="changeMoney($(this),'+index+')">'+
		'<a href="#'+r.main+'">'+
		'<img src="'+r.main_icon+'" alt="" class="biIcon">'+r.main+'</a>'+
		'</li>')
	})
	});
 })
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
					'<p style="text-align: center;margin-top: 10px;font-weight: bolder;font-size: 18px;">'+(r.tx_status=='success'?"Deposit success":(r.tx_status=='fail'?"Deposit fail":"Deposit wait..."))+'</p>'+
					'<p style=" text-align:  center; margin-top: 10px; font-size:  15px;color: #665;">Amount:'+
						r.value_dec
					+'</p>'+
					'<p style="text-align:  center;margin-top:  10px;font-size:  15px;color: #665;">Time'+
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
		console.log(res)
		data = res.data;
		ndata = data;
		$.each(data.list,function(index,r){
			var reg = new RegExp(r.name);
			if(reg.test(ha)){
				$('.addressqdr').html(data.list[index].addr)
				$('#copy').attr('data-clipboard-text',data.list[index].addr)
				$('.qrImage').qrcode({width:100,height: 100,text: ndata.list[index].addr});
			}
		})

	});
	var singalInfo = null;
	http.post('user/get-info',{},function(n){
		singalInfo = n.data
		if(/^\#[a-zA-Z]+$/g.test(ha)){

			reSattus();
		}
	})
	function reSattus(){
		//singalInfo.status=2
		if(singalInfo===null){
	 		$('.raise').css('display','none')
	 		$('.norealName').css('display','block');
	 	}else if(singalInfo.status==1){//审核中
	 		$('.raise').css('display','none');
	 		$('.norealName').css('display','block');
	 		$('.norealNameTxt').html('Wait patiently. Certification is in the process of auditing....')
	 	}else if(singalInfo.status==2){//审核通过
	 		$('.coinAddress').css('display','block');
	 		$('.raise').css('display','block');
	 		$('.norealName ').css('display','none');
	 		$('.ybcoin').css('display','block');
	 	}
	}
  	function changeMoney(ev,i){
	 	reSattus();
		$('.addressqdr').html(ndata.list[i].addr)
		$('#copy').attr('data-clipboard-text',ndata.list[i].addr)
		$('.qrImage').empty();
		$('.qrImage').qrcode({width:100,height: 100,text: ndata.list[i].addr});
	}
		var clipboard =  new ClipboardJS('#copy', {
			text: function(trigger) {
				 return trigger.getAttribute('data-clipboard-text');
			}
		});
		clipboard.on('success', function(e) {
			 http.info('Replication success')
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