<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
?>
<style type="text/css">
.ybc_user_nav {
	padding-bottom: 50px;
}
#codes {
	width: 150px;
}
html,body {
	background: #F5F9FE;
}
.invitaionLink {
	text-align: left;
	padding-left: 405px;
	margin-bottom: 10px;
	color: #368ae5;
	font-size: 14px;
	display: block;
	width: 160px;
}
.ybc_text .ybc_next{
	display: inline-block;
	text-decoration: none;
	color: #fff;
    margin-left: 20px;

}
.ybc_section {
    height: 500px;
}
</style>

<div class="section">
	<ul class="ybc_user_nav clearfix Font18" id="registerList">
		<a class="activeds" data-i18n="u_h_1" style="white-space:nowrap;">Email register<img src="//static.digifinex.vip/Home/images/dft/hot.png" style="margin:-4px 0 0 6px;"></a>
        <a class="" data-i18n="u_h_2">Phone register</a>
	</ul>
	<div class="ybc_section">
		<form id="stepTwo" class="steps">
			<div id="step1" >
                    <div class="ybc_user clearfix">
						<div class="ybc_label left"><label for="email" data-i18n="u_h_13">Email:</label></div>
						<div class="ybc_text left"><input name="email" id="emails" type="email" placeholder="Please enter your Email address."></div>
						<div class="ybc_hint left"><span id="emailmsg" class="wenan"></span></div>
					</div>
                       <div class="ybc_user clearfix">
						<div class="ybc_label left"><label for="codes" data-i18n="u_h_14">Behavior captcha:</label></div>
						<div class="ybc_text left">
							<input name="ejcode" id="ejcode" value="" type="text" maxlength="6" placeholder="Enter the captcha code." style="width: 180px;"></div>
						<div class="ybc_hint left">
						<?php 
							echo Captcha::widget(['name'=>'captchaimg','captchaAction'=>'reg/captcha','imageOptions'=>['id'=>'captchaimg', 'title'=>'Change', 'alt'=>'Change', 'style'=>'cursor:pointer;margin-left:10px;'],'template'=>'{image}']); ?>
						</div>
						<div class="ybc_hint left"><span id="code2msg" class="wenan"></span></div>
					</div>
              
					<div class="ybc_user clearfix">
						<div class="ybc_label left"><label for="codes" data-i18n="u_h_14">Email code:</label></div>
						<div class="ybc_text left">
							<input name="vCode" id="codes2" value="" type="text" maxlength="6" placeholder="Enter the email code" style="width: 150px;"></div>
						<div class="ybc_hint left">
							<input type="button" class="button" value="Get code" id="msgt" data-key="on" onclick="get_email_code(this)"></div>
						<div class="ybc_hint left"><span id="code2msg" class="wenan"></span></div>
					</div>
                    <div class="ybc_user clearfix" style="color:#333">
                    <span>If you haven't received the email code for a long time, please check your spam.</span>
                    </div>
					<div class="ybc_user clearfix">
						<div class="ybc_label left"><label for="pwd2" data-i18n="u_h_6">Login password:</label></div>
						<div class="ybc_text left"><input id="pwd2" name="pwd" type="password" placeholder="Please enter your login password. The password length is 6-20 bits." maxlength="20"></div>
						<div class="ybc_hint left"><span id="pwd2msg" class="wenan"></span></div>
					</div>
					<div class="ybc_user clearfix">
						<div class="ybc_label left"><label for="repwd2" data-i18n="u_h_8">Repeat the password:</label></div>
						<div class="ybc_text left"><input id="repwd2" name="ckpwd" type="password" placeholder="Please repeat your login password" maxlength="20"></div>
						<div class="ybc_hint left"><span id="repwd2msg" class="wenan"></span></div>
					</div>
					<div class="ybc_user clearfix">
						<div class="ybc_label left"><label for="pid" data-i18n="u_h_10">Invitation code:</label></div>
						<div class="ybc_text left"><input name="pid" id="invitioncode2" type="text" placeholder="Non essential items" maxlength="6"></div>
						<div class="ybc_hint left"><span id="codex2msg" class="wenan"></span></div>
					</div>
              <div class="ybc_user clearfix">
              <div class="regCheck">
                   <input type="checkbox" class="checkcodes" checked="checked" name="risk" >
					<i class="check_bg"></i>   
              <span class="protocol">I have read <?= Yii::$app->config->info('WEB_APP_NAME') ?>'s letter carefully and agree with it
              <a class="jump" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_AGREEMENT') ?>">Terms of use</a>，
              <a class="jump" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_PRIVACY') ?>">Privacy policy</a>。
              </span>
              </div>   
              </div>  
					<div class="ybc_user clearfix">
						<div class="ybc_label left"><label>&nbsp;</label></div>
						<div class="ybc_text left"><input value="Register" class="ybc_next" onclick="reg_by_email()" type="button" id="registers2"></div>                  
					</div>
              </div>
		</form>
		<form id="stepOne" class="steps" style="display:none;">
			<div id="step1">
				<div class="ybc_user clearfix">
					<div class="ybc_label left"><label for="phone" data-i18n="u_h_3">mobile:</label></div>
					<div class="ybc_text left"><input type="password" style="z-index:-9999;position:absolute"><input name="phone" id="phone" type="tel" onkeyup='getval("phone")' maxlength="20" placeholder="Enter your phone number."></div>
					<div class="ybc_hint left"><span id="phonemsg"></span></div> 
                </div>  
               	<div class="ybc_user clearfix">
					<div class="ybc_label left"><label for="codes" data-i18n="u_h_14">Behavior captcha:</label></div>
					<div class="ybc_text left">
						<input name="mjcode" id="mjcode" value="" type="text" maxlength="6" placeholder="Please enter the code." style="width: 180px;"></div>
					<div class="ybc_hint left">
					<?php 
						echo Captcha::widget(['name'=>'captchaimg','captchaAction'=>'reg/captcha','imageOptions'=>['id'=>'captchaimg2', 'title'=>'Change', 'alt'=>'Change', 'style'=>'cursor:pointer;margin-left:10px;'],'template'=>'{image}']); ?>
					</div>
					<div class="ybc_hint left"><span id="code2msg" class="wenan"></span></div>
				</div>             
				<div class="ybc_user clearfix">
					<div class="ybc_label left"><label for="codes" data-i18n="u_h_5">Verification:</label></div>
					<div class="ybc_text left"><input name="codes" id="codes" value="" type="text" onkeyup='getval("codes")' maxlength="6" placeholder="Enter the phone code"></div>
					<div class="ybc_hint left"><input type="button" class="button" value="Get code" id="msgt1" data-key="on" ></div>
					<div class="ybc_hint left"><span id="codemsg"></span></div>
				</div>
				<div class="ybc_user clearfix">
					<div class="ybc_label left"><label for="pwd" data-i18n="u_h_6">Login password:</label></div>
					<div class="ybc_text left"><input id="pwd" onkeyup='getval("pwd")' name="pwd" type="text" placeholder="Please enter your login password. The password length is 6-20 bits." maxlength="20" onfocus="this.type=&#39;password&#39;"></div>
					<div class="ybc_hint left"><span id="pwdmsg"></span></div>
				</div>
				<div class="ybc_user clearfix">
					<div class="ybc_label left"><label for="repwd" data-i18n="u_h_8">Repeat the password:</label></div>
					<div class="ybc_text left"><input id="repwd" onkeyup='getval("repwd")' name="ckpwd" type="text" placeholder="Please repeat your login password" maxlength="20" onfocus="this.type=&#39;password&#39;"></div>
					<div class="ybc_hint left"><span id="repwdmsg"></span></div>
				</div>
				<div class="ybc_user clearfix">
					<div class="ybc_label left"><label for="pid" data-i18n="u_h_10">Invitation code:</label></div>
					<div class="ybc_text left"><input name="invitioncode" onkeyup='getval("invitioncode")' id="invitioncode" type="text" placeholder="Non-obligatory items" maxlength="6"></div>
					<div class="ybc_hint left"><span id="codexmsg"></span></div>
                </div>  
              <div class="ybc_user clearfix">
              <div class="regCheck">
                   <input type="checkbox" class="checkcodes" checked="checked" name="risk" >
					<i class="check_bg"></i>   
              <span class="protocol">I have read <?= Yii::$app->config->info('WEB_APP_NAME') ?>'s letter carefully and agree with it
              <a class="jump" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_AGREEMENT') ?>">Terms of use</a>，
              <a class="jump" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_PRIVACY') ?>">Privacy policy</a>。
              </span>
              </div>   
              </div>                
                <div class="ybc_user clearfix">
				    <div class="ybc_label left"><label>&nbsp;</label></div>                  
                    <div class="ybc_text left"><input value="Register" class="ybc_next"  type="button" id="registers"></div> 
                </div>
            </div>    
		</form>
	</div>
