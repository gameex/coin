<?php
use yii\widgets\ActiveForm;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '币种列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .field-coins-ram_token_addr{
        display: none;
    }
</style>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>添加币种</h5>
                </div>
                <div class="ibox-content">
                    <?php $form = ActiveForm::begin(); ?>
                    <div class="col-sm-12">
                        <?= $form->field($model, 'symbol')->textInput() ?>
                        <?= $form->field($model, 'coin_name')->textInput() ?>
                        <?= $form->field($model, 'icon')->widget('backend\widgets\webuploader\Image', [
                            'boxId' => 'icon',
                            'options' => [
                                'multiple'   => false,
                            ]
                        ])?>
                        <?= $form->field($model, 'unit')->textInput() ?>
                        <?= $form->field($model, 'ram_status')->radioList([1 => '是',0 => '否']) ?>
                        <?= $form->field($model, 'ram_token_addr')->textInput() ?>
                        <?= $form->field($model, 'ram_token_decimals')->textInput() ?>
                        <?= $form->field($model, 'limit_amount')->textInput(['placeholder' => '最小值为0.1000'])->label() ?>
                        <?= $form->field($model, 'withdraw_fee')->textInput()->label() ?>
                        <?= $form->field($model, 'sell_limit')->textInput() ?>
                        <?= $form->field($model, 'coin_text')->textarea(['rows' => 5 ]) ?>
                        <?= $form->field($model, 'recharge_enable')->radioList([1 => '开启',0 => '关闭']) ?>
                        <?= $form->field($model, 'withdraw_enable')->radioList([1 => '开启',0 => '关闭']) ?>
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
        var val = $("input[name='Coins[ram_status]']:checked").val()
        if(val == 1){
            $(".field-coins-ram_token_addr").css('display','block')
        }
        $("input[name='Coins[ram_status]']").click(function () {
            if($(this).val() == 0){
                $(".field-coins-ram_token_addr").css('display','none')
            }else{
                $(".field-coins-ram_token_addr").css('display','block')
            }
        })
    })


</script>