<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
?>
<link rel="stylesheet" type="text/css" href="/resource/frontend/css/index.css">
<link rel="stylesheet" type="text/css" href="/resource/frontend/css/uc.css">
<style type="text/css">
.currentForm label {
    width: 130px;
    display: inline-block;
    text-align: right;
    font-size: 12px;
    color: #333;
    line-height: 42px;
    padding-left: 19px;
}


</style>
<div id="main">
<div class="main_box" style="position: relative;">
	<div id="" class="currency_list_c">
		<p class="chose_c" data-i18n="d_1">Choose coins</p>
		<ul class="currencyList" id="currencyList">
		</ul>
	</div>
<div class="raise right bg_w clearfix">
		
	        <div class="support_ybc pass_ybc" id="verifyon" gasetting="1">
		        <!--  -->
		        	<div id="tagContent" class="passContent" style="display: none;">
		        		<div class="tagContent selectTag" id="tagContent0">
		                    <div class="choose_one clearfix">
								<h2 class="currency"><font class="typeP">BTC</font><font data-i18n="e_39"> Withdrawal</font></h2>
						    </div>
				
							<div class="currentForm">
								<div class="currencyAddr" currency_id="30">
								    <label class="choiceAddr" data-i18n="e_10">Withdrawal Address</label>		
									<input type="text" placeholder="Please enter the withdrawal address"   onkeyup="tiCoin($(this),'wallet_addr')" name="addre" id="addre" class="addre" value="" />			     
								</div>
								<div class="memo" currency_id="30" style="display: none">
								    <label class="choiceAddr" data-i18n="e_10">Memo/Tag</label>		
									<input type="text" placeholder="Please enter the note(Memo/Tag)"   name="memo" id="memo" value="" />			     
								</div>								
				        	    <div class="coin_rmb">
		        	    			<label for="" data-i18n="e_11">Withdrawals Amount</label>
		        					<input type="number" placeholder="Please enter the withdrawal amount."  onkeyup="tiCoin($(this),'value')" name="value" id="value" class="addre" value="" />
		        	    			<p><font data-i18n="e_29">Minimum withdrawal：</font><span id="limitAmount"></span><font data-i18n="e_41"></font></p>
		        	    		</div>

		        	    		<p class="surplus_amount"><font data-i18n="e_31">Account balance：</font><span id="balance_coin"></span><br>Withdrawals amount = Account balance - Withdrawal Fee<font data-i18n="e_41"></font></p>

		        	    		<div>
		        	    			<label for="" data-i18n="e_12">Withdrawal Fee</label>
		        	    			<input type="text" name="fee" disabled="" class="poundage">
								</div>

