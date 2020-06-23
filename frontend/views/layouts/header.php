<?php
use api\models\ExchangeCoins;
use yii\web\Session;
$session = new Session;
$session->open();
$isLogin = intval($session['user'])<=0 ? false : true;
$market = ExchangeCoins::find()->where(['enable'=>1])->select('stock,money')->orderBy('listorder DESC')->asArray()->one();
$show_night = 0;
?>
 <div style="" class="headerUser">
        <div class="headerRight">
            <div class="person right clearfix">
                <ul class="navLift">

     				<?php if($isLogin){?>
     				
     				 <li>
                            <a href="/uc/passwd" data-i18n="recharge">申购</a>
                        </li>   
     				
                    <li>
                        <a href="/uc/recharge#BTC" data-i18n="recharge">充值</a>
                    </li>
                    <li>
                        <a href="/uc/withdraw#BTC" data-i18n="withdraw">提现</a>
                    </li>
                    <li id="getAsset">
                        <a href="/uc/assets" class="asset_header_hover" data-i18n="assets">资产</a>
                        <div class="mywalletView clearfix" style="min-width: 220px;display: none;">
                            <div class="mywallet" style="">
                                <h2 data-i18n="nav_3">总资产</h2>
                                <p class="assets assetsAll"><span class="totalAssets">≈$0</span></p>
                                <p class="information_new">
                                    <a href="/uc/assets" class="asset_uc" data-i18n="nav_4">资产明细</a>
                                    <a href="/uc/cashlog" class="fin" data-i18n="u_c_1">财务日志</a>
                                </p>
                            </div>
                        </div>
                    </li>
                    <li id="getMessage" style="position: relative; display: inline-block;" class="msging en_none">
                        <a href="/uc/message" class="news" data-i18n="news">消息</a>
                        <!-- <span class="msgNnm" num="0" style="display: none;">0</span> -->
                        <div class="myMsgView clearfix">
                            <div class="msgView">
                                <p><font data-i18n="a_notice">通知消息</font>
                                    <!-- <span data-i18n="a_readed" class="read"  style="color: rgb(153, 153, 153);">标为已读</span></p> -->
                                <ul id="useMessage">
                                	<!--<li class="msglist" >
                                		<a href="javascript:;">LBTC超级存币活动升级版，每日获千分之四的利息，随到随存</a>
                                		<i>4小时前</i>
                                	</li>-->
                                </ul>
                                <a href="/uc/message" class="msgAll" data-i18n="a_lookmore" style="display: none;">查看更多</a>
                                <span class="msgBlock" data-i18n="a_unread" style="display: inline-block;">暂无未读消息</span>
                            </div>
                        </div>
                    </li>
                    <li id="getMyselfInfo" class="asset_header" style="position: relative;">
                        <a href="/uc" class="asset_headerHover" data-i18n="personalcenter">个人中心</a>
                        <div class="myuserView clearfix">
                            <div class="myUser">
                                <div class="uidTOP">
                                    <span class="uid" uid="186109136">UID&nbsp;&nbsp;</span>
                                    <a href="/uc" class="user_mine" data-i18n="a_intoperson">进入个人中心</a>
                                </div>
                                <div class="userBottom">
                                    <ul>
                                        <a href="/uc/entrusted"><li><font data-i18n="nav_2">当前委托</font><i></i></li></a>
                                        <a href="/uc/password"><li><font data-i18n="u_a_21">修改密码</font><i></i></li></a>
                                        <a href="/uc/assets"><li><font data-i18n="u_f_8">我的资产</font><i></i></li></a>
                                        <a href="/uc/verified"><li><font data-i18n="u_f_8">实名认证</font><i></i></li></a>
                                        <a href="/uc/cashlog"><li><font data-i18n="u_f_8">财务日志</font><i></i></li></a>
                                        <a href="/uc/message"><li><font data-i18n="u_f_8">系统消息</font><i></i></li></a>
                                  <!--  <a href="/wakuang/mining.html"><li><font data-i18n="u_f_8">挖矿</font><i></i></li></a>
                                      	<a href="/uc/wak"><li><font data-i18n="u_f_8">挖矿记录</font><i></i></li></a>  -->
                                    </ul>
                                </div>                              
                                <a class="out" href="/login/out" onclick="javascript:window.localStorage.clear(),window.sessionStorage.clear();" data-i18n="a_quit">退出</a>
                            </div>
                        </div>
                    </li>
                        <?php if($show_night == 1){?>
                            <?php if($_SESSION['mode']=='night'){?>
                                <li>
                                    <a href="#" onclick="change_day()">日间模式</a>
                                </li>
                            <?php }else{ ?>
                                <li>
                                    <a href="#" onclick="change_night()">夜间模式</a>
                                </li>
                            <?php } ?>
                        <?php } ?>



                                 <a href="javascript:void(0)" class="regMult">
                                 <img style="width:24px;height:16px;margin-top:15px;margin-left:30px;" src="/resource/frontend/img/jtzw@2x.png" data-i18n="[src]language_img" alt="">
                                 <div class="multlang_cont multlang_regcont">
                                 <ul>
                                     <li class="zh"><img src="/resource/frontend/img/jtzw@2x(1).png">简体中文</li>
                                     <li class="en"><img src="/resource/frontend/img/English@2x.png">English</li>
                                 </ul>
                               </div>
                            </a>  
     				<?php }?>

                    <li style="display: none;">
                        <a href="/download/" data-i18n="appdownload">APP下载</a>
                    </li>
                     <li class="langhover" style="position: relative; display: inline-block;display: none;">
                        <a href="/userInfoCenter.html#" class="multlang"><img src="/resource/frontend/img/jtzw@2x(1).png" data-i18n="[src]language_img" alt=""></a>
                        <div class="multlang_cont">
                            <ul>
                                <li class="zh"><img src="/resource/frontend/img/jtzw@2x(1).png">简体中文</li>                              
                                <li class="en"><img src="/resource/frontend/img/English@2x.png">English</li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="headertop">
        <div class="wapper clearfix">
                 <h1 class="left" style="width:150px;">
                  <a href="/" style="margin-top:5px;">
                    <img src="<?= Yii::$app->config->info('WEB_SITE_LOGO') ?>" alt="<?= Yii::$app->config->info('WEB_APP_NAME') ?>">
                </a>
            </h1>
            <?php if(!$isLogin){ ?>
            <div class="loginArea right" style="display: block;margin-top: 16px;">
                <?php if($show_night == 1){?>
                    <?php if($_SESSION['mode']=='night'){?>
                            <a href="#" onclick="change_day()">日间模式</a>
                    <?php }else{ ?>
                            <a href="#" onclick="change_night()">夜间模式</a>
                    <?php } ?>
                <?php } ?>
                
               	<a href="/login" class='' data-i18n="login">登录</a>
                <a href="/reg" class="h_regist" data-i18n="registration" style='color: #ffffff;height: 40px;line-height: 32px;border-radius: 5px;background-color: #659aea;'>注册</a>
                 <a href="/download/" data-i18n="appdownload">APP下载</a>  
                 <a href="javascript:void(0)" class="regMult">
                    <img src="/resource/frontend/img/jtzw@2x(1).png" data-i18n="[src]language_img" alt="">
                    <div class="multlang_cont multlang_regcont">
                        <ul>
                            <li id="zh"><img src="/resource/frontend/img/jtzw@2x(1).png">简体中文</li>                          
                            <li class="en"><img src="/resource/frontend/img/English@2x.png">English</li>
                        </ul>
                    </div>
                </a>
            </div>
            <?php }?>
            <ul class="nav" style="z-index:9995;width: 500px;">

                <li class="trading_nav" style="font-family:Arial;">
                    <a href="/trade/<?= $market['money']?>/<?= $market['stock']?>" data-i18n="exchange">币币交易</a>
                </li>
               <li>
                    <a class="sell" href="/sellbtc">场外交易</a>
                </li>
                <li class="en_none" style="font-family:Arial;"id='gonggao'>
                    <a href="<?= Yii::$app->config->info('WEB_LINK_NOTICE') ?>">公告</a>
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

$(".en").click(function(){
    window.location.href = <?php echo "'".substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'], '?'))."'"; ?> + "?language=en";
});
function change_day(){
    window.location.href = <?php echo "'".substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'], '?'))."'"; ?> + "?mode=day";
}
function change_night(){
    window.location.href = <?php echo "'".substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'], '?'))."'"; ?> + "?mode=night";
}
</script>
