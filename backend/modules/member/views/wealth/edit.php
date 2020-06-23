<?php
use yii\widgets\ActiveForm;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '理财套餐', 'url' => ['index']];
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
                    <h5>添加套餐</h5>
                </div>
                <div class="ibox-content">
                    <?php $form = ActiveForm::begin(); ?>
                    <div class="col-sm-12">
                        <?= $form->field($model, 'type')->dropDownList([4=>'认购']) ?>

                        <?= $form->field($model, 'coin_symbol')
                            ->dropDownList(common\models\Coins::find()
                            ->select(['symbol'])
                            ->indexBy('symbol')
                            ->column(),
                            [
                                'value' => 5,
                                'prompt' =>['text'=>'请选择币种', 'options'=>['value'=>0]],
                            ]
                        ) ?>
                        <?= $form->field($model, 'name')->textInput() ?>
                        <?= $form->field($model, 'period')->textInput() ?>
                        <?= $form->field($model, 'day_profit')->textInput() ?>
                        <?= $form->field($model, 'min_num')->textInput() ?>
                        <?= $form->field($model, 'max_num')->textInput() ?>
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