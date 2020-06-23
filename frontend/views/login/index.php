<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
?>
<style>
           .user{
            font-size: 20px;
       text-align: center;
       color: #2b37dd;
       padding-bottom: 20px;
             }
			.flexslider .slides{
				background-color: #141463;
			}
			.top_body .qcodeImg {
				margin: 0 20px 21px 0;
			}

			.top_body .wechatEr p {
				width: 230px;
				margin: 10px 0 0 22px;
			}
			.nav li a,.loginArea a, .person a {
				color:#fff;
			}
			.loginArea a:nth-child(2) {
				border: 1px solid #fff;
			}
			.denglu {
				background: #3b85f5;
                margin-top:10px;
                margin-left: 78px;
                border-radius: 6px;
				font-size: 14px;
				color: #fff;
				cursor: pointer;
				border-bottom: none;
				display: block;
				padding: 10px 0;
                width: 250px;
              text-align:center;
              vertical-align: middle;
			}
          .denglu:hover{
            background: #3577db;
            }
	body {
		min-width: 100%;
	}

	.headerUser .headerRight {
		width: 100%;
	}

	.wapper {
		width: 100%
	}

	.headertop .left {
		margin-left: 20px;
	}

	.nav li:nth-child(1) {
		margin-left: 32px;
	}

	.headerUser .person {
		margin-right: 20px;
	}          
   .headertop .left img{
   width:100%;
   height: 50px;
    }               
  .headertop {
    height: 60px;
    min-width:1400px;
    width:100%;
    background-color: #fff;
    margin-bottom: 0px;
    position: relative;  
     }
  	.nav li a {
    line-height: 60px;
    height: 50px;
    padding: 0 15px;
    font-size: 14px;
    margin: 0 5px;
    color: #000;
  }	
   	.nav li a:hover {
    line-height: 60px;
    height: 50px;
    padding: 0 15px;
    font-size: 14px;
    margin: 0 5px;
    color: #545de2;
    border-bottom: 2px solid #2c5ad7;
  }	 
  
  .loginArea a, .person a{    
    color: #000;
  }
  .headerUser .person .navLift li a{
    color: #000;    
   } 
  .headerUser .person .navLift li a:hover {
    color: #545de2;
    border-bottom: 2px solid #1b4bfa; 
   } 
		</style>
		<a class='yc' style=''></a>
		<div id="main" class="mainClass" style="padding:0 0 200px 0;min-height:auto;margin-bottom:0;">
			<div class="flexslider">
                <div class="main_bg" style="top:150px;">
				<div class="login_tongyi" style="right: 350px;height: 300px;top:-30px;width: 400px;box-shadow:0 3px 6px 0 rgba(95, 149, 207, 0.5);">
					<h2 class="user" data-i18n="a_login">登录</h2>
					<span class="note" id="codemsg"></span>
					<form id="signupForm">
						<ul class="login">
							<li>
								<input name="phone" id="phone" type="text" onkeyup='getLoginVal("phone")' placeholder="请输入手机号码或邮箱">
							</li>
							<li>
								<input name="pwd" id="pwd" type="password" onkeyup='getLoginVal("pwd")' placeholder="请输入密码" class="radius">
								<input type="hidden" name="type" id="types" value="1">
								<input type="hidden" name="gacode" id="gacode" value="">
							</li> 
                        <!--<div class="ybc_user clearfix">
                        	
						<div class="ybc_text left">
							<input name="lgcode" id="lgcode" value="" type="text" maxlength="6" placeholder="请输入校验码" style="width: 150px;margin: 0 0 0 72px;"></div>
						<div class="ybc_hint left">
						<?php 
							echo Captcha::widget(['name'=>'captchaimg','captchaAction'=>'reg/captcha','imageOptions'=>['id'=>'captchaimg', 'title'=>'换一个', 'alt'=>'换一个', 'style'=>'cursor:pointer;'],'template'=>'{image}']); ?>
						</div>
						<div class="ybc_hint left"><span id="code2msg" class="wenan"></span></div>
					    </div>-->
						<!--	<li id="checkcode" style="" class="clearfix">
								<input type="text" class="code left" name="vcode" id="code" placeholder="请输入验证码" maxlength="6">
								<input type="button" value="发送验证码" class="sendcode right" onclick="sendcodes()" id="sendcode" data-key="on">
							</li>-->
							<li>
								<!--<input class="denglu" value="登录" type="button" onclick="checkform(event)" id="denglu">-->
								<a class="denglu" id='goToLogin' href="javascript:;">登录</a>
							</li>
							<li class="forget" style="margin-top:20px;">
								<a href="./reg" class="zhuce" data-i18n="registration">立即注册</a>
								<a href="./findpw" class="forgetpwd" data-i18n="a_password">忘记密码</a>
							</li>
						</ul>
					</form>
				</div>
			</div>

		</div>
				<ol class="flex-control-nav flex-control-paging">

				</ol>
			</div>
	<!--		<script>
				$(function(){
                  	$('.yc').click();
					http.post('start/start-page',{type:1},function(res){
				 		console.log(res)
				 		res.data && $.each(res.data, function(index,r) {
				 			$(".slides").append('<li title="'+r.title+'" style="background: url(&quot;'+r.img+'&quot;) 50% 0px no-repeat; height: 640px; width: 100%; float: left; margin-right: -100%; position: relative; opacity: 0; display: block; z-index: 1;" class="" data-thumb-alt="">'+
										'</li>')
				 			$('.flex-control-nav').append('<li>'+
						                     '<a href="javascript:;" class="">'+index+'</a>'+
					                         '</li>')
				 		});
				 		LunBo();
				 	})
					function LunBo(){
						var $li = $('.flexslider ul li');
						var count = 0 //计时器;
						$li.eq(count).addClass('flex-active-slide').css('opacity', '1')
						$('.flexslider ol li:eq(' + count + ') a').addClass('flex-active')

						function rightMove() {
							$li.eq(count).removeClass('flex-active-slide').css('opacity', '0');
							$('.flexslider ol li:eq(' + (count) + ') a').removeClass('flex-active');
							count++;
							if(count >= $li.length) {
								count = 0;
							}
							$li.eq(count).addClass('flex-active-slide').css('opacity', '1')
							$('.flexslider ol li:eq(' + count + ') a').addClass('flex-active');

						}
						var timer = setInterval(function() {
							rightMove()
						}, 3000)
						//$('.flexslider ul li')
						//点击按钮；
						$('.flexslider ol li').click(function() {
							count = $(this).index()
							//console.log(count)
							$li.removeClass('flex-active-slide').css('opacity', '0').eq(count).addClass('flex-active-slide').css('opacity', '1')
							$('.flexslider ol li a').removeClass('flex-active');
							$('.flexslider ol li:eq(' + count + ') a').addClass('flex-active');
						})
					}
					var tradeName = sessionStorage.getItem('tradeName')

					//$('.trading_nav a').attr('href', './trade?changeName=' + (tradeName ? tradeName : 'BTCUSDT'))

				})
			</script>-->

		<div class="bg_double" id="doubles" style="display:none">
			<div class="doublebox">
				<h5 data-i18n="a_3">输入双重验证密码</h5>
				<ul class="doubleList">
					<li>
						<input type="text" name="" id="doublecode" value="" placeholder="请输入双重验证密码" class="styleinpt" style="margin: 0 0 10px 0;" maxlength="6" onkeyup="javascript:this.value=this.value.replace(/\D/gi,&#39;&#39;);KeyDowns(event);"><br>

						<i class="tishi" id="doubleMsg" style="padding-top: 10px;"></i>
					</li>
					<li>
						<input type="button" value="确定" class="verifition" style="margin: 40px 0;" onclick="cancelDouble()" id="sure">
					</li>
					<li>
						<a href="./helpCenter.html/articles/360002711393" target="_blank" class="loseup" data-i18n="a_5">双重验证密码丢失？</a>
					</li>
					<li class="wechatEr">
						<p data-i18n="a_6">使用微信“双重验证”小程序用户可使用微信扫一扫右侧二维码快速打开</p>
				<!--		<img src="./index/wechats.png" class="qcodeImg"> -->
					</li>
				</ul>
				<i class="close" onclick="goOut()"></i>
			</div>
		</div>


