<?php
$this->title = '首页';
$this->params['breadcrumbs'][] = ['label' => $this->title];

use yii\helpers\Html;

?>

<?= Html::cssFile('/resource/backend/css/total.css') ?>

<div class="wrapper wrapper-content">
    <div class="row">
        <?= backend\widgets\baseinfo\InfoWidget::widget() ?>
    </div>
<div class="ex-row">
        <!--  用户统计模块开始  -->
        <div class="ex-member">
            <div class="boardCol">
                <div class="ex-card ex-card-bordered">
                    <div class="ex-card-head">
                        <div class="ex-row">
                            <div class="ex-col ex-col-span-8">用户类型</div>
                            <div class="ex-col ex-col-span-8">累计</div>
                            <div class="ex-col ex-col-span-8">昨日新增</div>
                        </div>
                    </div> <!---->
                    <div class="ex-card-body">
                        <div class="cardBody">
                            <div class="ex-row">
                                <div class="ex-col ex-col-span-8">注册用户</div>
                                <div class="ex-col ex-col-span-8"><?= $data['member_count']['total']['member_total'] ?></div>
                                <div class="ex-col ex-col-span-8"><?= $data['member_count']['yesterday_total']['member_total'] ?></div>
                            </div>
                            <div class="ex-row">
                                <div class="ex-col ex-col-span-8">实名用户</div>
                                <div class="ex-col ex-col-span-8"><?= $data['member_count']['total']['verified_member_total'] ?></div>
                                <div class="ex-col ex-col-span-8"><?= $data['member_count']['yesterday_total']['verified_member_total'] ?></div>
                            </div>
                            <div class="ex-row">
                                <div class="ex-col ex-col-span-8">认证商家</div>
                                <div class="ex-col ex-col-span-8"><?= $data['member_count']['total']['otc_merchant_total'] ?></div>
                                <div class="ex-col ex-col-span-8"><?= $data['member_count']['yesterday_total']['otc_merchant_total'] ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!--  用户统计模块结束  -->

        <!--   交易模块开始     -->
        <div  class="ex-transaction">
            <!--   法币交易模块开始         -->
            <div  class="boardCol">
                <div  class="ex-card ex-card-bordered">
                    <div class="ex-card-head">
                        <div  class="cardTitle">
                            <span>法币交易</span>
                            <div  class="selectWrapper">
                                <select name="coin_name" id="coin-select" >
                                    <?php foreach ($data['otc_coin'] as $otc_coin_val) { ?>
                                        <option value="<?=$otc_coin_val['coin_id']?>"><?= $otc_coin_val['coin_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            </div>
                        </div>
                    </div> <!---->
                    <div class="ex-card-body">
                        <div  class="ex-row">
                            <div  class="ex-col ex-col-span-8">
                                <p  class="tradeTotalNum otcNum"></p>
                                <span >法币交易总交易量</span>
                            </div>
                            <div  class="ex-col ex-col-span-8">
                                <p  class="tradeTotalMoney otcMoney"></p>
                                <span >总交易额</span>
                            </div>
                            <div  class="ex-col ex-col-span-8 ">
                                <p  class="fee serverFee">0</p>
                                <span >总手续费</span>
                            </div>
                        </div>
                        <div  class="cardFooter">
                            <div  class="ex-col-offset-1">
                                <p >昨日交易量 <span class="otcOldCount"></span></p></div>
                            <div  class="ex-col-offset-1">
                                <p >昨日手续费 <span class="otcOldServerFee">0</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!--   法币交易模块结束         -->
            <br >

            <!--   币币交易模块开始         -->
            <div  class="boardCol">
                <div  class="ex-card ex-card-bordered">
                    <div class="ex-card-head">
                        <div  class="cardTitle">
                            <span>币币交易</span>
                            <div  class="selectWrapper">
                                <select name="" id="pair-select" >
                                    <?php foreach ($data['exchange_coin'] as $exchange_coin_val) { ?>
                                        <option value="<?=$exchange_coin_val['id']?>"><?= $exchange_coin_val['stock'] ?> /<?= $exchange_coin_val['money']?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div> <!---->
                <div class="ex-card-body">
                    <div  class="ex-row">
                        <div  class="ex-col ex-col-span-8">
                            <p  class="tradeTotalMoney pairMoney"><?=$pairData['pair_money'] ?></p>
                            <span >总交易额</span>
                        </div>
                        <div  class="ex-col ex-col-span-8">
                            <p  class="tradeTotalNum pairAskCount"><?=$pairData['ask_count'] ?></p>
                            <span >买入总交易量</span>
                        </div>
                        <div  class="ex-col ex-col-span-8">
                            <p  class="tradeTotalNum pairBidCount"><?=$pairData['bid_count'] ?></p>
                            <span >卖出总交易量</span>
                        </div>

                    </div>
                    <div  class="cardFooter">
                        <div  class="ex-col-offset-1">
                            <p >买入总交易额 <span class="pairAskMoney"><?=$pairData['ask_amount'] ?></span></p></div>
                        <div  class="ex-col-offset-1">
                            <p >买出总交易额 <span class="pairBidMoney"><?=$pairData['bid_amount'] ?></span></p>
                        </div>
                    </div>
                </div>
            </div>
            <!--   币币交易模块结束         -->
        </div>
        <!--   交易模块结束     -->
    </div>

</div>
<script>
    $(function () {
        var coin_id =  $("#coin-select").val();
        // var pair_id = $("#pair-select").val();
        // getPairTotalData(pair_id)
        getOtcTotalData(coin_id)

        $("#coin-select").change(function () {
            getOtcTotalData($(this).val())
        })
        $("#pair-select").change(function () {
            getPairTotalData($(this).val())
        })
        // 币币交易数据统计
        function getPairTotalData(obj) {
            $.ajax({
                url:"get-pair-data",
                type:"POST",
                data:{id:obj},
                success:function (result) {
                    result = $.parseJSON(result)
                    if(result.code == 200){
                        console.log(result.data)
                        $(".pairBidCount").html(result.data.bid_count)
                        $(".pairAskCount").html(result.data.ask_count)
                        $(".pairMoney").html(result.data.pair_money)
                        $(".pairAskMoney").html(result.data.ask_amount)
                        $(".pairBidMoney").html(result.data.bid_amount)
                    }else{
                        $(".pairBidCount").html(0)
                        $(".pairAskCount").html(0)
                        $(".pairMoney").html(0)
                        $(".pairAskMoney").html(0)
                        $(".pairBidMoney").html(0)
                    }
                }
            })
        }


        // 法币数据统计
        function getOtcTotalData(obj){
            $.ajax({
                url:"get-otc-data",
                type:"POST",
                data:{id:obj},
                success:function (result) {
                    result = $.parseJSON(result)
                    if(result.code == 200){
                        console.log(result.data)
                        $(".otcNum").html(result.data.relaData.count)
                        $(".otcMoney").html(result.data.relaData.sum)
                        $(".otcOldCount").html(result.data.oldData.count)
                    }else{
                        $(".otcNum").html(0)
                        $(".otcMoney").html(0)
                        $(".otcOldCount").html(0)
                    }
                }
            })
        }

    })
</script>
