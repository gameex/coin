<link href="/resource/frontend/css/uc.css" rel="stylesheet">  
<style type="text/css">
.referer-lead table tbody tr:nth-child(1) td:nth-child(1),.referer-lead table tbody tr:nth-child(2) td:nth-child(1),.referer-lead table tbody tr:nth-child(3) td:nth-child(1) {
    background: none;
    color: #353535;
}
th.header,.referer-lead table tbody tr td ,.referer-lead h3{
    text-align: left;
}
</style>
<div id="main">
    <div class="main_box">
        <?php echo $this->render('left.php'); ?>
       
        <div class="raise right bg_w clearfix" >
        	
        	<div style="border-bottom:3px solid #f5f9ff;padding:20px 20px;">
	 	<?php 
	 	  $article = (new \yii\db\Query())->from('jl_sys_article')->where(['id' => 62])->one();
	 	  $article = $article['content'];
	 	?>
	 	<?= $article ?>
	 </div>
        	
           <div class="tagContent selectTag" id="tagContent0">
            		<div><h3 style="padding:20px 20px;">兑换专区</h3></div>
            		
            	
            		<div style="padding:20px 20px;"><span>可用 <text class="money"><?= sprintf("%.2f",$btc['balance'])  ?></text> <text class="coin_name">BTC</text> = <text class="pacanum">0</text>&nbsp;PAPC</span></div> 
            		 <div style="padding:20px 20px;position:relative;width:470px;">
	            		<select id="coin" name="coin" style="width:150px;height:30px;">
	            			<option class="fir" value="1" selected="true">BTC</option>
	            			<option value="2">USDT</option>
	            		</select>
	            		
	            		<input type="hidden" class="hidenum" value="<?= sprintf("%.2f",$btc['balance'])  ?>" >
	            		
	            		<input class="coin_num" type="text" placeholder="数量"  style="border:1px solid black;height:28px;margin-bottom:3px;width:250px;">
	            		<input class="all" type="button" value="全部" style="padding:7px 10px;position:absolute;right:86px;background:#008ee6"  >
            		</div> 
            		 <button class="submit-btn" style="margin-left:187px;margin-top:40px;margin-bottom:20px;width:240px;background:#008ee6;border:none;padding:10px 0">立即兑换</button>
           </div>
	 
          <span  style="position:absolute;right:70px;bottom:190px;font-size:20px;">当前换率&nbsp;<text class="cur_rate">0</text></span>

        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>

<script type="text/javascript" src="/resource/frontend/js/clipboard.min.js"></script>

<script type="text/javascript" src="/resource/frontend/js/http.js"></script>
<script type="text/javascript" src="/resource/frontend/js/index_cn.js"></script>


<script type="text/javascript">


     	$(function(){
       
       
         coin_num = <?= sprintf("%.2f",$btc['balance'])  ?>;
     $('.all').click(function(){
  	 var options = $("#coin option:selected");　　　　//获取选中项
     var coin = options.val();
     if(coin == 1){
     	coin_num = <?= sprintf("%.2f",$btc['balance']) ?>;
     	
     	
     }else{
     	coin_num = <?= sprintf("%.2f",$usdt['balance'])?>;
     }
     $(".coin_num").val(coin_num)
  });
   $(".coin_num").bind('input propertychange', function () {
           var options = $("#coin option:selected");　　　　//获取选中项
           var coin = options.val(); 
           if($(this).val()>0){
           	 coin_num = $(this).val();
           	 
           }
 });
 
 $('#coin').change(function(){

      var coin = $(this).val()
 	      usdt_rate = 1/http.rate2;  //1usdt = 多少paca
          rate_ = http.rate1;
          btc_rate = usdt_rate*rate_;
 	if(coin == 1){
 	     coin_num = <?= sprintf("%.2f",$btc['balance']) ?>;
         rate = btc_rate;
         $('.coin_name').text('BTC');
         clearInterval(time2)
 	 	
 	 	
 	 	time1 = setInterval(function(){
  	   var options = $("#coin option:selected");　　　　//获取选中项
       var coin = options.val();
       $('.money').text(coin_num)
        var sr = http.rate1*(1/http.rate2);
         rate = sr;
       $('.pacanum').text((coin_num*sr).toFixed(2))
       $('.cur_rate').text(sr.toFixed(2)) 
   },1000)
         
 	}else{
     	coin_num = <?= sprintf("%.2f",$usdt['balance'])?>;
 	 	rate =  usdt_rate;
 	 	 $('.coin_name').text('USDT');
 	 	clearInterval(time1)
 	 	
 	 	
 	 	time2 = setInterval(function(){
  	   var options = $("#coin option:selected");　　　　//获取选中项
       var coin = options.val();
       $('.money').text(coin_num)
        var sr2 = (1/http.rate2);
        rate = sr2;
       $('.pacanum').text((coin_num*sr2).toFixed(2))
       $('.cur_rate').text(sr2.toFixed(2)) 
   },1000)
 	}
 });

  
   time1 = setInterval(function(){
  	   var options = $("#coin option:selected");　　　　//获取选中项
       var coin = options.val();
       $('.money').text(coin_num)
        var sr = http.rate1*(1/http.rate2);
        rate = sr;
       $('.pacanum').text((coin_num*sr).toFixed(2))
       $('.cur_rate').text(sr.toFixed(2)) 
   },1000)
 $('.submit-btn').click(function(){
 	 var options = $("#coin option:selected");　　　　//获取选中项
     var coin = options.text();
    
 	$.post('/uc/do-purse',{
 		coin:coin,
 		rate:rate,
 		coin_num:coin_num
 	},function(res){
 		res_ = JSON.parse(res);
 	
 		if(res_.error>0){
 			alert(res_.message)
 		}else{
 			alert('兑换成功');
 			location.reload();
 		}
 	})
 })
     	})

    
          
  
    

</script>