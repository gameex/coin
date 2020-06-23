<link href="/resource/frontend/css/uc.css" rel="stylesheet">
<div id="main">
    <div class="main_box">
	<?php echo $this->render('left.php'); ?>

		<div class="raise right bg_w clearfix double">
		    <h2 data-i18n="u_f_8">Verified</h2>
		    <div class="support_ybc pass_ybc" style="display: block;">
		        <div id="tagContent" class="passContent">
		            <div class="tagContent selectTag" id="tagContent0">
		                <form id="signupForm"  enctype="multipart/form-data">
		                    <ul class="ybc_con" style="margin-left: 50px;">
		                        <li>
		                        	<input type="password" style="z-index:-9999;position:absolute;">
		                        	<label for="password">&nbsp;&nbsp;&nbsp;<font data-i18n="u_f_4">Name</font>：</label>
		                        	<input name="oldpwd" id="oldpwd" type="text" placeholder="please enter your real name">
		                        	<i class="tishi" id="oldpwdmsg"></i>
		                        </li>
		                        <li>
		                        	<label for="newword"><font data-i18n="u_f_5">identity number</font>：</label>
		                        	<input name="pwd" id="pwd" type="text" placeholder="please enter the ID number">
		                        	<i class="tishi" id="pwdmsg"></i>
		                        </li>
		                    </ul>
		                    <div class="ws_idCardZ">
		                    	<img src="/resource/frontend/img/img_shengfenzheng_zheng.png" alt="" style="width:100%;height:100%;">
		                    	<input type="file" id="uploadZ" type="file" style="position:absolute;top: 0;left: 0;width: 100%;height: 100%;opacity: 0;">
		                    </div>
		                    <div class="ws_idCardF">
		                    	<img src="/resource/frontend/img/img_shenfenzheng_fan.png" alt="" style="width:100%;height:100%;">
		                    	<input type="file" id="uploadF" style="position:absolute;top: 0;left: 0;width: 100%;height: 100%;opacity: 0;">
		                    </div>
		                    <input class="tijiao" value="save" type="button" id="save" style="width:200px;height:40px;position: absolute;bottom: 30px;left:30%"></input>
		                </form>
		            </div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
</div>

<script type="text/javascript">

	var idCardz = '';
	var idCardf = '';
	$(document).ready(function() {
		init();
		/* 初始化 */
		function init() {
			bandEvent();
		
		}
		/* 事件绑定 */
        function bandEvent() {
        	http.post('user/get-info', {
			}, function(res) {
				console.log(res.data.status_msg);
				if(res.data.status == 3){
					if(res.data.status_msg == "The review failed, please upload the real information"){
						http.info("Audit failed, please recertify information");
						$('#oldpwd').val(res.data.real_name);
						$('#pwd').val(res.data.id_number);
						$('.ws_idCardZ img').attr('src',res.data.id_card_img2);
						$('.ws_idCardF img').attr('src',res.data.id_card_img);
						idCardz = res.data.id_card_img2;
						idCardf = res.data.id_card_img;
					}
					// $('#tagContent0').html('<div class="nopass"style="background:url(/resource/frontend/img/ico_shenhe_b.png) no-repeat;">'+res.data.status_msg+'</div>');
				}else if(res.data.status == 2){
					$('#tagContent0').html('<div class="nopass"style="background:url(/resource/frontend/img/ico_renzhengchenggong_b.png) no-repeat;">'+res.data.status_msg+'</div>');
				}else if(res.data.status == 1){
					$('#tagContent0').html('<div class="nopass"style="background:url(/resource/frontend/img/ico_renzhengchenggong_b.png) no-repeat;">'+res.data.status_msg+'</div>');
				}else if(res.data.status == 0){

				}
			});
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
	});

	$(function() {
		bindEvent();
		function bindEvent() {
			var input = document.getElementById('uploadZ');
	        input.onchange =function(e){
	            var dom =document.getElementById('uploadZ').files[0];
	           
	            var reader = new FileReader();
	            reader.onload = (function (file) {
	                return function (e) {
	                
	                	$('.ws_idCardZ img').attr('src',this.result);
	                };
	        })(e.target.files[0]);
	        	reader.readAsDataURL(e.target.files[0]);
				var formFile = new FormData(document.getElementById("signupForm"));
				
               	formFile.append("access_token",localStorage.getItem('access_token'));
               	formFile.append("image",dom); //加入文件对象
				var data = formFile;
               $.ajax({
                   url: "/api/user/upload-image",
                   data: data,
                   type: "Post",
                   dataType: "json",
                   cache: false,//上传文件无需缓存
                   processData: false,//用于对data参数进行序列化处理 这里必须false
                   contentType: false, //必须
                   success: function (result) {
                   		if(result.code == 200){
                   			idCardz = result.data.urlPath;
                   			// http.info(result.message);
                   			return idCardz
                   		}else{
                   			alert(result.message)
                   		}
                   },
               })
	        }

	        var input = document.getElementById('uploadF');
	        input.onchange =function(e){
	            var dom =document.getElementById('uploadF')[0];
	            var reader = new FileReader();
	            reader.onload = (function (file) {
	                return function (e) {
	                	$('.ws_idCardF img').attr('src',this.result);
	                };
	        })(e.target.files[0]);
	            reader.readAsDataURL(e.target.files[0]);
	           var formFile = new FormData(document.getElementById("signupForm"));
               	formFile.append("access_token",localStorage.getItem('access_token'));
               	formFile.append("image",e.target.files[0]); //加入文件对象
				var data = formFile;
               $.ajax({
                   url: "/api/user/upload-image",
                   data: data,
                   type: "Post",
                   dataType: "json",
                   cache: false,//上传文件无需缓存
                   processData: false,//用于对data参数进行序列化处理 这里必须false
                   contentType: false, //必须
                   success: function (result) {
                   		if(result.code == 200){
                   			idCardf = result.data.urlPath;
                   			// http.info(result.message);
                   			return idCardf
                   		}else{
                   			// alert(result.message)
                   		}
                   },
                   error:function(err){
                   	http.info(err.message)
                   }
               })
	        }
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
			$('#save').click(function(event){
				if(1){      //idCardf !='' && idCardz !=''
					http.post('user/set-real', {
						real_name:$("#oldpwd").val(),
						id_number:$("#pwd").val(),
						id_card_img:idCardz,
						id_card_img2:idCardf
					}, function(res) {
						if(res.code ==200){
							http.info('Pending review')
						}
					},function(err){
						http.info(err.message)
					},function(r){
						http.info(r.message);
					});
				}else{
					http.info("Please complete the information");
				}
			})
		}
	});
</script>