<!-- 								<div>
		        	    			<label for="" data-i18n="e_12">矿工费:</label>
		        	    			<input type="range" name="current" class="range" value="0" min="0" max="0" style="padding-left: 0;" id="range">
								</div>

								<div>
		        	    			<label for="" data-i18n="e_12" style="margin-right: 20px;"></label>
		        	    			慢<input type="submit" value="0" style="height: 30px;line-height: 10px;background: transparent;margin-top: -5px;margin-bottom: 30px;width:230px;" class="submitVal">快
								</div> -->
						<div>
						<label for="codes" data-i18n="u_h_14">Captcha</label>
						<div class="ybc_text left">
							<input name="ejcode" id="ejcode" value="" type="text" maxlength="6" placeholder="Enter the captcha" style="width: 150px;margin-left:-10px;margin-top: -5px;border: 1px solid #5973d8;"></div>
					  	<div class="ybc_hint left">
					      	<?php 
							echo Captcha::widget(['name'=>'captchaimg','captchaAction'=>'reg/captcha','imageOptions'=>['id'=>'captchaimg', 'title'=>'Change', 'alt'=>'Change', 'style'=>'cursor:pointer;'],'template'=>'{image}']); ?>
					              	</div>
                          		<div class="ybc_hint left"><span id="code2msg" class="wenan"></span></div>
					             </div>
								<div class="currencyAddr" currency_id="30">
								    <label class="choiceAddr" data-i18n="e_10">Email Code</label>		
									<input type="hidden" name="addre" id="phone" value="<?php echo $_SESSION['email'];?>"/>
									<input type="text" placeholder="Input Email code" name="addre" id="code" class="addre" value="" style="width: 150px;"/>
		                        	<input type="button" value="Send code" class="sendcode" onclick="sendcode(this)" id="sendCode" data-key="on" style="margin-left:5px;width:120px;">
		                        	<i class="tishis" id="code_msg"></i>
								</div>
		        	    		<div>
		        	    			<label for=""></label>
		        	    			
		        	    			<input type="button" value="submit" class="MentionCoin" id="MentionCoin" phone="">					        	    		
		        	    		</div>
		        	    		<div class="cue" style="padding-left:55px;" data-i18n="e_15">For the sake of your financial security, please do not directly withdraw to the ICO address. We will not deal with the distributeof future tokens.</div>					        	
					        </div>
				        	
			        	    <p class="zend_help" style="line-height: 20px;padding-bottom: 30px;color: #1048F8;text-decoration: none;margin-top: -20px;display: block;text-align: right;padding-right: 40px;"><a target="_blank" style="color: #1048F8;text-decoration: none;" href="<?= Yii::$app->config->info('WEB_LINK_REQUEST') ?>" data-i18n="[href]about_28"><font data-i18n="d_23">What's the problem? Immediate feedback</font></a></p>		        		</div>
		    		</div>

				    <div class="ybc_list ybc_withdrawal" style="margin-top: 16px;display: none;" >
						    <div class="ybcoin">
						        <h2 class="newchange" page="1" data-i18n="e_43">Withdrawal record</h2>
						        <ul class="time" id="time" >
									<li class="choseActive" data-time="1" data-i18n="u_e_12">Within 3days</li>
									<li data-time="2" data-i18n="u_e_3">Within 1month</li>
									<li data-time="3" data-i18n="u_e_4">Within 3months</li>
									<div class="clear"></div>
								</ul><span style="display: inline-block;text-align:right;height: 38px;line-height: 38px;vertical-align: top;" data-i18n="e_18">The application for withdrawal of currency can be withdrawn within 2 minutes.</span>
						        <div class="clear"></div>
						    </div>
						    <div class="raise_list">
								<table style="width:100%;"  align="center" border="0" cellpadding="0" cellspacing="0">
									<thead>
										<tr>
											<th >Type</th>
											<th >Coins</th>
											<th >Amonut</th>
											<th >Time</th>
											<th >Detail</th>
											
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
							<div id="oImg" style="display: none">
								<img src="/resource/frontend/img/noData.png" alt="">
								<p>No data</p>
							</div>
							<div class="allpage">
								<div class="pagecnt">
									<font data-i18n="c_20">Total</font>&nbsp;<span id="allcount"></span>&nbsp;<font data-i18n="c_21">records</font>，<font data-i18n="c_20">Total</font>&nbsp;<span id="allPage"></span>&nbsp;<font data-i18n="c_22">page</font>，<font data-i18n="c_23">At present is</font>&nbsp;<span id="pages"></span>&nbsp;<font data-i18n="c_24">page</font>
								</div>
								<div class="jumpbtn">						
									<button id="firstPage" data-i18n="c_25">Home</button>
									<button id="upPage" data-i18n="c_26">Last page</button>
									<button id='nextPage'  data-i18n="c_27">Next page</button>
									<button id='endPage'  data-i18n="c_28">End page</button>
									<div class="jumpchoice">
										<font data-i18n="c_29">Jump to</font><input id="choicePage" value="" type="text"><font data-i18n="c_24">page</font>&nbsp;<button class="jumpPage" data-i18n="c_30">Jump</button>
									</div>
								</div>
							</div>
						</div>
				
				<!--测试公测-->
				
			</div>
		</div>
  
	<div class="norealName right" style="display: none;">
			<p class="norealNameTxt">You have not been authenticated by your real name and cannot buy or sell.<a href="/uc/verified">Go to authenticated</a></p>
	</div>
