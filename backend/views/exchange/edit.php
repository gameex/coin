<?php
use yii\widgets\ActiveForm;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '交易对列表', 'url' => ['transaction-pair']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>添加交易对</h5>
                </div>
                <div class="ibox-content">
                    <div  class="formula-hint"  title="提示" data-container="body" data-toggle="popover" data-placement="top" data-content="请勿使用同样的币种">

                    </div>
                    <?php $form = ActiveForm::begin(); ?>
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
                        <?= $form->field($model, 'limit_amount')->textInput() ?>
                        <?= $form->field($model, 'taker_fee')->textInput() ?>
                        <?= $form->field($model, 'maker_fee')->textInput() ?>
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
        $("#exchangecoins-stock_coin_id").change(function () {
            var money = $("#exchangecoins-money_coin_id").find("option:selected").text();
            var stock = $(this).children('option:selected').html();
            if( money == stock){
                $(".formula-hint").popover("show");
                // 隐藏弹框
                popHide()
                $("#exchangecoins-stock").val('')
            }else{
                $("#exchangecoins-stock").val($(this).children('option:selected').html())
            }


        })
        $("#exchangecoins-money_coin_id").change(function () {
            var stock = $("#exchangecoins-stock_coin_id").find("option:selected").text();
            var money = $(this).children('option:selected').html();

            if( money == stock){
                $(".formula-hint").popover("show");
                // 隐藏弹框
                popHide()
                $("#exchangecoins-money").val('')
            }else{
                $("#exchangecoins-money").val($(this).children('option:selected').html())
            }

        })

        function popHide(){
            if($(".formula-hint").attr('aria-describedby') != null){
                var time = setTimeout(function () {
                    $(".formula-hint").popover("hide");
                },2500);
            }
        }

    })
</script>