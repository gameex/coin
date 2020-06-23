<?php
use api\models\ExchangeCoins;
use yii\web\Session;
$session = new Session;
$session->open();
$isLogin = intval($session['user'])<=0 ? false : true;
$market = ExchangeCoins::find()->where(['enable'=>1])->select('stock,money')->orderBy('listorder DESC')->asArray()->one();
$show_night = 0;
?>
<style>
body{min-width:1400px}
	.navLift li .msgView .msglist {
	    display: block !important;
	    height: 28px !important;
	    line-height: 28px !important;
	    margin: 0 !important;
	    float: inherit !important;
	    width: 100%;
	}
	.navLift li .msgView .msglist a {
	    max-width: 208px;
	    font-size: 12px;
	    color: #555 !important;
	    white-space: nowrap;
	    text-overflow: ellipsis;
	    overflow: hidden;
	    line-height: 16px;
	    height: 16px !important;
	    padding-top: 6px;
	}
  .mes{
            padding:70px 0;
            width:100%;
        }
        .tb{

            width:1140px;
            margin:auto;
        }
        .tbT{
            border:1px solid #dce1f2;
            height:45px;
            line-height:45px;
            border-radius:8px;
        }
        .tbT div{
            width:160px;
            color:#1f34d5;
            text-align:center;
            line-height:45px;
            border-radius:8px;
        }
        .tbT .tbTClick{
            background-color:#1f34d5;
            color:#fff;
        }
        .tbMain {
            width:1140px;
            margin:auto;
            margin-top:20px;
            border:1px solid #dce1f2;
            border-radius:8px;
            border-bottom-left-radius:0;
            border-bottom-right-radius:0;
        }
        .tbMainT>div{
            width:190px;
            text-align: center;
            line-height:40px;
            color:#3d3dce;
            font-weight: bold;
        }
        .vi{
            background-color:#dce1f2;
        }
        .vi>div{
            font-size:14px;
            width:190px;
            text-align: center;
            line-height:40px;
            color:#3d3dce;
            font-weight: bold;
        }
        .listBox>div{
            width:190px;
            text-align: center;
            line-height:40px;
            color:#3d3dce;
            font-weight: bold;
        }
        .allList{
            width:1140px;
            margin:auto;
            border:1px solid #dce1f2;
            border-top:0;
        }
        .listBox{
            border-top:1px solid #dce1f2;
        }
        .allList .listBox:first-child{
            border-top:0;
        }
        .listBox span{
            color:#999;
        }
  .loginArea {
    width: 500px;
  }  
  .headertop {
    min-width:1400px;
    width:100%;
    background-color: #fff;
    box-shadow: 0 3px 6px 0 rgba(192,222,255,.5);
     }
  	.nav li a {
    line-height: 60px;
    height: 50px;
    padding: 0 15px;
    font-size: 14px;
    margin: 0 5px;
    color: #000;
  }	
   	.nav li a:hover {
    line-height: 60px;
    height: 50px;
    padding: 0 15px;
    font-size: 14px;
    margin: 0 5px;
    color: #545de2;
    border-bottom: 2px solid #2c5ad7;
  }	 
  .nav li a.active{
    line-height: 60px;
    height: 50px;
    padding: 0 15px;
    font-size: 14px;
    margin: 0 5px;
  }
  
  .loginArea a, .person a{    
    color: #000;
  }
  .headerUser .person .navLift li a.active{
    color: #545de2;
    border-bottom: 2px solid #2c5ad7;    
   } 
  .headerUser .person .navLift li a{
    color: #000;
   }  
  .headerUser .person .navLift li a:hover {
    color: #545de2;
    border-bottom: 2px solid #1b4bfa; 
   } 