<div class="clear"></div>
</div>
<div class="clear"></div>
</div>

<script> 


function alert_msg(data){ 
	layui.use(['layer','form'], function(){
			  var layer = layui.layer
			  ,form = layui.form;
			  //layer.msg(data)
			  layer.msg(data, {
				  //icon: 6,
				  time: 2000 //2秒关闭（如果不配置，默认是3秒）
				},function(){
					//console.log(11)
				});
	});
}


function lastTime(val){
	 val--;
	 val = val>=10?val:'0'+val;
	 val = val<=0?0:val;
	 return val
}

function sendcode(obj){
	user_name = $('#phone').val();
	if (user_name == "") {
		alert_msg('Please bind your email first');
		return;
	}
  	ejcode = $('#ejcode').val();
	//console.log(ejcode);
	if (ejcode == "") {
		alert_msg('Captcha cannot be empty');
		return;
	}  
	if(user_name.indexOf("@") != -1){
		var post_data={
			email:user_name,
			type:3,
		    ejcode:ejcode,                    
			os:'web',
		}
		var post_url = '/api/register/email-varcode';
	}else{
		var post_data={
			mobile_phone:user_name,
		    ejcode:ejcode,                    
			type:3,
			os:'web',
		}
		var post_url = '/api/register/mobile-varcode';
	}

	$.ajax({
	   type: 'POST',
	   url: post_url,
	   dataType: 'json',
	   data: post_data,
	   success: function(data){
	            if(data.code == 200){
					var v = 60
					var timer = setInterval(function(){
						v = lastTime(v)
						$(obj).val(v+'s to resend')
						if(v==0){
							clearInterval(timer)
							$(obj).val('Resend code')
						}
					},1000)
	                alert_msg('Send success');
	            }else{
	                alert_msg(data.message);
	            }
	   }
	});
}



var currency = '';
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
	});
 })
	http.post('exchange/market', {}, function(res) {
		data = res.data;
		$.each(data, function(index,r) {
		    var reg = new RegExp(r.main);
		    var ac = '' 
		    if(reg.test(has)){
		    	//console.log(r.main);
		    	ac = r.main
		    }
		})
		currency = has.split('#')[1];
		withdrawFee(currency);
		recharge(1,10,bTime,eTime,pageIndex,currency);
        $('.typeP').html(currency)
	})
})

