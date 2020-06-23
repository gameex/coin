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
				<h2 data-i18n="about_1"><?= Yii::$app->config->info('WEB_APP_NAME') ?> Introduction to Exchange </h2>
				<p><font data-i18n="about_2"><?= Yii::$app->config->info('WEB_APP_NAME') ?> Exchange is one of the world's well-known digital asset exchanges. It mainly provides currency trading services for a series of block chain assets such as Bitcoin, ETF and Wright coin. It is founded by block chain and digital asset enthusiasts. The core team is from Xunlei, Tencent, Baidu and HP. Registered in Seychelles by <?= Yii::$app->config->info('WEB_APP_NAME') ?>  LIMITED公司运营，目前运营中心位于新加坡。</font><br><font data-i18n="about_3"><?= Yii::$app->config->info('WEB_APP_NAME') ?>的愿景是能够为区块链资产投资者提供便捷、放心、安全的投资渠道。</font></p>
			</div>
			<div class="aboutCenter sdiv divtest" data-animation="on">
				<ul>
					<li class="aboutOn aboutOn02">
						<span><font data-i18n="about_4">Leading in the industry</font><br><font data-i18n="about_5">Technical framework</font></span>
						<div class="aboutList">
							<div class="aboutNei">
								
							</div>
						</div>
					</li>
					<li class="aboutOn aboutOn03">
						<span><font data-i18n="about_6">100%</font><br><font data-i18n="about_7">Bond</font></span>
						<div class="aboutList">
							<div class="aboutNei">
								
							</div>
						</div>
					</li>
					<li class="aboutOn aboutOn04">
						<span><font data-i18n="about_8">Us SEC</font><br><font data-i18n="about_9">Level auditing system</font></span>
						<div class="aboutList">
							<div class="aboutNei">
								
							</div>
						</div>
					</li>
				</ul>
			</div>
			<div class="aboutCenter02 sdiv divtest" data-animation="on">
				<h2 data-i18n="about_10">Trading is mining</h2>
				<div class="msg aboutOn aboutOn01">
					<p data-i18n="about_11">On <?= Yii::$app->config->info('WEB_APP_NAME') ?> platform, platform passes are available for trading in any currency, and platform fees are awarded for holding passes. Please refer to the website bulletin for specific mining rules and holding permit reward rules.</p>
				</div>
			</div>
			<div class="aboutCenter03 sdiv divtest" data-animation="on">
				<h2 data-i18n="about_13">Our Technology</h2>
				<div class="msg aboutOn aboutOn01">
					<p style="margin-bottom: 16px;" data-i18n="about_14">The accumulation of safe, stable and reliable technology is a powerful guarantee for the safety of users'assets and the fast and accurate service.</p>
					<p data-i18n="about_15">1、Expanding HDM wallet technology: Using single cold wallet and multi-layer hot wallet, the receipt address is generated by multiple signatures, which not only meets the security performance of wallet, but also meets the expansion requirements of wallet and address.</p>
					<p data-i18n="about_16">2、Intelligent Exchange Service Solution: Separate CDN front-end cluster is used to prevent DDOS attacks and downtime in physical layer. Layered LVS and DB proxy interface architecture, easy to solve the front-end and back-end expansion problem.</p>
					<p data-i18n="about_17">3、 Real-time bookkeeping and transaction processing mechanism: NC real-time bookkeeping of user transactions, whole process of transaction processing. After the transaction is completed, the data will land in real time, and the redundant backup and update the cache will ensure the correctness and reliability of each account.</p>
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
					<h2 data-i18n="about_21">Securities Regulatory Commission-level IT Security Audit</h2>
					<p data-i18n="about_22">It is similar to the requirement of the Securities Regulatory Commission of the United States that listed companies in the United States must meet the provisions of the Sarbanes Act 404 to conduct IT audits, so that all server operation logs and any modification of server procedures by R&D personnel can be replayed and audited.</p>
				</div>
				<div class="about05_right aboutOn aboutOn02">
						<img src="/resource/frontend/img/America_safe.png">
				</div>
			</div>
<!--			<div class="aboutBottom clearfix sdiv divtest" data-animation="on">
				<h2 data-i18n="about_23"><?= Yii::$app->config->info('WEB_APP_NAME') ?> Major Investors</h2>
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