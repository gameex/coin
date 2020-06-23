<link href="/resource/frontend/css/uc.css" rel="stylesheet">  
<div id="main">
    <div class="main_box">
        <?php echo $this->render('left.php'); ?>
        <div class="raise right bg_w clearfix" >
            <div class="ybc_list">
                <div class="ybcoin clearfix">
                    <h2 class="left" data-i18n="u_c_1">Invite rewards</h2>
                </div> 
                <div class="link-target">
                <ul>
                <li>
                <span class="link"></span>
                <button class="wl-btn link-btn copy-text">Copy Invitation Links</button>
                </li>
                <li>
                <span class="code"></span>
                <button class="wl-btn code-btn copy-text">Copy Invitation Code</button>
                </li>
                </ul>
                </div>
                <div class="total-reward">
                <ul class="rewards">
                    <li>
                        <span>Total Invitation Rewards:</span>
                        <span class="rewards-value rewards-num">0.0000</span> 
                        <span class="coin_symbol"></span>
                        <div class="tip-div">
                            <span class="tip-icon"><a href='<?= Yii::$app->config->info('WEB_LINK_HELP') ?>' target="_blank">?</a></span>
                        </div>
                     </li>
                     <!--
                    <li>
                        <span>累积交易手续费奖励:</span><span class="rewards-value rewards-fee">0.0000</span>
                        <span class="coin_symbol"></span>
                        <div class="tip-div">
                            <span class="tip-icon"><a href='http://ex2.kinlink.cn' target="_blank">?</a></span>
                        </div>                    
                    </li>-->
                </ul>
                <ul class="rewards-detail">
                    <li>
                        <p class="level-1">0</p>
                        <p>First-level invitations</p>
                    </li>
                    <em></em>
                    <li>
                        <p class="level-2">0</p>
                        <p>Second-level invitations</p>

                    </li>
                    <em></em>
                    <li>
                        <p class="level-3">0</p>
                        <p>Three-level invitations</p>
                    </li>
                </ul>
                <ul class="rewards-detail">
                    <li>
                        <p><span class="rewards-num">0.0000</span> <span class="coin_symbol"></span></p>
                        <p>Total rewards</p>
                    </li>
                    <em></em>
                    <li>
                         <p><span class="frozen-num">0.0000</span> <span class="coin_symbol"></span></p>
                        <p>Thawed rewards</p>
                    </li>
                    <em></em>
                    <li>
                         <p><span class="freeze-num">0.0000</span> <span class="coin_symbol"></span></p>
                        <p>Frozen rewards</p>
                    </li>
                    </ul>                    
                </div>

                <div class="referer-lead">
                    <h3>Invitation Rank</h3>
                    <table class="latest-list-new" align="center" border="0" cellpadding="0" cellspacing="0">
                    <thead>
                    <tr height="40">
                    <th class="header" width="190">Rank</th>
                    <th class="header" width="380">Name</th>
                    <th class="header" width="200">Get rewards<span class="coin_symbol"></span></th>
                    </tr>
                    </thead>
                    <tbody >
                        
                    </tbody>
                    </table>
                </div>

        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>

<script type="text/javascript" src="/resource/frontend/js/clipboard.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        http.post('user/invite-info', {
        }, function(res) {
            $(".coin_symbol").html(res.data.coin_symbol);
            $(".level-1").html(res.data.level_1_num);
            $(".level-2").html(res.data.level_2_num);
            $(".level-3").html(res.data.level_3_num);
            $(".rewards-num").html(res.data.invite_rewards);
            $(".rewards-fee").html(res.data.fee_rewards);
            $(".freeze-num").html(res.data.freeze_rewards);
            $(".frozen-num").html(res.data.frozen_rewards);
            $(".code").html(res.data.invite_code);
            $(".link").html(res.data.invite_url);

            $('.link-btn').attr('data-clipboard-text',res.data.invite_url);
            $('.code-btn').attr('data-clipboard-text',res.data.invite_code);
        });

        var clipboard =  new ClipboardJS('.copy-text', {
            text: function(trigger) {
                 return trigger.getAttribute('data-clipboard-text');
            }
        });
        clipboard.on('success', function(e) {
             http.info('Copy success')
        });

        //rank
        http.post('user/invite-rank', {
        }, function(res) {

            List = res.data;
            
            var rank_index = 0;
            $.each(List, function(index,r) {

                 rank_index = index +1 ;
                 $('.latest-list-new').append('<tr height="32"><td>No.'+ rank_index +'</td><td>'+ r.username +'</td><td>'+r.total_invite_rewards+'</td></tr>')

            });

        });

    });   
</script>