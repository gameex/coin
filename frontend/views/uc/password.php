<link href="/resource/frontend/css/uc.css" rel="stylesheet">
<div id="main">
    <div class="main_box">
		<?php echo $this->render('left.php'); ?>
		<div class="raise right bg_w clearfix" id="changepwd">
    		<h2 data-i18n="u_a_21">修改登录密码</h2>
    		<div class="support_ybc pass_ybc">
    			<div id="tagContent" class="passContent">
        			<div class="tagContent selectTag" id="tagContent0">
            			<form id="signupForm">
                			<ul class="ybc_con" style="margin-left: 50px;">
                    			<li>
		                        	<input type="password" style="z-index:-9999;position:absolute;">
		                        	<label for="password">&nbsp;&nbsp;&nbsp;<font data-i18n="u_f_4">原登录密码</font>：</label>
		                        	<input name="oldpwd" id="oldpwd" type="password" placeholder="请输入原登录密码" onblur="getLoginVal(oldpwd)">
		                        	<i class="tishi" id="oldpwdmsg"></i>
		                        </li>
		                        <li>
		                        	<label for="newword"><font data-i18n="u_f_5">新登录密码</font>：</label>
		                        	<input name="pwd" id="pwd" type="password" placeholder="请输入新登录密码" onblur="getLoginVal(pwd)">
		                        	<i class="tishi" id="pwdmsg"></i>
		                        </li>
		                        <li>
		                        	<label for="repeat">&nbsp;&nbsp;&nbsp;<font data-i18n="u_f_6">重复密码</font>：</label>
		                        	<input name="ckpwd" id="repwd" type="password" placeholder="请重复新登录密码" onblur="getLoginVal(repwd)">
		                        	<i class="tishi" id="repwdmsg"></i>
		                        </li>
		                        <li>
		                            <p class="careful_tip" data-i18n="e_72">注意：注意修改密码后24小时不能提币</p>
		                        </li>
		                        <li>
		                        	<label class="buys">&nbsp;</label><input class="tijiao" data-i18n="u_f_7" value="提交" type="button" id="tijiao"></input>
		                        </li>
		                    </ul>
            			</form>
        			</div>
        		</div>
            </div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		init();
		/* 初始化 */
		function init() {
			bandEvent();
		}
		/* 事件绑定 */
        function bandEvent() {
        	$('.langhover').hover(function() {
				$('.multlang_cont').show();
			}, function() {
				$('.multlang_cont').hide();
			});

			$('.regMult').hover(function() {
				$('.multlang_cont').show();
			}, function() {
				$('.multlang_cont').hide();
			});

			$('.multlang_cont').hover(function() {
				$(this).show();
			}, function() {
				$(this).hide();
			});
        }
        $("#tijiao").click(function(event){
        	http.post('user/password-edit', {
				oldpassword:$("#oldpwd").val(),
				password:$("#pwd").val(),
				repassword:$("#repwd").val()
			}, function(res) {
				console.log(res.data);
				if(res.data =='修改成功!'){
					window.localStorage.clear();
					window.sessionStorage.clear();
					window.location.href="/login/out";
				}
			},function(err){
				http.info(err.message);
			});
        })
	});

	$(function() {
		bindEvent();
		function bindEvent() {

			$(".headerUser").find('.asset_header_hover').hover(function() {
				var $headUser = $(".headerUser"), $mw = $('.mywallet'), $mwv = $('.mywalletView');
				$headUser.find('.uses').find('.arrow').addClass('active');
				$mw.show();
				$mwv.show();
			}, function() {
				var $headUser = $(".headerUser"), $mw = $('.mywallet'), $mwv = $('.mywalletView');

				$mw.hide();
				$mwv.hide();
				$headUser.find('.uses').find('.arrow').removeClass('active');
			});

			$('.mywalletView').hover(function() {
				$(".mywallet").show();
				$('.mywalletView').show();
				$('.headerUser').find('.uses').find('.arrow').addClass('active');
			}, function() {
				$(".mywallet").hide();
				$('.mywalletView').hide();
				$('.headerUser').find('.uses').find('.arrow').removeClass('active');
			});

			/* 消息 */
			$(".headerUser").find('.msging').hover(function() {
				$(".headerUser").find('.myMsgView').show();
				$(".headerUser").find('.msgView').show();
			}, function() {
				$(".headerUser").find('.myMsgView').hide();
				$(".headerUser").find('.msgView').hide();
			});

			/*个人中心*/
			$(".headerUser").find('.asset_headerHover').hover(function() {
				$(".headerUser").find('.myuserView').show();
				$(".headerUser").find('.myUser').show();
			},function(){
				$(".headerUser").find('.myuserView').hide();
				$(".headerUser").find('.myUser').hide();
			})

			$('.myuserView').hover(function(){
				$(".headerUser").find('.myuserView').show();
				$(".headerUser").find('.myUser').show();
			},function(){
				$(".headerUser").find('.myuserView').hide();
				$(".headerUser").find('.myUser').hide();
			})
		}
	});
	function getLoginVal(type) {
		if(/^[a-zA-Z0-9]{6,20}$/.test(type.value)){
			type.nextElementSibling.innerHTML = '';
			return;
		}else{
			type.nextElementSibling.innerHTML = '密码长度在6-20位之间'
		}
	}
</script>