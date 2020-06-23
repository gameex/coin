<script>
/*滚动添加css*/
var isScroll = {
    /*初始化*/
    init: function (_el) {
        this.start(_el);
        $(window).on('scroll', function () {
            isScroll.start(_el)
        });
    },
    /*开始*/
    start: function (_el) {
        var self = this;
        $(_el).each(function () {
            var _self = $(this);
            /*滚动高度*/
            var isScrollTop = $(window).scrollTop();
            /*滚动视度*/
            var isWindowHeiget = $(window).height() * 0.8;
            /**/
            var _class = $(this).data('animation');
            if (isScrollTop + isWindowHeiget > $(this).offset().top) {
                _self.addClass(_class);
            }
        });
    }
}	
</script>
    <!--top end-->    
<style type="text/css">
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
    min-width:1400px;
    width:100%;
    background-color: #fff;
    box-shadow: -4px 2px 0 0 #d2d2d2;    
     }
  	.nav li a {
    line-height: 60px;
    height: 50px;
    padding: 0 15px;
    font-size: 14px;
    margin: 0 5px;
    color: #fff;
  }	
   	.nav li a:hover {
    line-height: 60px;
    height: 50px;
    padding: 0 15px;
    font-size: 14px;
    margin: 0 5px;
    color: #FFF;
   border-bottom: 1px solid #fff;
  }	 
  .nav li a.active{
    line-height: 60px;
    height: 50px;
    padding: 0 15px;
    font-size: 14px;
    margin: 0 5px;
    color: #fff;
    border-bottom: 2px solid #fff;
  }
  
  .loginArea a, .person a{    
    color: #fff;
  }
  .headerUser .person .navLift li a{
    color: #fff;    
   } 
  .headerUser .person .navLift li a:hover {
    color: #545de2;
    border-bottom: 2px solid #1b4bfa; 
   }   
/*	.nav li{margin-left: 60px;}*/
/*.get_early{margin: 4px 15px 0 28px;}
.nav li{margin-left: 60px;}*/
</style><style type="text/css">
	.OpenBeta {
	    height: 30px;
	    line-height: 30px;
	    text-align: center;
	    font-size: 14px;
	    color: #F8E81C;
	    margin-top:12px;
	}
	.OpenBeta.active {
		background: #FDE1DF;
		position: relative;
		color: #DA2E22;
		font-weight: bold;
		z-index: 99;
	}
	.nav li:nth-child(1){margin-left: 32px;}

	.sub-nav::-webkit-scrollbar {
	    width: 6px;
	    height: 16px;
	}

	.sub-nav::-webkit-scrollbar-thumb {
	    width: 10px;
	    height: 20px;
	    border-radius: 10px;
	    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
	    background-color: #afd0f5;
	    /*background-color: rgba(151,168,190,.3);*/
	}

  .headertop .loginArea{
  margin-right:32px;
  }
  .sub-nav {
	    width: 486px;
	    max-width: 486px;
	    max-height: 193px;
	    min-height: 140px;
	    display: inline-block;
	    padding-bottom: 10px;
	    overflow-y: visible;
	    overflow-x: hidden;
	}
   .sub-nav li {
		height: 21px;
	}
    .sub-nav li:hover {
		border-radius: 0;
	}
    .sub-nav .ht_icon {
		display: inline-block;
	    width: 20px;
	    height: 20px;
	    background-repeat: no-repeat;
	    background-size: 100% 100%;
	    vertical-align: top;
	}
     .sub-nav a {
		height: 21px;
		font-size: 12px;
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
		margin-left: 4px;
		display: inline-block;
	}

	.top_body{
		width: 420px;
		/*width: 325px;*/
	}
	.top_body .headerUser .person .information .user_mine {
		width: 100px;
	}
	.loginArea {
		width: 286px;
		/*width: 210px;*/
	}
	.top_body .loginArea {
		width: 370px;
	    /*width: 235px;*/
	}
	.loginArea .regMult img{
		width: 24px;
		height: 16px;
		margin-bottom: -3px;
	}
	.loginArea .regMult:hover{
		background: none;
	}
	.loginArea {
		overflow: initial;
	}
	.multlang_regcont {
		top: 25px;
		text-align: center;
	}
	.multlang_regcont ul {
		width: 100%;
	    background: #fff;
	    border-radius: 3px;
	    box-sizing: border-box;
	    position: relative;
	    display: inline-block;
	}	
	.multlang_regcont ul li {
	    width: 100px;
	    position: relative;
	    cursor: pointer;
	    text-align: center;
	    margin-left: 0px;
	    line-height: 40px;
	}	
	.multlang_cont ul li:hover {
		color: blue;
	}
	.h_regist {
		margin-right: 10px;
	}
	/*.top_body .nav li {
	    margin-left: 15px; 
	}
	.top_body .headerUser .person .navLift li {
	    margin-left: 10px;
	}*/
	/*.headerUser .person .navLift li {
	    margin-left: 10px;
	}*/
</style>
<style>
	body,html{
		background: #1C1F32;
	}
	#main{
		background: #1C1F32;
		padding-top:0;
		padding-bottom: 135px;
	}

    .animated{
            opacity: 0;
        }
