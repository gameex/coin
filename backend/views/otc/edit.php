<?php
use yii\widgets\ActiveForm;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '币种列表', 'url' => ['coins']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?= $this->title ?>OTC币种</h5>
                </div>
                <div class="ibox-content">
                    <?php 
                        $model->status = 1;
                        $form = ActiveForm::begin(); 
                    ?>
                    <?php $form = ActiveForm::begin(); ?>
                    <div class="col-sm-12">
                        <div  id="coin" class="formula-hint"  title="提示" data-container="body" data-toggle="popover" data-placement="top" data-content="该币种已添加"></div>
                        
                        <?= 
                            $model->isNewRecord ?
                                $form->field($model, 'coin_name')
                            ->dropDownList(common\models\Coins::find()
                                ->select(['symbol'])
                                ->indexBy('id')
                                ->column(),
                                [
                                    'value' => 5,
                                    'prompt' =>['text'=>'全部', 'options'=>['value'=>0]],
                                ]
                            )
                           :
                                 $form->field($model, 'coin_name')->textInput(['readonly' => true,'value'=>$model->coin_name])                                 
                            ;
                        ?>
                        <?= !$model->isNewRecord ? $form->field($model, 'coin_name')->hiddenInput(['value'=>$model->coin_id]) : '' ?>
                        <?= $form->field($model, 'max_register_num')->textInput() ?>
                        <?= $form->field($model, 'max_register_time')->textInput(['placeholder' => '以小时计算'])->label() ?>
                        <?= $form->field($model, 'limit_amount')->textInput(['placeholder' => '最小值为0.1000'])->label("最低发布数量") ?>
                        <?= $form->field($model,'status')->radioList([0 => '禁用', 1 => '启用'])?>
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
        $("#otccoinlist-coin_name").blur(function () {
            var coin_id = $(this).val()
            $.ajax({
                url : "check-coin",
                type : "POST",
                data : {id:coin_id},
                success: function (result) {
                    result = $.parseJSON(result)
                    if(result.code != 200){
                       $("#coin").popover('show')
                        var obj =$("#coin")
                        // 隐藏弹框
                        popHide(obj)
                        $("#otccoinlist-coin_name").val('')
                    }
                }
            })
        })
        function popHide(obj){
            if(obj.attr('aria-describedby') != null){
                var time = setTimeout(function () {
                    $("#coin").popover("hide");
                },1000);
            }
        }
    })
</script>