</style>
 <div style="" class="headerUser">
        <div class="headerRight">
            <div class="person right clearfix">
                <ul class="navLift">
     				<?php if($isLogin){?>
                    <li>
                        <a href="/uc/recharge#ETH" data-i18n="recharge">Deposits</a>
                    </li>
                    <li>
                        <a href="/uc/withdraw#BTC" data-i18n="withdraw">withdraw</a>
                    </li>
                    <li id="getAsset">
                        <a href="/uc/assets" class="asset_header_hover" data-i18n="assets">Assets</a>
                        <div class="mywalletView clearfix" style="min-width: 220px;display: none;">
                            <div class="mywallet" style="">
                                <h2 data-i18n="nav_3">Total Assets</h2>
                                <p class="assets assetsAll"><span class="totalAssets">≈$0</span></p>
                                <p class="information_new">
                                    <a href="/uc/assets" class="asset_uc" data-i18n="nav_4">Asset details</a>
                                    <a href="/uc/cashlog" class="fin" data-i18n="u_c_1">Financial log</a>
                                </p>
                            </div>
                        </div>
                    </li>
                    <li id="getMessage" style="position: relative; display: inline-block;" class="msging en_none">
                        <a href="/uc/message" class="news" data-i18n="news">Message</a>
                        <!-- <span class="msgNnm" num="0" style="display: none;">0</span> -->
                        <div class="myMsgView clearfix">
                            <div class="msgView">
                                <p><font data-i18n="a_notice">Notification message</font>
                                    <!-- <span data-i18n="a_readed" class="read"  style="color: rgb(153, 153, 153);">标为已读</span></p> -->
                                <ul id="useMessage">
                                	<!--<li class="msglist" >
                                		<a href="javascript:;">LBTC超级存币活动升级版，每日获千分之四的利息，随到随存</a>
                                		<i>4小时前</i>
                                	</li>-->
                                </ul>
                                <a href="/uc/message" class="msgAll" data-i18n="a_lookmore" style="display: none;">Check more</a>
                                <span class="msgBlock" data-i18n="a_unread" style="display: inline-block;">No Unread news</span>
                            </div>
                        </div>
                    </li>
                    <li id="getMyselfInfo" class="asset_header" style="position: relative;">
                        <a href="/uc" class="asset_headerHover" data-i18n="personalcenter">User Center</a>
                        <div class="myuserView clearfix">
                            <div class="myUser">
                                <div class="uidTOP">
                                    <span class="uid" uid="186109136">UID&nbsp;&nbsp;</span>
                                    <a href="/uc" class="user_mine" data-i18n="a_intoperson">User Center</a>
                                </div>
                                <div class="userBottom">
                                    <ul>
                                        <a href="/uc/entrusted"><li><font data-i18n="nav_2">My order</font><i></i></li></a>
                                        <a href="/uc/password"><li><font data-i18n="u_a_21">Modify password</font><i></i></li></a>
                                        <a href="/uc/assets"><li><font data-i18n="u_f_8">My assets</font><i></i></li></a>
                                        <a href="/uc/verified"><li><font data-i18n="u_f_8">Authentication</font><i></i></li></a>
                                        <a href="/uc/cashlog"><li><font data-i18n="u_f_8">Financial log</font><i></i></li></a>
                                        <a href="/uc/message"><li><font data-i18n="u_f_8">System message</font><i></i></li></a>
                                  <!--  <a href="/wakuang/mining.html"><li><font data-i18n="u_f_8">Mining</font><i></i></li></a>
                                      	<a href="/uc/wak"><li><font data-i18n="u_f_8">Mining Log</font><i></i></li></a>  -->
                                    </ul>
                                </div>                              
                                <a class="out" href="/login/out" onclick="javascript:window.localStorage.clear(),window.sessionStorage.clear();" data-i18n="a_quit">Log out</a>
                            </div>
                        </div>
                    </li>
                        <?php if($show_night == 0){?>
                            <?php if($_SESSION['mode']=='night'){?>
                                <li>
                                    <a href="#" onclick="change_day()">Day Mode</a>
                                </li>
                            <?php }else{ ?>
                                <li>
                                    <a href="#" onclick="change_night()">Night Mode</a>
                                </li>
                            <?php } ?>
                        <?php } ?>                                 
								 <a href="javascript:void(0)" class="regMult">
                                 <img style="width:24px;height:16px;margin-top:15px;margin-left:30px;" src="/resource/frontend/img/English@2x.png" data-i18n="[src]language_img" alt="">
                                 <div class="multlang_cont multlang_regcont">
                                 <ul>
                                     <li class="zh"><img src="/resource/frontend/img/jtzw@2x(1).png"><font color="#FF0000">简体中文</font></li>
                                     <li class="en"><img src="/resource/frontend/img/English@2x.png"><font color="#FF0000">English</font></li>
                                 </ul>
                               </div>
                            </a>  
     				<?php }?>

                    <li style="display: none;">
                        <a href="/download/" data-i18n="appdownload">APP Download</a>
                    </li>
                     <li class="langhover" style="position: relative; display: inline-block;display: none;">
                        <a href="/userInfoCenter.html#" class="multlang"><img src="/resource/frontend/img/English@2x.png" data-i18n="[src]language_img" alt=""></a>
                        <div class="multlang_cont">
                            <ul>
                                <li class="zh"><img src="/resource/frontend/img/jtzw@2x(1).png"><font color="#FF0000">简体中文</font></li>
                                <li class="en"><img src="/resource/frontend/img/English@2x.png"><font color="#FF0000">English</font></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="headertop">
        <div class="wapper clearfix"style="width: 1400px;">
                 <h1 class="left" style="width:130px;">
                  <a href="/" style="margin-top:5px;">
                    <img src="<?= Yii::$app->config->info('WEB_SITE_LOGO') ?>" alt="<?= Yii::$app->config->info('WEB_APP_NAME') ?>">
                </a>
            </h1>
            <?php if(!$isLogin){ ?>
            <div class="loginArea right" style="display: block;margin-top: 16px;">
                <?php if($show_night == 0){?>
                    <?php if($_SESSION['mode']=='night'){?>
                            <a href="#" onclick="change_day()">Day Mode</a>
                    <?php }else{ ?>
                            <a href="#" onclick="change_night()">Night Mode</a>
                    <?php } ?>
                <?php } ?>
               	<a href="/login" class='' data-i18n="login">Login</a>
                <a href="/reg" class="h_regist" data-i18n="registration" style='color: #ffffff;height: 40px;line-height: 32px;border-radius: 5px;background-color: #659aea;'>Register</a>
                 <a href="/download/" data-i18n="appdownload">APP download</a>
                 <a href="javascript:void(0)" class="regMult">
                    <img src="/resource/frontend/img/English@2x.png" data-i18n="[src]language_img" alt="">
                    <div class="multlang_cont multlang_regcont">
                        <ul>
                            <li class="zh"><img src="/resource/frontend/img/jtzw@2x(1).png"><font color="#FF0000">简体中文</font></li>
                            <li class="en"><img src="/resource/frontend/img/English@2x.png"><font color="#FF0000">English</font></li>
                        </ul>
                    </div>
                </a>
            </div>
            <?php }?>
            <ul class="nav" style="z-index:9995;width: 450px;">
                <li class="trading_nav" style="font-family:Arial;">
                    <a href="/trade/<?= $market['money']?>/<?= $market['stock']?>" data-i18n="exchange">Exchange</a>
                </li>
               <li>
                    <a  href="/sellbtc">OTC</a>
                </li>
                <li class="en_none" style="font-family:Arial;"id='gonggao'>
                    <a href="<?= Yii::$app->config->info('WEB_LINK_NOTICE') ?>">Announcement</a>
                </li>
                <!--li>
                    <a href="/help" data-i18n="help">Help</a>
                </li-->
				<li>
					<a href="/uc/passwd" data-i18n="u_j_45">Purchase</a>
                </li>
            </ul>
       </div> 