</div>  
<script>
var inObj = {};
function getval(type,e){
	inObj[type] = $('#'+type).val()
}

function GetQueryString(name)
{
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!=null)return  unescape(r[2]); return null;
}
 

$(document).ready(function(){
	var code=GetQueryString("code");

	if(code !=null && code.toString().length>1)
	{
	   $('#invitioncode').val(code);

	   $('#invitioncode2').val(code);

	   $('#invitioncode').attr("disabled", true);
	   
	   $('#invitioncode2').attr("disabled", true);
	}

});



$('#registerList a').click(function() {
	var Index = $(this).index()
	console.log(Index);
	$('#registerList a').removeClass('activeds').eq(Index).addClass('activeds')
	$('.steps').css('display', 'none').eq(Index).css('display', 'block')
})

function lastTime(val){
	 val--;
	 val = val>=10?val:'0'+val;
	 val = val<=0?0:val;
	 return val
}

$('#msgt1').click(function(){
  	var mjcode = $('#mjcode').val();
	if (mjcode == "") {
		alert_msg('Code cannot be empty');
		return;
    }  
	var checkVal = tool.detectionData.checkData({
		name:'phone',
		val:inObj.phone,
		string:'手机号'
	})
	var $this = this

	if(checkVal){
		http.post('register/mobile-varcode',{mobile_phone:inObj.phone,type:1},function(res){
			http.info(res.message)
			var v = 60
			var timer = setInterval(function(){
				v = lastTime(v)
				$($this).val(v+'s can resend')
				if(v==0){
					clearInterval(timer)
					$($this).val('Resend Code')
				}
			},1000)

		},function(err){
			http.info(err.message)
		})
	}
})

