<style>
	.headertop{background: none;box-shadow:none;}
	.nav li a{color: #FFF;}
	.nav li a:hover{ border-bottom: 1px solid #FFF;}
	.headerUser .person .navLift li a{color: #FFF};
	.headerUser .person .navLift li a.active{border-bottom: 1px solid #FFF;}
	.headerUser .person .navLift li a:hover{border-bottom: 1px solid #FFF;}
	.loginArea a{color: #fff;}
	.loginArea a:nth-child(2){border: 1px solid #fff;}
    .nav li a.active{color: #FFF;border-bottom: 1px solid #fff;}
     .nav li a:hover{color: #FFF;border-bottom: 1px solid #fff;}
	#main{
		padding-top: 0;
		padding-bottom: 135px;
	}
	.download{
	    position: relative;
    	top: -65px;
	}
	.download .img_list{
		background: #272b41;
		height: 740px;
	    padding-top: 21px;
    	box-sizing: border-box;
	}
	.download .img_list2{
		width: 100%;
	    height: 740px;
	    margin: 0 auto;
	    position: relative;
	    background: #F5F5F5;
	}
	.download .img_list2 .up_view02{
	    width: 1206px;
	    height: 830px;
	    margin: 0 auto;
	    position: relative;
	    right: -33px;
	}
	.download .img_list2 img{
    	display: block;
    	max-width: 100%;
	}	
	.download .view03{
		background:#f5f5f5;
	}
	.download .img_list2 h2{
	    position: absolute;
    	top: 318px;
    	font-size: 40px;
		color: #2861F6;
		font-weight: bold;	
		line-height: 52px;
	}
	.download .img_list2 h3{
	    position: absolute;
    	top: 346px;
    	left: 800px;
    	font-size: 40px;
		color: #2861F6;
		font-weight: bold;
		line-height: 52px;
	}
	.download .img_list .up_view{
		width: 889px;
		margin:0 auto;
		height: 100%;
		position: relative;
		background: url('/resource/frontend/img/group_a@2x.png') 50% 0 no-repeat;
		background-size: 100%;
	    left: -245px;
	}
	.download .img_list .up_sdk{
	    width: 255px;
	    height: 422px;
	    display: inline-block;
	    position: absolute;
	    top: 221px;
	    right: -288px;
	    text-align: center;
	}
	.download .img_list .up_sdk h2{
		font-size: 40px;
		color: #FFFFFF;
		font-weight: bold;
		line-height: 52px;
		margin-bottom: 26px;
		text-align: center;
	}
	.download .img_list .up_sdk span{
	    font-size: 16px;
	    color: #FFFFFF;
	    margin-bottom: 26px;
	    display: inherit;
	}
	.download .img_list .up_sdk ul{
		text-align: center;
	}
	.download .img_list .up_sdk ul li{
		height: 152px;
		width: 126px;
		font-size: 16px;
		color: #FFFFFF;
	    margin: 0 auto;
	}
	.download .img_list .up_sdk ul li img{
		width: 126px;
		height: 126px;
	    margin-bottom: 26px;
	}
/*	.download .img_list .up_sdk ul li{
		font-size: 16px;
		color: #FFFFFF;
		margin-top: 5px;
	}*/
	.download .img_list .up_sdk a{
		display: inline-block;
		width: 229px;
		height: 58px;
		background: #FFFFFF;
		box-shadow: 0 52px 85px 0 rgba(0,0,0,0.44);
		border-radius: 100px;
		line-height: 58px;
		text-align: center;
		font-size: 20px;
		color: #2861F6;
	    text-decoration: none;
	    margin-top: 30px;
	    margin-left: 65px;
	}

	.top_body .download .img_list .up_sdk {
	    width: 460px;
	}
	.top_body .download .img_list .up_sdk ul li {
	    width: 300px;
	}
	
	.download .img_list3{
		width: 100%;
	    height: 740px;
	    margin: 0 auto;
	    background: #fff;
	}
	.download .img_list3 .up_view03 {
	    width: 1140px;
	    height: 740px;
	    margin: 0 auto;
	    position: relative;
	    padding-right: 100px;
    	box-sizing: border-box;
	}
	.download .img_list3 .up_view03 img{
		display: block;
    	max-width: 100%;
	}
	.download .img_list3 .up_view03 h3{
	    position: absolute;
	    top: 317px;
	    right: 180px;
	    font-size: 40px;
	    color: #2861F6;
	    font-weight: bold;
	    line-height: 52px;
	}
</style>
<div id="main">
	<ul class="download">
		<li class="img_list">
			<div class="up_view">
				<div class="up_sdk">
					<h2 data-i18n="u_g_36"><?= Yii::$app->config->info('WEB_APP_NAME') ?> APP Preemptive Experience</h2>
					<ul class="clearfix">
						<li>
							<img src="<?= Yii::$app->config->info('APP_QRCODE_DOWNLOAD') ?>">
							<p data-i18n="u_g_76">Please download it in sweep code</p>
						</li>
					</ul>
				</div>
			</div>
		</li>

		<li class="img_list2">
			<div class="up_view02">
				<img src="/resource/frontend/img/Group_b@2x.png">
				<h2><font data-i18n="u_g_40">Whenever and wherever possible</font><br><font data-i18n="u_g_41">Observing market changes</font></h2>
			</div>
		</li>
		<li class="img_list3">
			<div class="up_view03">
				<img src="/resource/frontend/img/Group_c@2x.png">
				<h3><font data-i18n="u_g_42">Every minute and second</font><br><font data-i18n="u_g_43">Trading to make money</font></h3>
			</div>
		</li>
	</ul>
</div>