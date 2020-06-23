<?php
use yii\widgets\ActiveForm;


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
                    <h5>认购</h5>
                </div>
                <div class="ibox-content">
<form id="w0" action="/backend/member/member/coinlock_post" method="post">
                    <div class="col-sm-12">


<div class="form-group field-member-type">
<label class="control-label" for="member-type">用户id：<?php echo $user['id']; ?></label>
<input type="hidden" name="uid" value="<?php echo $user['id']; ?>">
<input type="hidden" name="coin_symbol" value="<?php echo $_GET['coin_symbol']; ?>">
<div class="help-block"></div>
</div>


<div class="form-group field-member-type">
<label class="control-label" for="member-type">用户名：<?php echo $user['username']; ?></label>
<div class="help-block"></div>
</div>

<div class="form-group field-member-type">
<label class="control-label" for="member-type">币种：<?php echo $_GET['coin_symbol']; ?></label>
<div class="help-block"></div>
</div>

<div class="form-group field-member-type">
<label class="control-label" for="member-type">余额：<?php echo $balance; ?></label>
<div class="help-block"></div>
</div>

<div class="form-group field-member-type">
<label class="control-label" for="member-type">认购数量：</label>
<input type="text" id="memberwealthpackage-name" class="form-control" name="amount" value="" aria-required="true" aria-invalid="false">
<div class="help-block"></div>
</div>

<div class="form-group field-member-coin_symbol">
<label class="control-label" for="member-coin_symbol">认购套餐</label>
<select id="member-coin_symbol" class="form-control" name="wealth_package_id" value="5">
<option value="0">请选择套餐</option>

<?php foreach($wealth_package as $key => $vo): ?>
        <option value="<?php echo $vo['id']; ?>"><?php echo $vo['name']; ?> - 周期<?php echo $vo['period']; ?>天 - 释放<?php echo $vo['day_profit']; ?>%</option>
<?php endforeach; ?>

</select>

<div class="help-block"></div>
</div>








                        <div class="hr-line-dashed"></div>


                    </div>






















                    <div class="form-group">
                        <div class="col-sm-12 text-center">
                            <button class="btn btn-primary" type="submit">保存内容</button>
                            <span class="btn btn-white" onclick="history.go(-1)">返回</span>
                        </div>
                    </div>
                    </form>
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