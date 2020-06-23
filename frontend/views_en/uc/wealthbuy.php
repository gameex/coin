<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
?>
<style type="text/css">
.passContent .tagContent .ybc_con input {
    border: 1px solid #e1e1df;
    background: #e1e1df;
}
#num{
    border: 1px solid #e1e1df;
    background: #fff;
}
</style>
<link href="/resource/frontend/css/uc.css" rel="stylesheet">  
<div id="main">
    <div class="main_box">
		<?php echo $this->render('left.php'); ?>
		<div class="raise right bg_w clearfix" id="changepwd">
    		<h2 data-i18n="u_a_21">Purchase package</h2>
    		<div class="support_ybc pass_ybc">
    			<div id="tagContent" class="passContent">
        			<div class="tagContent selectTag" id="tagContent0">
            			<form id="signupForm">
                			<ul class="ybc_con" style="margin-left: 50px;">
                    			<li>
		                        	<label for="email">&nbsp;&nbsp;&nbsp;<font data-i18n="u_f_4">Currency：</font></label>
		                        	<input name="email" id="email" type="text" readonly="readonly" value="<?= $wealtdetail['coin_symbol'] ?>">
		                        </li>
                    			<li>
		                        	<label for="email">&nbsp;&nbsp;&nbsp;<font data-i18n="u_f_4">Name：</font></label>
		                        	<input name="email" id="email" type="text" readonly="readonly" value="<?= $wealtdetail['name'] ?>">
		                        </li>
                    			<li>
                    			<li>
		                        	<label for="email">&nbsp;&nbsp;&nbsp;<font data-i18n="u_f_4">Validity period：</font></label>
		                        	<input name="email" id="email" type="text" readonly="readonly" value="<?= $wealtdetail['period'] ?>">
		                        </li> 
                    			<li>
		                        	<label for="email">&nbsp;&nbsp;&nbsp;<font data-i18n="u_f_4">subscription ratio：</font></label>
		                        	<input name="email" id="email" type="text" readonly="readonly" value="<?= $wealtdetail['day_profit'] ?>">
		                        </li>
                    			<li>
		                        	<label for="email">&nbsp;&nbsp;&nbsp;<font data-i18n="u_f_4">Purchase amount：</font></label>
		                        	<input name="id" id="id" type="hidden" readonly="readonly" value="<?= $wealtdetail['id'] ?>">
		                        	<input name="num" id="num" type="text" value="" placeholder="Minimum purchase<?= $wealtdetail['min_num'] ?><?= $wealtdetail['coin_symbol'] ?>">
		                        </li>
		                        <li>
		                        	<label class="buys">&nbsp;</label><input class="tijiao" data-i18n="u_f_7" value="submit" type="button" id="tijiao"></input>
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
        	http.post('user/weathbuypackage', {
				id:$("#id").val(),
				num:$("#num").val(),
			}, function(res) {
				console.log(res);
				if(res.code ==200){
					var type = "<?= $wealtdetail['type'] ?>";
					if (type == 4) {
						window.location.href="/uc/wealthlock";
					}else{
						window.location.href="/uc/wealth";
					}
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
		user_name = $('#email').val();
	    ejcode = $('#ejcode').val();
	if (ejcode == "") {
		alert_msg('Check code cannot be empty');
		return;
	}             
		if (user_name == "") {
			alert_msg('Please input the email address');
			$('#email').focus();
			return;
		}

		if(user_name.indexOf("@") != -1){
			var post_data={
				email:user_name,
                ejcode:ejcode,                            
				type:3,
				os:'web',
			}
			var post_url = '/api/register/email-varcode';
		}else{
			var post_data={
				mobile_phone:user_name,
                mjcode:mjcode,                            
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
							$(obj).val(v+'Resend after second')
							if(v==0){
								clearInterval(timer)
								$(obj).val('Resend verification code')
							}
						},1000)
		                alert_msg('Sent successfully');
		            }else{
		                alert_msg(data.message);
		            }
		   }
		});
	}

</script>