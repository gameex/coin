<style>
	.sdmenu div a {
	    text-align: left;
	    padding-left: 40px;
	    white-space: nowrap;
	    text-overflow: ellipsis;
	    overflow: hidden;
	}
</style>
<div id="my_menu" class="sdmenu left">
	<div>
		<p><span data-i18n="u_a_1">用户中心</span></p>
		<a href="/uc/index" class="menu11" id="index">
			<span data-i18n="u_a_2">基本信息</span>
		</a>
		<a href="/uc/assets" class="menu" id="assets">
			<span data-i18n="u_b_1">我的资产</span>
		</a>
		<a href="/uc/cashlog" class="menu13" id="cashlog">
			<span data-i18n="u_c_1">充提历史</span>
		</a>
		<a href="/uc/message" class="menu10" id="message">
			<span data-i18n="u_g_32">系统消息</span>&nbsp;&nbsp;
		</a>
	</div>
	<div style="position:relative;">
		<p><span data-i18n="u_d_1">我的交易</span></p>
		<a href="/uc/entrusted" class="menu2" id="entrusted">
			<span data-i18n="u_d_2">委托管理</span>
		</a>
		<a href="/uc/clinch" class="menu3" id="clinch">
			<span data-i18n="u_e_1">我的成交</span>			
		</a>
	
	</div>
	<div>
		<p><span data-i18n="u_f_2">安全中心</span></p>
		<a href="/uc/password" class="menu8" id="password">
			<span data-i18n="u_f_3">登录密码</span>
		</a>
		<a href="/uc/verified" class="menu5" id="verified">
			<span data-i18n="u_f_8">实名认证</span>
		</a>
		<a href="/uc/api" class="menu5" id="verified">
			<span data-i18n="u_f_8">API 管理</span>
		</a>				
	</div>
	<div style="border:none">
		<p><span data-i18n="u_g_31">限时活动</span></p>

		<a href="/uc/invite-friends" class="menu10" id="message">
			<span data-i18n="u_g_32">邀请好友</span>&nbsp;&nbsp;
		</a>	
<!-- 		<a href="/uc/wealth" class="menu10" id="message">
			<span data-i18n="u_g_32">矿机理财</span>&nbsp;&nbsp;
		</a> -->
		<a style="display:none" href="/uc/wealthlock" class="menu10" id="message">
			<span data-i18n="u_g_32">锁仓管理</span>&nbsp;&nbsp;
		</a>
		
		<a href="/uc/passwd" class="menu10" id="message">
            <span data-i18n="u_g_32">申购</span>&nbsp;&nbsp;
        </a>
		
      	<!--<a href="/uc/mining" class="menu15" id="mining">
			<span data-i18n="u_h_2">交易挖矿</span>
		</a>-->		

      	<!--<a href="javascript:alert('暂未开放')" class="menu15" id="lock">
			<span data-i18n="u_h_2">锁仓奖励</span>
		</a>-->	

		<!--
      	<a href="/wakuang/mining.html" class="menu14" id="index">
			<span data-i18n="u_h_1">挖矿介绍</span>
		</a>
		-->				
	</div>
</div>
<script>
	var urls=(window.location.href).split("uc/")[1];
	if(urls){
		$("#"+urls).addClass("uc-current");
	}else{
		$("#index").addClass("uc-current");
	}
</script>