<style type="text/css">
.register a.registerbtn{
	color: #fff;
	text-decoration: none;
}
.main_bg{
	width: 100%;
	min-height: 500px;
}
.main{
	width: 990px;
	min-height: 500px;
	background: #F5F9FE;
	margin: 16px auto 30px;
	padding: 40px 75px 55px;
}
.warning h2{
	line-height:32px;
	font-size: 24px;
	width:100%;
	text-align:center;
	color:#121212;
	padding-bottom: 48px;
}
.warntxt{
	font-size: 14px;
	color: #555555;
	line-height: 26px;
}
.choice{
	margin-top: 60px;
/*	position: relative;*/
}
.choice input[type="checkbox"]{
	width: 24px;
	height: 24px;
	border:1px solid #979797;
	display: inline-block;
	position: relative;
	opacity: 0;
	z-index: 99;
	cursor: pointer;
}
.check_bg{
	width: 24px;
	height: 24px;
	border:1px solid #979797;
	display: inline-block;
	position: absolute;
	left: -5px;
	top: -2px;
	background: #FFF;
	cursor: pointer;
}
.check_bg.show_bg{
	border: none;
	background: url("/resource/frontend/img/selecteds.png") no-repeat;
}
.register{
	text-align: center;
	margin-top: 30px;
}
.registerbtn{
	width: 104px;
	height: 42px;
	line-height: 42px;
	border-radius: 6px;
	background: #ccc;
	color: #fff;
	font-size: 14px;
	border: none;
	cursor: pointer;
	display: inline-block;
}
.cancelbtn{
	width: 76px;
    height: 42px;
    line-height: 42px;
    color: #368AE5;
    font-size: 14px;
	margin-left:65px;
	background:  #D3E6FB;
	border-radius: 6px;
	text-align: center;
	cursor: pointer;
	display: inline-block;
}
.cancelbtn:hover{
	color: #fff!important;
	text-decoration: none;
	background:  #2A79CF;;
}
.registerbtn.hoverbg{
	background: #368AE5;
}
.registerbtn.hoverbg:hover{
	background: #2A79CF;
}
.choice a,.warntxt a{color:#368AE5}
.regCheck{position: relative;}
.regCheck:nth-child(2){
	margin-top: 5px;
}
.regCheck:nth-child(2) .checkcodes{
	top: 5px;
    left: -5px;
}
.top_body .registerbtn {
    width: 160px;

}
</style>

<div class="main_bg" id="reg-prev">
	<div class="main">
		<div class="warning">
			<h2 data-i18n="u_h_41"><?php echo $content['title']; ?></h2>
			<div class="warntxt">
			<?php echo $content['content']; ?>
			</div>
		</div>
		<div class="choice">

				<div class="regCheck">

					<input type="checkbox" class="checkcodes" name="risk" >
					<i class="check_bg"></i>
					<label for="checkcodes"><font data-i18n="u_h_54">我已经认真阅读以上风险提示，并已同意 <?= Yii::$app->config->info('WEB_APP_NAME') ?> 的 </font>
						<a href="/help/14" data-i18n="u_h_55">使用条款</a>，
						<a href="/help/13" data-i18n="u_h_56">隐私政策</a>，
						<a href="/help/15" data-i18n="u_h_57">反洗钱条例</a>
						<font data-i18n="u_h_58">，同意在自担风险，自担损失的情况下参与交易</font>
					</label>
				</div>
				<div class="regCheck">
					<input type="checkbox" class="checkcodes" name="risk" >
					<i class="check_bg"></i>
					<label for="checkcodes" data-i18n="u_h_59">我不是美国人也不是新加坡人</label>
				</div>
				<div class="register">
					<!--<input class="registerbtn" value="继续注册" type="submit" disabled="disabled">-->
					<a href='javascript:;' class="registerbtn">继续注册</a>
					<a href="./index" class="cancelbtn" data-i18n="b_42">取消</a>
				</div>
		</div>
	</div>

	<script>
		$('.regCheck').click(function(){
			var Index = $(this).index();
			var val = $('.checkcodes').eq(Index).attr('checked')
			if(val){
				$('.checkcodes').eq(Index).attr({checked:false});
				$('.check_bg').eq(Index).removeClass('show_bg')
			}else{
				$('.checkcodes').eq(Index).attr({checked:true});
				$('.check_bg').eq(Index).addClass('show_bg')
			}//./regist.html
			//console.log($('.checkcodes').eq(Index))
			$('.checkcodes').map(function(index,res){
			   if(!res.checked){
			   	$('.registerbtn').removeClass('hoverbg').attr({href:'javascript:;'})
			   }else{
			   	$('.registerbtn').addClass('hoverbg').attr({href:'./reg'})
			   }
			})
		})
	</script>
</div>