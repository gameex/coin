<style>
html,body{background: #F5F9FE;}
.content_bg{
	width: 100%;
	min-height: 500px;
	background: #F5F9FE;
}
.content{
	width: 1140px;
	margin:0 auto;
	text-align: center;
}
.formbody>h5{
	padding: 67px 0 50px;
	font-size: 18px;
	color: #333;
}
.forgermsg1 li label{
	font-size: 14px;
	color: #333;
}
.forgermsg1 li input{
	width: 290px;
	height: 42px;
	line-height: 42px;
	padding-left: 10px;
	background: #fff;
	border-radius: 6px;

	border: 1px solid transparent;
}
.forgermsg1 li input:focus{
	border: 1px solid #B3D4F9;
}
.forgermsg1 li input.code{
	width: 150px;
	margin-right: 10px;
}
.forgermsg1 li input.sendcode{
	width: 130px;
	padding-left: 0;
	margin-left: 0px;
	background: #B3D4F9;
	font-size: 14px;
	color: #368AE5;
	cursor: pointer;
}
.forgermsg1 li{
	position: relative;
	margin:0 10px 20px 20px;
}
.tishis{
	height: 42px;
	line-height: 42px;
	font-size: 12px;
	color: #E62512;
	font-style: initial;
	display: inline-block;
	position: absolute;
	left: 765px;
}
.forgermsg1 li input.tijiao{
	background: #368AE5;
	font-size: 14px;
	color: #fff;
	cursor: pointer;
	margin-left: 88px;
    width: 307px!important;
}

#steps1 label {
	width: 113px;
	display: inline-block;
	text-align: right;
}
.top_body .formbody>h5 {
    margin-left: 35px;
}
.top_body #steps1 label {
	width: 150px;
	display: inline-block;
	text-align: right;
}
#steps2 label {
	width: 113px;
	display: inline-block;
	text-align: right;
}
.top_body #steps2 label {
	width: 150px;
	display: inline-block;
	text-align: right;
}
.top_body .forgermsg1 li input.tijiao {
    margin-left: 125px;
}
.top_body #steps2 li input.tijiao {
    margin-left: 0px;
}
.top_body .tishis {
    left: 790px;
}
</style>
<div class="content_bg">
	<div class="content">
		<div class="formbody">
			<h5 data-i18n="a_password">忘记密码</h5>
				<form id="forgetMsg">
					<ul class="forgermsg1" id="steps1">
                        <li><label for="phone" data-i18n="u_h_16">手机号码或邮箱：</label>
                        	<input type="text" class="" name="phone" id="phone" placeholder="请输入手机号码或邮箱">
                        </li>
                        <li>
                        	<label for="code" data-i18n="u_h_17">验证码：</label>
                        	<input type="text" class="code" name="code" id="code" placeholder="请输入验证码" maxlength="6">
                        	<input type="button" value="发送验证码" class="sendcode" onclick="sendcode(this)" id="sendCode" data-key="on">
                        	<i class="tishis" id="code_msg"></i>
                        </li>
                    	<li><label for="pwd" data-i18n="u_h_21">输入新密码：</label>
                        	<input type="password" class="loginValue" value="" name="pwd" id="pwd" placeholder="请输入新密码">
                        	<i class="tishis" id="news_msg"></i>
                        </li>
                        <li>
                        	<label for="code" data-i18n="u_h_23">重复新密码：</label>
                        	<input type="password" name="ckpwd" id="repwd" placeholder="请重复新密码">
                        	<i class="tishis" id="repeat_msg"></i>
                        </li>
                        <li style="margin-bottom:15px;">
                        	<label> </label><input type="button" value="提交" class="tijiao" id="tijiao" style="margin-left: 0;border:0;width: 302px!important;">
                        </li>
                    </ul>
				</form>
		</div>
	</div>
</div>

<script>
var findIndex = {};

 function lastTime(val){
		 	 val--;
		 	 val = val>=10?val:'0'+val;
		 	 val = val<=0?0:val;
		 	 return val
		 }
// function sendcode(e){
// 	//console.log(e.val())
// 	var phone = tool.detectionData.checkData({
// 				name: 'phone',
// 				val: findIndex.phone,
// 				string: '手机号'
// 		})
// 	if(phone){
// 		var $this = e;
// 		http.post('register/mobile-varcode',{mobile_phone:findIndex.phone,type:2},function(res){
// 		 		http.info(res.message)
// 		 		var v = 60
// 		 		var timer = setInterval(function(){
// 		 			v = lastTime(v)
// 		 			$this.val(v+'秒后重发')
// 		 			if(v==0){
// 		 				clearInterval(timer)
// 		 				$($this).val('重发验证码')
// 		 			}
// 		 		},1000)

// 		 	},function(err){
// 					 		http.info(err.message)
// 					 	})
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


function lastTime(val){
	 val--;
	 val = val>=10?val:'0'+val;
	 val = val<=0?0:val;
	 return val
}

function sendcode(obj){
	user_name = $('#phone').val();
	if (user_name == "") {
		alert_msg('手机号或邮箱不能为空');
		return;
	}

	if(user_name.indexOf("@") != -1){
		var post_data={
			email:user_name,
			type:2,
			os:'web',
		}
		var post_url = '/api/register/email-varcode';
	}else{
		var post_data={
			mobile_phone:user_name,
			type:2,
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
						$(obj).val(v+'秒后重发')
						if(v==0){
							clearInterval(timer)
							$(obj).val('重发验证码')
						}
					},1000)
	                alert_msg('发送成功');
	            }else{
	                alert_msg(data.message);
	            }
	   }
	});

}











$('#sumTijiao').click(function(){
	var phone = tool.detectionData.checkData({
				name: 'phone',
				val: findIndex.phone,
				string: '手机号'
		})
	var code =phone && tool.detectionData.checkData({
				name: 'codes',
				val: findIndex.code,
				string: '验证码'
		})
	//console.log(code)
	if(code){
		//console.log(111);
		var $this = this;
		http.post('register/forget-password-phone',{
			mobile_phone:findIndex.phone,
			varcode:findIndex.code
		},function(res){
			$($this).css('display','none');
			$('#steps2').css('display','block');
			$('#steps1').css('display','none');
		},function(err){
			http.info(err.message);
		})
	}
})
$('#tijiao').click(function(){
	var mobile_phone = $('#phone').val();
	var varcode = $('#code').val();
	var password = $('#pwd').val();
	var repassword = $('#repwd').val();
	if (mobile_phone == "") {
		alert_msg('手机号或邮箱不能为空');
		return;
	}
	if (varcode == "") {
		alert_msg('验证码不能为空');
		return;
	}
	if (password == "") {
		alert_msg('密码不能为空');
		return;
	}
	if (repassword == "") {
		alert_msg('重复密码不能为空');
		return;
	}

	if(user_name.indexOf("@") != -1){
		var post_data={
			email:mobile_phone,
			varcode:varcode,
			password:password,
			repassword:repassword,
			os:'web',
		}
		var post_url = '/api/register/email-forget-password';
	}else{
		var post_data={
			mobile_phone:mobile_phone,
			varcode:varcode,
			password:password,
			repassword:repassword,
			os:'web',
		}
		var post_url = '/api/register/forget-password';
	}

	$.ajax({
	   type: 'POST',
	   url: post_url,
	   dataType: 'json',
	   data: post_data,
	   success: function(data){
	            if(data.code == 200){
	                alert_msg('重置成功');
		 			window.location.href='/login'
	            }else{
	                alert_msg(data.message);
	            }
	   }
	});

})
</script>