$('#registers').click(function(){
var phone = tool.detectionData.checkData({
	name:'phone',
	val:inObj.phone,
	string:'手机号'
})
var codes = phone&&tool.detectionData.checkData({
	name:'codes',
	val:inObj.codes,
	string:'验证码'
})
var pwd =codes&& tool.detectionData.checkData({
	name:'pwd',
	val:inObj.pwd,
	string:'密码'
})
var repwd = pwd&&tool.detectionData.checkData({
	name:'repwd',
	val:inObj.repwd,
	string:'重复密码'
})

var invitioncode = $('#invitioncode').val();

if(phone && codes && pwd && repwd){//请求接口
	if(pwd === repwd){
		http.post('register/register',{
			mobile_phone:inObj.phone,
			varcode:inObj.codes,
			password:inObj.pwd,
			repassword:inObj.repwd,
			code:invitioncode
		},function(res){
			http.info(res.message)
			inObj = null;
			//console.log(res.data);
			/*localStorage.setItem('access_token',res.data.access_token)*/
			setTimeout(function(){
				window.location.href = './uc/index'
			},2000)
		},function(err){
		 		http.info(err.message)
		 	})
	}else{
		http.info('Inconsistent passwords')
	}
}
})

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

function get_email_code(obj){
	var email_addr = $('#emails').val();
  	var ejcode = $('#ejcode').val();
//	console.log(ejcode);
	if (ejcode == "") {
		alert_msg('Ejcode cannot be empty');
		return;
	}
	if (email_addr == "") {
		alert_msg('Email cannot be empty');
		return;
	}

	var post_data={
		email:email_addr,
		ejcode:ejcode,      
		type:1,
	}
	$.ajax({
	   type: 'POST',
	   url: '/api/register/email-varcode',
	   dataType: 'json',
	   data: post_data,
	   success: function(data){
	            if(data.code == 200){
					var v = 60
					var timer = setInterval(function(){
						v = lastTime(v)
						$(obj).val(v+'s can resend')
						if(v==0){
							clearInterval(timer)
							$(obj).val('Resend code')
						}
					},1000)
	                alert_msg('Successful send email code');
	            }else{
	                alert_msg(data.message);
	            }
	   }
	});
}


function reg_by_email(){
	var email_addr = $('#emails').val();
	var codes2 = $('#codes2').val();
	var pwd2 = $('#pwd2').val();
	var repwd2 = $('#repwd2').val();
	var invitioncode2 = $('#invitioncode2').val();
	if (email_addr == "") {
		alert_msg('Email cannot be empty');
		return;
	}
	if (codes2 == "") {
		alert_msg('Verification code cannot be empty');
		return;
	}
	if (pwd2 == "") {
		alert_msg('Password cannot be empty');
		return;
	}
	if (repwd2 == "") {
		alert_msg('Reinput passwords cannot be empty');
		return;
	}

	var post_data={
		email:email_addr,
		varcode:codes2,
		password:pwd2,
		repassword:repwd2,
		code:invitioncode2,
	}
	$.ajax({
	   type: 'POST',
	   url: '/api/register/email-register',
	   dataType: 'json',
	   data: post_data,
	   success: function(data){
	            if(data.code == 200){
	            alert_msg('Register was successful');
                setTimeout(function(){
				window.location.href = './login'
               },2000)   
	            }else{
	                alert_msg(data.message);
	            }
	   }
	});

}

</script>