</div>
<script type="text/javascript">
    <?php if($isLogin){ ?>
        localStorage.setItem('access_token', '<?php echo $session['access_token'] ?>');
    <?php }else{ ?>
        window.localStorage.clear(),
        window.sessionStorage.clear();
    <?php }?>

		//请求接口获取总资产；
			//console.log($('#getAsset'))
			$(function(){
				    var ind = 1;//第一次未进入请求
					$('#getAsset').on('mouseenter',function(){
						if(ind == 1)http.post('bargain/balance',{},function(res){
							console.log(res)
										$('.totalAssets').html('≈$'+((res.data.total_money).toFixed(2)));
										ind=0;
								});
					})

					http.post('user/user-info',{},function(re){
						console.log(re)
						$('.uid').append(re.data.UID).attr('uid',re.data.UID)
					})

					http.post('user/message-list',{type:1,limit_begin:0,limit_num:5},function(res){
						console.log(res);
						$('.msgNnm').css('display','block').html(res.count).attr('num',res.count);
						$('.msgBlock').css('display','none');
						$('.msgAll').css('display','block');
						$.each(res.data, function(index,re) {
							$('#useMessage').append('<li class="msglist" >'+
                                		'<a href="#">'+re.title+'</a>'+
                                		'<i>'+re.add_time+'</i>'+
                                	'</li>')
						});

					})
			})


$(".zh").click(function(){
    window.location.href = <?php echo "'".substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'], '?'))."'"; ?> + "?language=zh";
});
function change_day(){
    window.location.href = <?php echo "'".substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'], '?'))."'"; ?> + "?mode=day";
}
function change_night(){
    window.location.href = <?php echo "'".substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'], '?'))."'"; ?> + "?mode=night";
}
</script>