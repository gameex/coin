<?php
use yii\widgets\ActiveForm;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '交易对列表', 'url' => ['transaction-pair']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    element.style{
        top: 447px;
        left: 908px;
    }
    .popover.top{
        margin-top: 10px;
    }

</style>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>添加交易对</h5>
                </div>
                <div class="ibox-content">
                    <div  id="stock" class="formula-hint"  title="提示" data-container="body" data-toggle="popover" data-placement="top" data-content="请勿使用同样的币种"></div>

                    <?php 
                        $model->enable = 1;
                        $form = ActiveForm::begin(); 
                    ?>
                    <div class="col-sm-12">
                        <?= $form->field($model, 'stock_coin_id')
                            ->dropDownList(common\models\Coins::find()
                            ->select(['symbol'])
                            ->indexBy('id')
                            ->column(),
                            [
                                'value' => 5,
                                'prompt' =>['text'=>'全部', 'options'=>['value'=>0]],
                            ]
                        ) ?>

                        <?= $form->field($model, 'stock')->textInput(['readonly' => true]) ?>

                        <?= $form->field($model, 'money_coin_id')->dropDownList(common\models\Coins::find()
                            ->select(['symbol'])
                            ->indexBy('id')
                            ->column(),
                            [

                                'prompt' =>['text'=>'全部', 'options'=>['value'=> 100]],
                            ]
                        ) ?>
                        <?= $form->field($model, 'money')->textInput(['readonly' => true]) ?>

                        <div  id="limit_amount" class="formula-hint"  title="提示" data-container="body" data-toggle="popover" data-placement="top" data-content="最低发布值不能小于0.0001"></div>

                        <?= $form->field($model, 'limit_amount')->textInput(['placeholder' => '默认值为0.0001'])->label() ?>
                        <div  id="taker_fee" class="formula-hint"  title="提示" data-container="body" data-toggle="popover" data-placement="top" data-content="Taker费率不能超过1%"></div>
                        <?= $form->field($model, 'taker_fee')->textInput(['placeholder' => '百分比计算'])->label() ?>
                        <div  id="maker_fee" class="formula-hint"  title="提示" data-container="body" data-toggle="popover" data-placement="top" data-content="Maker费率不能超过1%"></div>
                        <?= $form->field($model, 'maker_fee')->textInput(['placeholder' => '百分比计算'])->label() ?>

                        <div  id="小数点位数" class="formula-hint"  title="提示" data-container="body" data-toggle="popover" data-placement="top" data-content="小数点位数"></div>
                        <?= $form->field($model, 'decimals')->textInput(['placeholder' => '小数点位数'])->label() ?>

                        <?= $form->field($model, 'enable')->radioList([0 => '禁用' , 1 => '启用']) ?>

                        <div class="hr-line-dashed"></div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 text-center">
                            <button class="btn btn-primary" type="submit">保存内容</button>
                            <span class="btn btn-white" onclick="history.go(-1)">返回</span>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(function () {
        var query = window.location.href.substring(window.location.href.lastIndexOf("?")+1);

       if(query.indexOf("id=") != -1){
            stock = $("#exchangecoins-stock").val();
            money = $("#exchangecoins-money").val();

            var html = '<div class="form-group field-exchangecoins-coins required">\n' +
                            '<label class="control-label" for="exchangecoins-limit_amount">交易对</label>\n' +
                            '<input type="text" id="exchangecoins-coins" class="form-control" name="ExchangeCoins[coins]" value="' + stock + '/' + money + '" placeholder="" aria-required="true" readonly="true">\n' +
                            '\n' +
                            '<div class="help-block"></div>\n' +
                        '</div>'
           $(".col-sm-12").eq(1).prepend(html)
            $("#stock").remove()
            $(".field-exchangecoins-stock_coin_id").remove()
            $(".field-exchangecoins-stock").remove()
            $(".field-exchangecoins-money_coin_id").remove()
            $(".field-exchangecoins-money").remove()

       }

        console.log($("#limit_amount"))

        $("#exchangecoins-stock_coin_id").change(function () {
            var money = $("#exchangecoins-money_coin_id").find("option:selected").text();
            var stock = $(this).children('option:selected').html();

            checkCoins(stock,money);

            if( money == stock){
                $("#stock").popover("show");
                var obj =$("#stock")
                // 隐藏弹框
                popHide(obj)
                $("#exchangecoins-stock").val('')
            }else{
                $("#exchangecoins-stock").val($(this).children('option:selected').html())
            }


        })
        $("#exchangecoins-money_coin_id").change(function () {

            var stock = $("#exchangecoins-stock_coin_id").find("option:selected").text();
            var money = $(this).children('option:selected').html();

            checkCoins(stock,money);

            if( money == stock){
                $("#stock").popover("show");
                var obj =$("#stock")
                // 隐藏弹框
                popHide(obj)
                $("#exchangecoins-money").val('')
            }else{
                $("#exchangecoins-money").val($(this).children('option:selected').html())
            }

        })
        $("#exchangecoins-limit_amount").blur(function () {
            if($("#exchangecoins-limit_amount").val() < 0.0001 && $("#exchangecoins-limit_amount").val() != ''){
                $("#limit_amount").popover("show")
                var obj =$("#limit_amount")
                // 隐藏弹框
                popHide(obj)

                $("#exchangecoins-limit_amount").val('')
            }
        })
        $("#exchangecoins-maker_fee").blur(function () {
            if($("#exchangecoins-maker_fee").val() >= 1){
                $("#maker_fee").popover("show")
                var obj =$("#maker_fee")
                // 隐藏弹框
                popHide(obj)
                $("#exchangecoins-maker_fee").val('')
            }
        })
        $("#exchangecoins-taker_fee").blur(function () {
            if($("#exchangecoins-taker_fee").val() >= 1){
                $("#taker_fee").popover("show")
                var obj = $("#taker_fee")
                // 隐藏弹框
                popHide(obj)

                $("#exchangecoins-taker_fee").val('')
            }
        })

        function popHide(obj){
            if(obj.attr('aria-describedby') != null){
                var time = setTimeout(function () {
                    $(".formula-hint").popover("hide");
                },1000);
            }
        }

        function checkCoins(stock,money){
            if(stock != '' && money != ''){
                $.ajax({
                    url  : 'check-conis',
                    type : 'POST',
                    data : {stock : stock,money:money},
                    success : function (result) {
                        result = $.parseJSON(result)
                        if(result.code == 200){
                            $("#stock").attr('data-content','已有该交易对，请勿重复添加')
                            $("#stock").popover("show");
                            var obj =$("#stock")
                            // 隐藏弹框
                            popHide(obj)
                            $("#exchangecoins-stock").val('')
                            $("#exchangecoins-money").val('')
                        }
                    }
                })
            }
        }

    })
</script>