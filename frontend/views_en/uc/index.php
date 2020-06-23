
<link href="/resource/frontend/css/uc.css" rel="stylesheet">  
<div id="main">
    <div class="main_box">
        <?php echo $this->render('left.php'); ?>

        <div class="assets_content w950 right bg_w" id="safebox" style=" border-left-style:none!important;">
        	<h1 data-i18n="u_a_2">User center</h1>
            <div class="safe_center clear">
	            <div>               
                    <dl class="dls">
                         <dd class="WS_phone"><span>UID:</span><span class="data_s"></span></dd>
                        <dd class="WS_name"><span>Name:</span><span data-i18n="u_a_3" style="width:100px;" class="data_s"></span>
                            <a href="/uc/verified" class="margin" data-i18n="u_a_26" style="opacity:0;">KYC</a>
                        </dd>
                        <dd class="WS_tele"><span>Phone:</span><span data-i18n="u_a_4" class="data_s"> </span>
                            <a href="/uc/bindphone" class="margin" data-i18n="u_a_28" >Bind phone</a>
                            </dd>

                        <dd class="WS_email"><span>Email:</span><span data-i18n="u_a_5" class="data_s"></span>
                            <a href="/uc/bindemail" class="margin" data-i18n="u_a_28" >Bind email</a>
                        </dd>
                        <dd class="WS_nike"><span>Nickname:</span>
                            <input type="text" placeholder="Please enter name" class="niname" style="width: 80px;border: 1px solid #1747927d;font-size: 14px;color: #999;background: 0 0;margin-left: 30px;" id="ninames" value="" maxlength="20">
                            <button class="modify" data-i18n="u_a_7">Modify</button>
                        </dd>
                    </dl>
                </div>
                <div style="float:left; margin-left:70px;position: relative;">
                    <dl style=" padding-top:0px !important;color: #333;" class="dls">
                        <dd>
                            <font data-i18n="u_f_31">After KYC, the daily withdrawal can be increased to 10 BTC.</font>
                            <a href="/uc/verified" data-i18n="u_a_26" class="ws_Real">Go to KYC</a>
                        </dd>                       
                        <dd style="margin-top: 0px;" data-i18n="">
                            <font data-i18n="a_assets">Total assets</font>：<em class="em_color"><font data-i18n="u_b_4" class="user_money">$0</font> </em>
                            <a href="/uc/assets" data-i18n="u_a_10">Details</a>
                        </dd>
                        <dd style="display: none;">Main station：　<em class="em_color">data-i18n="u_b_4"&gt;约 $ 0 </em>Main plate：<em class="em_color">data-i18n="u_b_4"&gt;About￥0</em></dd>
                    </dl>
                </div>
            </div>
            <ul class="sc_statu">
                <li style="width: 300px;">
                    <em class="sc_statu_type_1_1"></em>
                    <dl>
                        <dt data-i18n="u_a_14">KYC</dt>
                        <dd class="s-verify">
                        	<font data-i18n="u_f_29">Uncertified</font>
                        	<a href="/uc/verified" data-i18n="u_f_30">Click to KYC</a>
                        </dd>
                    </dl>
                </li>
                <li style="width: 300px;">
                    <em class="sc_statu_type_1_1"></em>
                    <dl>
                        <dt data-i18n="u_a_14">Email bind</dt>
                        <dd class="s-email">
                            <font data-i18n="u_f_29">Unbind</font>
                            <a href="/uc/bindemail" data-i18n="u_f_30">Click to bind</a>
                        </dd>
                    </dl>
                </li>   
                <li style="width: 300px;">
                    <em class="sc_statu_type_1_1"></em>
                    <dl>
                        <dt data-i18n="u_a_14">Phone bind</dt>
                        <dd class="s-phone">
                            <font data-i18n="u_f_29">Unbind</font>
                            <a href="/uc/bindphone" data-i18n="u_f_30">Click to bind</a>
                        </dd>
                    </dl>
                </li>                               
            </ul>

        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        http.post('user/user-info', {
        }, function(res) {
            $(".dls dd").css({"color":'#333'});
            $(".dls dd .data_s").css({"color":'#999'});
            $(".WS_phone .data_s").html(res.data.UID);
            $(".WS_tele .data_s").html(res.data.mobile_phone);
            $(".WS_email .data_s").html(res.data.email);
            // if(res.data.verified_status != 0){
            //     $(".WS_name .data_s").html(res.data.name);
            //     $(".ws_Real").html(res.data.status_msg);
            //     $(".nopass font").html(res.data.status_msg); 
            //     $(".nopass a").css({"opacity":0});
            // }else{

              
                if(res.data.verified_status == 1){
                    $(".WS_name  .data_s").html(res.data.name);
                    $(".ws_Real").html(res.data.verified_status_msg);
                    $(".ws_Real a").css({"text-decoration":"none"});
                    $(".ws_Real a").attr("href","#");
                    $(".s-verify font").html(res.data.verified_status_msg); 
                    $(".s-verify a").hide();
                    $(".s-verify ").css({"background":'url(/resource/frontend/img/ico_renzhengchenggong.png) no-repeat'});
                }else if(res.data.verified_status == 2){
                    $(".WS_name  .data_s").html(res.data.name);
                    $(".ws_Real").html(res.data.verified_status_msg);
                    $(".ws_Real a").css({"text-decoration":"none"});
                    $(".ws_Real a").attr("href","#");
                    $(".s-verify font").html(res.data.verified_status_msg); 
                    $(".s-verify a").hide();
                    $(".s-verify ").css({'background':'url(/resource/frontend/img/ico_renzhengchenggong.png) no-repeat'});
                }else if(res.data.verified_status == 3){
                    $(".WS_name  .data_s").html(res.data.name);
                    $(".ws_Real").html(res.data.verified_status_msg);
                    $(".ws_Real a").css({"text-decoration":"none"});
                    $(".ws_Real a").attr("href","#");
                    $(".s-verify font").html(res.data.verified_status_msg); 
                    $(".s-verify a").hide();
                    $(".s-verify ").css({'background':'url(/resource/frontend/img/ico_renzhengshibai.png) no-repeat'});
                }else if(res.data.verified_status == 0){
                    $(".WS_name .data_s").css({"display":'none'});
                    $(".WS_name a").show();
                    $(".s-verify a").show();
                    $(".s-verify").css({"background":'url(/resource/frontend/img/selectedb.png) no-repeat'});
                }
                //$(".nopass").addClass("alpass");
                //$(".alpass").removeClass("nopass");
                
            // }

            if(res.data.email != ''){
                $(".WS_email .data_s").html(res.data.email);
                $(".WS_email a").hide();
                $(".s-email a").hide();
                $(".s-email font").html('Bind success'); 
                $(".s-email ").css({'background':'url(/resource/frontend/img/ico_renzhengchenggong.png) no-repeat'});               
            }else{
                $(".WS_email .data_s").css({"display":'none'});
                $(".WS_email a").show();
                
                $(".s-email ").css({'background':'url(/resource/frontend/img/selectedb.png) no-repeat'}); 
            }

             if(res.data.mobile_phone != ''){
                $(".WS_tele .data_s").html(res.data.mobile_phone);
                $(".s-phone font").html('Bind success'); 
                $(".WS_tele a").hide();
                $(".s-phone a").hide();
                $(".s-phone ").css({'background':'url(/resource/frontend/img/ico_renzhengchenggong.png) no-repeat'});                  
            }else{
                $(".WS_tele .data_s").css({"display":'none'});
                $(".WS_tele a").show();              
                $(".s-phone ").css({'background':'url(/resource/frontend/img/selectedb.png) no-repeat'}); 
            }

            if(res.data.WS_nike != ''){
                $(".WS_nike input").val (res.data.nickname);
            }
            
        });
        $(".modify").click(function(event) {
            if($(this).html() == "修改"){
                $(this).html('保存');
                var result=$("#ninames").val();
                $("#ninames").focus();
            }else if($(this).html() == "保存"){
                $(this).html('修改');
                var result=$("#ninames").val();
                http.post('user/nickname-edit', {
                    nickname:result
                }, function(res) {
                });
            }
        });

        http.post('/bargain/balance', {
            asset_type:''
        }, function(res) {
            if(res.code == 200){
                $(".user_money").html('≈$'+res.data.total_money);
            }
        });
    });
</script>