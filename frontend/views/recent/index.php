<style>
body{
	background: #F5F9FE;
}
.allpage {
    width: 100%;
    text-align: center;
    margin-top: 50px;
}
}
.pagecnt {
    float: left;
    font-size: 14px;
    color: #555;
    width: 42%;
    text-align: right;
}
</style>

<div id="main" style="min-height: 689px;padding-bottom: 150px;">
	<div class="main_box">
		<div class="dynamic">
			<h3>最新动态</h3>
			<ul id="newList">
			</ul>
			<div class="allpage">
			<div class="pagecnt">
				<font data-i18n="c_20">共</font>&nbsp;<span id="allcount"></span>&nbsp;<font data-i18n="c_21">条记录</font>，<font data-i18n="c_20">共</font>&nbsp;<span id="allPage"></span>&nbsp;<font data-i18n="c_22">页</font>，<font data-i18n="c_23">当前是</font>&nbsp;<span id="pages"></span>&nbsp;<font data-i18n="c_24">页</font>
			</div>
			<div class="jumpbtn">						
				<button id="firstPage" data-i18n="c_25">首页</button>
				<button id="upPage" data-i18n="c_26">上一页</button>
				<button id='nextPage'  data-i18n="c_27">下一页</button>
				<button id='endPage'  data-i18n="c_28">末页</button>
				<div class="jumpchoice">
					<font data-i18n="c_29">跳转至</font><input id="choicePage" value="" type="text"><font data-i18n="c_24">页</font>&nbsp;<button class="jumpPage" data-i18n="c_30">跳转</button>
				</div>
			</div>
		</div>
		</div>
	</div>
	
</div>

<script>
var page=1;
var allpage=$("#allPage").html();
function firstPage(){
	if(allpage == 1 || page == 1){
		return false;
	}
	page=1;
	newpblice(page,function (data){
		finDOM(data.list)
		$("#pages").html(page)
	})
}

function prePage(){
	page--;
	if(page< 1){
		page=1;
		return false;
	}
	newpblice(page,function (data){
		finDOM(data.list)
		$("#pages").html(page)
	})
}
function nextPage(){
	page++;
	if(page>allpage){
		page=allpage;
		return false;
	}
	newpblice(page,function (data){
		finDOM(data.list);
		$("#pages").html(page)
	})
}
function lastPage(){
	if(allpage == 1){
		return false;
	}
	newpblice(allpage,function (data){
		finDOM(data.list)
		$("#pages").html(allpage);
		page=allpage;
	})
}
function jumpPage(){
	var jumppages=$("#choicePage").val();
	if(jumppages<1 || Number(jumppages)>Number(allpage)){
		return false;
	}
	newpblice(jumppages,function (data){
		finDOM(data.list);
		$("#pages").html(jumppages);
		page=jumppages;
	})
}

function finDOM(list){
	$("#newList").html('');
	var temp="";
	list.forEach(function (key,i){
		temp +="<li>";
		temp +="	<a href='/notice/"+key.article_id+"'><span>"+key.add_time+"</span>"+key.title+"</a>";
		temp +="</li>"
	});
	$("#newList").append(temp)
}

$(function() {
	var count = 0 
	function actile(type,beginNum,endNum,IndexPage){
		http.post('start/cate',{id:type,limit_begin:beginNum,limit_num:endNum},function(r){
			console.log(r);
			$('#allcount').html(r.count);
			count = r.count;
			$('#allPage').html(Math.ceil(r.count/10));
			if(count > 10){
				$(".allpage").show();
			}else{
				$(".allpage").hide();
			};
			$('#pages').html(IndexPage);
			$('#newList').empty();
			$.each(r.data, function(index,n) {
				var timer = new Date(n.append*1000);
				var year = timer.getFullYear();
				var month = timer.getMonth()+1;
				var nowDate = timer.getDate();
				var hours = timer.getHours();
				var min = timer.getMinutes();
				var second = timer.getSeconds();
				timer = year+'-'+month+'-'+nowDate+'&nbsp;'+hours+':'+min;
				$('#newList').append('<li>'+ '<a href="'+n.url+'#0">'+'<p>'+n.title+'</p>'+'<span>'+timer+'</span>'+ '</a>'+'</li>');
			});
		})
	}
	var ha =  window.location.hash;
	var type = 9;
	var IndexPage = 1;
	var begN = 0;
	var endN = 10;
	if(ha)type = ha.split('#')[1];
	actile(type,begN,endN,IndexPage);
	$('#nextPage').click(function(){//下一页
		if((begN*IndexPage)<=count){
			IndexPage++;
			begN +=10;
			actile(type,begN,endN,IndexPage);
		}else{
			http.info('没有查询到数据')
		}
		$('#choicePage').val(' ')
	})
	$("#upPage").click(function(){//上一页
		if(begN>=10){
			IndexPage--;
			begN -=10;
			//endN -=10;
			actile(type,begN,endN,IndexPage);
		}
		$('#choicePage').val(' ')
	})
	$('#firstPage').click(function(){//首页
			IndexPage = 1
			begN =0;
			endN =10;
			actile(type,begN,endN,IndexPage);
			$('#choicePage').val(' ')
	})	
	$('#endPage').click(function(){//末页
		var s = Math.floor(count/10);
		var left = count%10;
		if(left>0){
			begN  = s*10;
			//endN  = (s+1)*10;
			IndexPage = s+1;
		}else{
			begN = (s-1)*10;
			//endN = s*10;
			IndexPage = s
		}
		actile(type,begN,endN,IndexPage);
		$('#choicePage').val(' ')
	})
	$('#choicePage').keyup(function(){
		//console.log($(this).val());
		IndexPage = $(this).val() - 0
	})
	$('.jumpPage').click(function(){//跳转
		//var x = Math.floor(count/10);
		begN = (IndexPage-1)*10;
		if((begN)<=count){
			actile(type,begN,endN,IndexPage);
		}else{
			http.info('无数据')
		}
		
	})
	
})
</script>