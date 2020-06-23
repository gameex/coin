<link href="/resource/frontend/css/uc.css" rel="stylesheet">  
<div id="main">
    <div class="main_box">
	<?php echo $this->render('left.php'); ?>
        <div class="raise right bg_w clearfix" id="changepwd">
            <div class="message" style="padding-bottom:65px;">
                <p class="messagetab"><a href="javascript:void(0)" class="current" data-i18n="u_g_32">系统消息</a>
                	<!-- <span class="allRead" onclick="allRead()" data-i18n="u_g_33">全部标记已读</span> -->
                </p>
                <div id="finlist"></div>
			</div>
			<div class="allpage" id="Page" style="display: none;">
				<div class="pagecnt">
					<font data-i18n="c_20">共</font>&nbsp;<span id="allcount">0</span>&nbsp;<font data-i18n="c_21">条记录</font>，<font data-i18n="c_20">共</font>&nbsp;<span id="allPage">0</span>&nbsp;<font data-i18n="c_22">页</font>，<font data-i18n="c_23">当前是</font>&nbsp;<span id="pages"> 1 </span>&nbsp;<font data-i18n="c_24">页</font>
				</div>
				<div class="jumpbtn">							
					<button id="firstPage" data-i18n="c_25">首页</button>
					<button id="prePage" data-i18n="c_26">上一页</button>
					<button id="nextPage" data-i18n="c_27">下一页</button>
					<button id="lastPage" data-i18n="c_28">末页</button>
					<div class="jumpchoice">
						<font data-i18n="c_29">跳转至</font><input type="text" id="choicePage" value=""><font data-i18n="c_24">页</font>&nbsp;<button id="jumpPage" data-i18n="c_30">跳转</button>
					</div>
				</div>
			</div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>

<script>
	$(document).ready(function() {
		var page=1;
		var allPage=$("#allPage").html();
		var allcount = '';
		init(page)
		function init(page){
			http.post('user/message-list', {
				type:1,
				limit_begin:(page-1)*10,
				limit_num:page*10
			}, function(res) {
				if(res.code == 200){
					allcount = res.count;
					allPage = Math.ceil(res.count/10);
					if(allcount>10){
						$("#Page").show();
					}else{
						$("#Page").hide();
					};
					$("#allcount").html(allcount);
					$("#allPage").html(allPage);
					$("#pages").html(page);
					page=page;
					finDOM(res.data,1);
				}
			});
		}
		function timestampToTime(timestamp) {
	        var date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
	        var Y = date.getFullYear() + '-';
	        var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
	        var D = date.getDate() + ' ';
	        var h = date.getHours() + ':';
	        var m = date.getMinutes() + ':';
	        var s = date.getSeconds();
	        return Y+M+D+h+m+s;
	    }
		function finDOM(list,pages){
			$("#finlist").html('');
			var temp="";
			list.forEach(function (key,i){
				// var time = timestampToTime(key.add_time)
				if(key.is_show == 2){
					return false;
				}
				temp +="<div class='mynews news01'>";
				temp +="	<div class='andfrom yourattention'>";
				temp +="		<p class='left news_name' style='overflow: hidden;text-overflow:ellipsis;white-space: nowrap;color:#333;'>";
				if(key.title){
					temp +="		<a href='#' style='text-decoration: none;color:#368AE5;'>"+key.title+"";
				}else{
					temp +="		<a href='#' style='text-decoration: none;'>"+key.title+"";
				};
				
				temp +="			</a></p>";
				temp +="		<p class='right news_date'>"+key.add_time+"</p>";
				temp +="		<div class='clear'></div>";
				temp +="	</div>";
				temp +=" <div class='andfrom yourattention' style='margin-top:10px;'>";
				temp +="		<p class='left news_name' style='width:100%;'>";
					if(key.content){
						temp +="<a href='#' style='word-wrap:break-word;text-decoration: none;'>"+key.content+"</a>";
					}else{
						temp +="<a href='#' style='color:#009900;'></a>";
					};
				temp +="			</a></p>";
				temp +="</div>"
				temp +="</div>"
			});
			$("#finlist").append(temp);
		}
		$("#firstPage").on("click",function(){
			if(allPage == 1 || page==1){
				return false;
			}
			page=1;
			init(page);
		});
		$("#prePage").on("click",function(){
			page--;
			if(page<1){
				page=1;
				return false;
			}
			init(page);
		});
		$("#nextPage").on("click",function(){
			page++;
			if(page>allPage){
				page=allPage;
				return false;
			}
			init(page);
		});
		$("#lastPage").on("click",function(){
			if(allPage == 1){
				return false;
			}
			page = allPage
			init(page);
		});
		$("#jumpPage").on("click",function(){
			var jumpPages=$("#choicePage").val();
			if(jumpPages<1 || Number(jumpPages)>Number(allPage)){
				return false;
			}
			page = jumpPages
			init(page)
		});
	});
</script>