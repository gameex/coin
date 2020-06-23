<style>
body{
	background: #F5F9FE;
}
</style>
<div id="wiki" class="wiki_class">
	<div class="wiki_main">
		<div class="wikie_title">
			<p class="shuziwiki">Coin Encyclopedias</p>
			<!--<div id="" class="search_bi">
				<input type="text" placeholder="搜索币种" id="searchBI" onkeyup="search(this.value);KeyDown(event)">
				<i class="search_icon" id="searchIcon" onclick="search_bi($(&#39;#searchBI&#39;).val());searchAll($(&#39;#searchBI&#39;).val())"></i>
				<ul class="searchList" id="searchList"></ul>
			</div>-->
			<div class="clear"></div>
		</div>
		<div class="currList">
			<ul class="currList_c clearfix" id="currList">
			</ul>
		</div>
	</div>
	<div class="clear"></div>
</div>

<script>
$(function() {
	http.post('start/cate',{id:7},function(res){
		$.each(res.data,function(index,r){
			$('#currList').append('<li>'+
			                  '<a href="'+r.url+'#1"> <img src="'+r.cover+'" alt="" class="icon_bi">'+
				              '<div class="introduce_c">'+
					          '<p class="biName">'+r.title+'</p>'+
					          '<p class="biTxt">'+r.title+'</p>'+
				              '</div>'+
			                  '</a>'+
		                      '</li>')
		})
	})
})
</script>