function withdrawFee(currency){
	http.post('bank/withdraw-prepare', {
		'coin_symbol':currency
	}, function(res) {
		var List = res.data;
		$('#range').attr("value",0);
		if(res.code == 200){      
			$('.poundage').val(List.withdraw_fee+List.unit);
			$('#range').attr("value",List.current);
			$('.range').attr("min",List.low);
			$('.range').attr("max",List.height);
			$('.range').attr('step', List.low);
			$('.submitVal').val(List.current+currency);
			if(currency=='XRP'||currency=='EOS'){
				$('.memo').show();
			}else{
				$('.memo').hide();
			}
		}else{
			$('.raise_list').hide();
			$('.passContent').hide();
			$('.ybcoin').hide();
			$('.changetab').hide();
			$('#oImg').show();
			$('#oImg p').html(res.message);			
			//alert(res.message);
		}
	});
}
$('.range').change(function(){
	$('.submitVal').val($(this).val()+currency);
})
	var count = null;
	function recharge(page,size,Btime,Etime,pageIndex,currency){
		pageIndex = pageIndex || 1;
		var Img = document.getElementById('oImg');
    	http.post('chat/init-pc',{transaction_type:10,page:page,page_size:size,begin_time:Btime,end_time:Etime,coin_symbol:currency},function(res){
 			if(res.code == 200){
	      		count = res.data.total;
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
	    			var t = r['ctime']
	    			var timer = tool.timerChuo(t);
					Img.style.display = 'none'
					$('.changetab tbody').append('<tr><td data-i18n="u_d_14">提现</td><td width="275" data-i18n="d_10">'+r.coin_symbol+'</td>'+
											'<td  data-i18n="d_11">'+r.value_dec+'</td>'+
											'<td data-i18n="e_16">'+r.created_at+'</td>'+
											'<td data-i18n="e_17">'+r.status+'</td>'+
											'</tr>')
	    		});
    		}else{
    			$('.changetab tbody').empty();
    			Img.style.display = 'block';
    			$(".allpage").hide();
    		}
    	})
    }
    function timerChou(sec){//sec 天计
    	return Math.floor(new Date()/1000 - sec*24*60*60);
    }
    var bTime  = timerChou(3);
    var eTime = timerChou(0);
    // recharge(1,10,bTime,eTime);
    var daysIndex = 0;
    var pageIndex = 1;//页码；
    var beginPage = 1;//开始页；
    $('#time li').click(function(){
    	$(this).addClass('choseActive').siblings().removeClass('choseActive');
    	var index = $(this).index();
    	daysIndex = index;
    	switch(index){
    		case 0://3天
    			 bTime  = timerChou(3);
    			 recharge(1,10,bTime,eTime,pageIndex,currency);
    			 pageIndex = 1;
    			 beginPage = 1;
    		break;
    		case 1://一个月；
    			bTime = timerChou(30.4);
    			recharge(1,10,bTime,eTime,pageIndex,currency);
    			pageIndex = 1;
    			beginPage = 1;
    		break;
    		case 2://三个月
    			bTime = timerChou(3*30.4);
    			recharge(1,10,bTime,eTime,pageIndex,currency);
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
  			recharge(beginPage,10,bTime,eTime,pageIndex,currency);
  		}else{
  			http.info('无数据')
  		}
  		$('#choicePage').val(' ');
  	})
    $('#upPage').click(function(){//上一页
    	if(pageIndex>0){
  			pageIndex--;
  			beginPage -=1;
  			console.log(currency)
  			recharge(beginPage,10,bTime,eTime,pageIndex,currency);
  		}
  		$('#choicePage').val(' ');
    })
    $('#firstPage').click(function(){//首页
    	recharge(1,10,bTime,eTime,1,pageIndex,currency);
    	$('#choicePage').val(' ');
    })
    $('#endPage').click(function(){//末页
    	var s = Math.ceil(count/10);
    	recharge(s,10,bTime,eTime,s,currency);
    	$('#choicePage').val(' ');
    })
    $('.jumpPage').click(function(){//跳转
    	var s = $('#choicePage').val();
    	var r = Math.ceil(count/10);
    	if(r>=s){
    		recharge(s,10,bTime,eTime,s,currency);
    	}else{
    		http.info('无数据')
    	}
    	
    })
		var singalInfo = null;
		var ha = window.location.hash;
		var nowData = null;
		var tixianObj = {};
		 http.post('bargain/balance', {
		 		asset_type:''
		 	}, function(res) {
			//console.log(res)
			data = res.data;
			nowData = data;
			$.each(data.list,function(index,r){
				var reg = new RegExp(r.name);
				if(reg.test(ha)){
					//console.log(r.name)
					$('.typeP').html(r.name);
					$('#balance_coin').html(r.available+r.name)
					$('#limitAmount').html(r.limit_amount+r.name)
					tixianObj.coin_symbol = r.name;
				}
			})

		});
		$('#currencyList').on('click','li',function(){
			$(this).addClass('active').siblings().removeClass('active')
			var icon_class = $(this).children(1).html();
			icon = icon_class.split('>')[1];
			currency = icon;
			$('#range').attr("value",0);
			withdrawFee(icon)
			recharge(1,10,bTime,eTime,pageIndex,icon);
		})
		http.post('user/get-info',{},function(n){
			singalInfo = n.data
			//console.log(singalInfo)
			if(/^\#[a-zA-Z]+$/g.test(ha)){
				reSattus();
			}
		 		$('.rechargeMethods').css('display','block');
		 		$('.raise').css('display','block');
		 		$('.norealName ').css('display','none');
		 		$('.ybcoin').css('display','block');
		 		$('.passContent').css('display','block');
		 		$('.ybc_withdrawal').css('display','block');
		},function(){
		 		$('.rechargeMethods').css('display','block');
		 		$('.raise').css('display','block');
		 		$('.norealName ').css('display','none');
		 		$('.ybcoin').css('display','block');
		 		$('.passContent').css('display','block');
		 		$('.ybc_withdrawal').css('display','block');
		})
		function reSattus(){
			//console.log(singalInfo)
			//singalInfo.status=2
			if(singalInfo===null){
		 		$('.rechargeMethods').css('display','block');
		 		$('.raise').css('display','block');
		 		$('.norealName ').css('display','none');
		 		$('.ybcoin').css('display','block');
		 		$('.passContent').css('display','block');
		 		$('.ybc_withdrawal').css('display','block');
		 	}else if(singalInfo.status==1){//审核中
		 		$('.rechargeMethods').css('display','block');
		 		$('.raise').css('display','block');
		 		$('.norealName ').css('display','none');
		 		$('.ybcoin').css('display','block');
		 		$('.passContent').css('display','block');
		 		$('.ybc_withdrawal').css('display','block');
		 	}else if(singalInfo.status==2){//审核通过
		 		$('.rechargeMethods').css('display','block');
		 		$('.raise').css('display','block');
		 		$('.norealName ').css('display','none');
		 		$('.ybcoin').css('display','block');
		 		$('.passContent').css('display','block');
		 		$('.ybc_withdrawal').css('display','block');
		 	}
		 	//console.log(singalInfo)
		}
        

		function changeMoney(ev,i){
		 	reSattus();
		 	tixianObj.coin_symbol = nowData.list[i].name		 		
		 	$('.typeP').html(nowData.list[i].name);
			$('#balance_coin').html(nowData.list[i].available+nowData.list[i].name)
			$('#limitAmount').html(nowData.list[i].limit_amount+nowData.list[i].name)
			if(nowData.list[i].name=='XRP'||nowData.list[i].name=='EOS'){
				$('.memo').show();
			}else{
				$('.memo').hide();
			}
		}

		function tiCoin(ev,type){
			tixianObj[type] = ev.val()

		}
		$('#MentionCoin').click(function(){
			user_name = $('#phone').val();
			code = $('#code').val();
			if (user_name == "") {
				alert_msg('请先绑定邮箱');
				return;
			}
			if (code == "") {
				alert_msg('验证码不能为空');
				return;
			}


			//console.log(tixianObj);
			tixianObj.wallet_addr = $('#addre').val();
			tixianObj.memo = $('#memo').val();
			tixianObj.value = $('#value').val();
			tixianObj.current = $('#range').val();
			tixianObj.code = code;
			if(tixianObj.wallet_addr){
				if(tixianObj.value){
					http.post('withdraw/turn-out',tixianObj,function(res){
						http.info(res.message);
                        setTimeout(function(){
                        location.reload();
                        },1000);
						// recharge(1,10,bTime,eTime);
						recharge(1,10,bTime,eTime,pageIndex,currency);
					},function(err){
						http.info(err.message);
						// recharge(1,10,bTime,eTime);
					})
				}else{
					http.info('提币数量不能0')
				}
			}else{
				http.info('提币地址不能为空')
			}

		})
</script>