</style>
<div id="main" class="clearfix">
	<div class="aboutV">
		<div class="aboutView">
			<div class="Introduction sdiv divtest on" data-animation="on">
				<h2 data-i18n="about_1"><?= Yii::$app->config->info('WEB_APP_NAME') ?>交易所简介</h2>
				<p><font data-i18n="about_2"><?= Yii::$app->config->info('WEB_APP_NAME') ?>交易所是全球知名的数字资产交易所之一，主要提供比特币、以太坊、莱特币等一系列区块链资产的币币交易服务，由区块链及数字资产爱好者创办，核心团队来自迅雷、腾讯、百度，惠普。由注册于塞舌尔的<?= Yii::$app->config->info('WEB_APP_NAME') ?> LIMITED公司运营，目前运营中心位于新加坡。</font><br><font data-i18n="about_3"><?= Yii::$app->config->info('WEB_APP_NAME') ?>的愿景是能够为区块链资产投资者提供便捷、放心、安全的投资渠道。</font></p>
			</div>
			<div class="aboutCenter sdiv divtest" data-animation="on">
				<ul>
					<li class="aboutOn aboutOn02">
						<span><font data-i18n="about_4">业内领先</font><br><font data-i18n="about_5">技术架构</font></span>
						<div class="aboutList">
							<div class="aboutNei">
								
							</div>
						</div>
					</li>
					<li class="aboutOn aboutOn03">
						<span><font data-i18n="about_6">100%</font><br><font data-i18n="about_7">保证金</font></span>
						<div class="aboutList">
							<div class="aboutNei">
								
							</div>
						</div>
					</li>
					<li class="aboutOn aboutOn04">
						<span><font data-i18n="about_8">美国SEC</font><br><font data-i18n="about_9">级审计系统</font></span>
						<div class="aboutList">
							<div class="aboutNei">
								
							</div>
						</div>
					</li>
				</ul>
			</div>
			<div class="aboutCenter02 sdiv divtest" data-animation="on">
				<h2 data-i18n="about_10">交易即挖矿</h2>
				<div class="msg aboutOn aboutOn01">
					<p data-i18n="about_11">在<?= Yii::$app->config->info('WEB_APP_NAME') ?>平台，交易任何币种，都可获得平台积分EFT，持有EFT可以获得平台分红奖励。具体的挖矿规则和持有EFT奖励规则请参考网站公告</p>
				</div>
			</div>
			<div class="aboutCenter03 sdiv divtest" data-animation="on">
				<h2 data-i18n="about_13">我们的技术</h2>
				<div class="msg aboutOn aboutOn01">
					<p style="margin-bottom: 16px;" data-i18n="about_14">安全、稳定、可靠的技术积累，是对用户资产安全和服务快速精准的有力保障。</p>
					<p data-i18n="about_15">1、扩展HDM钱包技术：采用单一冷钱包和多层热钱包的方式，通过多重签名生成收款地址，既满足钱包的安全性能，又满足钱包和地址的扩充需求。</p>
					<p data-i18n="about_16">2、智能交易所服务解决方案：采用分离的CDN前端集群，物理层防DDOS攻击，同时防宕机。分层LVS与DB代理接口的架构方式，轻松解决前端与后端的扩容问题。</p>
					<p data-i18n="about_17">3、实时记账与事务处理机制：用户交易数控实时记账，全程事物处理。交易完成后数据实时落地，同时冗余备份，更新缓存，确保用户每一笔账目的正确可靠。</p>
				</div>
			</div>
			<div class="aboutCenter04 sdiv divtest clearfix" data-animation="on">
				<ul>
					<li class="aboutOn aboutOn01"><img src="/resource/frontend/img/Groupewew.png" data-i18n="[src]about_18"></li>
					<li class="aboutOn aboutOn02"><img src="/resource/frontend/img/Group_23_Copy.png" data-i18n="[src]about_19"></li>
					<li class="aboutOn aboutOn03"><img src="/resource/frontend/img/Group_sesd.png" data-i18n="[src]about_20"></li>
				</ul>
			</div>
			<div class="aboutCenter05 sdiv divtest" data-animation="on">
				<div class="about05_left">
					<h2 data-i18n="about_21">美国证监会级别的IT安全审计</h2>
					<p data-i18n="about_22">采用类似美国证监会要求赴美上市企业必须满足塞班斯法案404条款来进行IT审计，做到所有服务器操作日志、研发人员对服务器程序任何修改，均可回放查看审计。</p>
				</div>
				<div class="about05_right aboutOn aboutOn02">
						<img src="/resource/frontend/img/America_safe.png">
				</div>
			</div>
<!--			<div class="aboutBottom clearfix sdiv divtest" data-animation="on">
				<h2 data-i18n="about_23"><?= Yii::$app->config->info('WEB_APP_NAME') ?>主要投资方</h2>
				<ul>
					<li class="aboutOn aboutOn01">
						<img src="/resource/frontend/img/5b28dbe766917.png">
						<span data-i18n="about_24">Jinglan</span>
					</li>
					<li class="aboutOn aboutOn02">
						<img src="/resource/frontend/img/5b518be06feb0.png">
						<span data-i18n="about_25">Jinglan</span>
					</li>
					<li class="aboutOn aboutOn03">
						<img src="/resource/frontend/img/5af4f93fa7a76.png">
						<span data-i18n="about_26">Jinglan</span>
					</li>
					<li class="aboutOn aboutOn04">
						<img src="/resource/frontend/img/5a75673183f57.png">
						<span data-i18n="about_27">Jinglan</span>
					</li>
				</ul>
			</div> -->
		</div>
	</div>
</div>
								
</script>
<script type="text/javascript">
$(function(){
	isScroll.init('.divtest');
})
</script>