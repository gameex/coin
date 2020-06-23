<!DOCTYPE html>
<html lang="en" style="font-size: 100px;">
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
	<title>注册</title>
    <script type="text/javascript" src="/resource/frontend/js/jquery.min.js"></script>
	<style type="text/css">
		*{
			margin: 0;
			padding: 0;
		}
		html,body{
			height: 100%;
		}
		body{
			background-color: #2e3349;
		}
		.auto_image{
			width: auto;
			height: auto;
			max-width: 100%;
			max-height: 100%;
		}
		.content{
			margin: 0.44rem 0.4rem 0;
		}
		.logo{
			width: 150px;
			margin: 0 auto 0.22rem;
		}
		.logo img{
			border-radius: 0.3rem;
		}
		.input-area .shurukuang{
			position: relative;
			width: 100%;
			height: 0.50rem;
			border-bottom: 1px solid #7a87b0;
			display: flex;
			align-items: center;
		}
		.shurukuang input{
			margin-left: 0.05rem;
			display: inline-block;
			height: 100%;
			width: 85%;
			border: none;
			outline: none;
			color: #cfd3e9;
			background-color: transparent;
		}

		button.varcodebutton{
			position: absolute;
		    right: 0;
		    top: 0;
		    z-index: 1;
		    height: 100%;
		    padding: 0 4px;
		    color: #21A9ED;
		    background: none;
		    border: none;
		    outline: none;
		}
		.reg{
			margin-top: 0.5rem;
			width: 100%;
			height: 0.4rem;
			background-color: #21A9ED;
			border: none;
			color: white;
		}
	</style>
</head>
<body style="font-size: 15px;">

	<div class="content">
		<div class="logo">
			<img class="auto_image" src="<?= Yii::$app->config->info('WEB_SITE_LOGO') ?>">
		</div>

		<div class="input-area">
			<div class="shurukuang">
				<img class="input-icon" src="/share/images/login-yonghu.png">
				<input id="mobile_phone" type="text" placeholder="请输入手机号">
			</div>
			<div class="shurukuang">
				<img class="input-icon" src="/share/images/login-mima.png">
				<input id="password" type="password" placeholder="请输入密码">
			</div>
			<div class="shurukuang">
				<img class="input-icon" src="/share/images/login-mima.png">
				<input id="repassword" type="password" placeholder="请再次输入密码">
			</div>
			<div class="shurukuang">
				<img class="input-icon" src="/share/images/login-var.png">
				<input id="varcode" type="text" placeholder="请输入验证码">
				<button id="getVar" class="varcodebutton">获取验证码</button>
			</div>
			<div class="shurukuang">
				<img class="input-icon" src="/share/images/login-yaoqing.png">
				<input id="code" type="text" placeholder="请输入邀请码（选填）">
			</div>
		</div>
		<button class="reg" id="reg">注册</button>	
	</div>


<script type="text/javascript">
	var api = "/api/";
	var down = "<?= Yii::$app->config->info('APP_DOWNLOAD_URL') ?>";

	$(document).ready(function(){
	
		let invite_code = GetQueryString("code")
		if(invite_code !=null && invite_code.toString().length>1){
			$('#code').val(invite_code);
			$('#code').attr("readonly",true);
		}

		$('#getVar').click(function(){

			let phone = $("#mobile_phone").val()
			if (phone.length == 0) {
				alert("手机号未填写")
				return;
			}

			isDisableVarCode(true)

			$.post(
	         	api + "register/mobile-varcode",
	         	{
	         		"mobile_phone":phone,
	         		"type":"1"
	         	},
	         	function(data, textStatus){
	         		if (data.code != 200) {
	         			isDisableVarCode(false)
	         			alert(data.message)
	         		}else{
	         			varCodeCutDown()
	         		}
	         	},
	         	"json")
		});

		$('#reg').click(function(){
			let phone = $("#mobile_phone").val()
			let password = $("#password").val()
			let repassword = $("#repassword").val()
			let varcode = $("#varcode").val()

			if (phone.length == 0 || password.length == 0 || repassword.length == 0 || varcode.length == 0) {
				alert("信息填写不全！")
				return;
			}

			$.post(
	         	api + "register/register",
	         	{
	         		"mobile_phone":phone,
	         		"password":password,
	         		"repassword":repassword,
	         		"varcode": varcode,
	         		"code":$("#code").val()
	         	},
	         	function(data, textStatus){
	         		if (data.code == 200) {
	         			alert("注册成功!");
	         			let os = GetQueryString("os")
	         			if (os === "ios") {
	         				$(location).attr('href', down + '12');
	         			}else{
	         				$(location).attr('href', down + '11');
	         			}
	         		}else{
	         			alert(data.message)
	         		}
	         	},
	         	"json")
		});

	});

	//发送验证码 倒计时
	function varCodeCutDown(){
		$('#getVar').html("60秒后重试");
		var second = 60;
		var timer = null;
		timer = setInterval(function(){
			second -= 1;
			if(second >0 ){
				$('#getVar').html(second + "秒后重试");
			}else{
				clearInterval(timer);
				$('#getVar').html("获取验证码");
				isDisableVarCode(false)
			}
		},1000);
	}

	function isDisableVarCode(isDisable){
		if (isDisable) {
			$("#getVar").attr("disabled", "true");
		}else{
			$("#getVar").removeAttr("disabled");//启用按钮
		}
	}

	function GetQueryString(name){
		var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
	    var r = window.location.search.substr(1).match(reg);//search,查询？后面的参数，并匹配正则
	    if(r!=null)return  unescape(r[2]); return null;
	}

</script>
</body>
</html>