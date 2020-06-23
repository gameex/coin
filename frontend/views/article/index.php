<style>
#content{
	width:1140px;
	margin:0 auto;
	min-height: 800px;
	padding:20px 0 250px 0;
}
#content h1{
	text-align: center;
	margin: 20px 0 20px 0;
}
</style>

<div id="main">
  <div class="newsarea" style="padding-top:0;">
    <div class="newsarea_box">
      <div class="product left clearfix" id="product">
        <div>         
            <h2 style="padding: 40px 30px 20px;font-size: 26px;color: #333;line-height: 150%;"><?php echo $content['title']; ?></h2>
            <p style="padding-left:30px;color:#999;font-size: 14px;"><?php echo date("Y-m-d H:i:s",$content['append']); ?>	</p>
        </div>
        <div class="clear"></div>
        <div class="paragraph paragraph_news">
      	   <?php echo $content['content']; ?>		
     </div>
      </div>
     <!--上面为显示的文章-->
      <div class="right" style="background: rgb(245, 249, 254); height: 1504px;" id="Rights">
        <!--<div class="latest">-->
          <div class="investmentarea" id="investmentarea">
            <div class="focusnum1">
              	<h2 class="left">最新动态</h2>
              	<p class="right"><a href="/recent">查看更多</a></p>
              <div class="clear"></div>
            </div>
            
	            </div>
        <!--</div>-->
      </div>
      <div class="clear"></div>
    </div>
  </div>
</div>
<script>
	http.post('start/cate',{id:9,limit_begin:0,limit_num:6 },function(r){
			$.each(r.data, function(index,n) {
				var timer = new Date(n.append*1000);
				var year = timer.getFullYear();
				var month = timer.getMonth()+1;
				var nowDate = timer.getDate();
				var hours = timer.getHours();
				var min = timer.getMinutes();
				var second = timer.getSeconds();
				timer = year+'-'+month+'-'+nowDate;
				$('#investmentarea').append('<ul>'+ '<a href="'+n.url+'"><li>'+'<p style="font-size: 14px;color: #555;line-height: 26px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;">'+n.title+'</p>'+'<p class="recruit" style="margin-left: 0;">'+timer+'</p>'+ '<div class="clear"></div></li></a>'+'</ul>');
			});					
		})		
</script>				
				