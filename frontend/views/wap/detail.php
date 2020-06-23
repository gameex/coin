<div id="wiki" class="wiki_class">
	<div class="wiki_main">
		<div class="wiki_left">

			<div class="bi_introduce">
				<h5><?php echo $content['title']; ?>介绍</h5>
				<div class="introduce_article">
				<?php echo $content['content']; ?>
				</div>
			</div>
		</div>
		<div class="wiki_right">
			<p class="more_currency"><span>其他数字货币</span><a href="/wiki">查看更多 </a></p>
			<div class="clear"></div>
			<ul class="moreList_c" id="moreList_c">
			
				</ul>
		</div>
	</div>
	<div class="clear"></div>
</div>
<script>
$(function() {
	http.post('start/wiki',{id:7},function(res){
		$.each(res.data,function(index,r){
			$('#moreList_c').append('<li>'+
			                  '<a href="'+r.url+'"> <img src="'+r.cover+'" alt="" c width="32" height="32">'+
				              '<div class="moreList_txt">'+
					          '<h5">'+r.title+'</h5>'+
					          '<div>'+r.title+'</div>'+
				              '</div>'+
			                  '</a>'+
		                      '</li>')
		})
	})
})
</script>