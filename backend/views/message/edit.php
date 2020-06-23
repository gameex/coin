<?php
use yii\widgets\ActiveForm;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '消息列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>添加消息</h5>
                </div>
                <div class="ibox-content">
                    <?php $form = ActiveForm::begin(); ?>
                    <div class="col-sm-12">
                        <?= $form->field($model, 'title')->textInput() ?>
                        <?= $form->field($model, 'content')->textInput() ?>
                        <?= $form->field($model, 'type')->dropDownList([1 => '单个用户消息']) ?>
                        <!-- <?= $form->field($model, 'uid')->dropDownList($model->type == 1 ? \common\models\Message::getUsers() : [0 => '所有用户']) ?> -->
                        <?= $form->field($model, 'uid')->textInput(['value'=>0]) ?>
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
    $("#message-type").change(function(){
        var val = $(this).val();
        $.ajax({
            url:"get-users",
            type:"POST",
            data:{type:val},
            success : function(result) {
                result = $.parseJSON(result);
                var uidObj = $("#message-uid")
                uidObj.find("option:selected").text("");
                uidObj.empty();
                for(var i in result){
                    uidObj.append("<option value='"+i+"'>"+result[i]+'('+i+')'+"</option>");
                }
            }
        });
    });
</script>