<!--<script type="text/javascript" src="commonJs/tool.js"></script>-->
<script type="text/javascript">
	//./userInfoCenter.html
	var loginObj = {}

	function getLoginVal(type) {
		loginObj[type] = $('#' + type).val()
		//console.log(loginObj)
	}
	//tool.loginStatus($('.loginArea'),$('.person'),$('.main_bg'))
	
	// function loginEnter(){
	// 	loginObj['phone'] = $('#phone').val();
	// 	loginObj['pwd'] = $('#pwd').val();
	// 	var phone = tool.detectionData.checkData({
	// 		name: 'phone',
	// 		val: loginObj.phone,
	// 		string: '手机号'
	// 	})
	// 	var pswd = phone && tool.detectionData.checkData({
	// 		name: 'pwd',
	// 		val: loginObj.pwd,
	// 		string: '密码'
	// 	})
	// 	var $this = this;
	// 	if(phone && pswd) {
	// 		http.post('register/sign', {
	// 			mobile_phone: loginObj.phone,
	// 			password: loginObj.pwd
	// 		}, function(res) {
	// 			http.info(res.message)
	// 			console.log(res.data)
	// 			loginObj = null;
	// 			//localStorage.setItem('access_token',res.data.access_token);
	// 			$('.person').css('display','block')
	// 			window.location.href = '/'
	// 		},function(err){
	// 			http.info(err.message)
	// 		})
	// 	}
	// }
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



function loginEnter(){
	user_name = $('#phone').val();
	password = $('#pwd').val();
	if (user_name == "") {
		alert_msg('手机号或邮箱不能为空');
		return;
	}
	if (password == "") {
		alert_msg('密码不能为空');
		return;
	}
    lgcode = $('#lgcode').val();
	//console.log(lgcode);
	if (lgcode == "") {
		alert_msg('校验码不能为空');
		return;
	}
	if(user_name.indexOf("@") != -1){
		var post_data={
			email:user_name,
			password:password,
          	lgcode:lgcode,          
			os:'web',
		}
		var post_url = '/api/register/email-sign';
	}else{
		var post_data={
			mobile_phone:user_name,
			password:password,
          	lgcode:lgcode,
			os:'web',
		}
		var post_url = '/api/register/sign';
	}

	$.ajax({
	   type: 'POST',
	   url: post_url,
	   dataType: 'json',
	   data: post_data,
	   success: function(data){
	            if(data.code == 200){
	                alert_msg('登录成功');
               setTimeout(function(){
				window.location.href = '/'
               },1000)   
	            }else{
	                alert_msg(data.message);
	            }
	   }
	});

}










	$('#goToLogin').click(function() {
		loginEnter();
	})
	$('#pwd').keydown(function(ev){
    	ev =ev || event 
    	if(ev.keyCode==13)loginEnter();
    })
  
</script>