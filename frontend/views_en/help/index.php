<div id="main">
	<div class="autobox">   
	    <div class="assets_center clear po_re">
	        <!--左侧菜单-->
	        <div class="coin_menu" id="lefts">
	        	<h5 style="position: relative;"><i class="helps"></i>Help sort</h5>
                <dl>
                    <div class="dl_menu" style="display:block;height: auto;" id="13" >
                            <dd>
                            	<em></em>
                            	<a href="/help/13" >Privacy policy</a>
                            </dd>  
	                    </div><div class="dl_menu" style="display:block;height: auto;" id="14" >
                            <dd>
                            	<em></em>
                            	<a href="/help/14" >Terms of use</a>
                            </dd>  
	                    </div><div class="dl_menu" style="display:block;height: auto;" id="15" >
                            <dd>
                            	<em></em>
                            	<a href="/help/15" >Anti-Money Laundering Ordinance</a>
                            </dd>  
	                    </div><div class="dl_menu" style="display:block;height: auto;" id="16" >
                            <dd>
                            	<em></em>
                            	<a href="/help/16" >Rate statement</a>
                            </dd>  
	                    </div><div class="dl_menu" style="display:block;height: auto;" id="17" >
                            <dd>
                            	<em></em>
                            	<a href="/help/17" >Application for currency</a>
                            </dd>  
	                    </div><div class="dl_menu" style="display:block;height: auto;" id="11" >
                            <dd>
                            	<em></em>
                            	<a href="/help/11" >No authentication code was received</a>
                            </dd>  
	                    </div><div class="dl_menu" style="display:block;height: auto;" id="19" >
                            <dd>
                            	<em></em>
                            	<a href="/help/19" >Beginning to Know Bitcoin</a>
                            </dd>  
	                    </div><div class="dl_menu" style="display:block;height: auto;" id="8" >
                            <dd>
                            	<em></em>
                            	<a href="/help/8" >How to Understand K-Line</a>
                            </dd>  
	                    </div> <div class="dl_menu" style="display:block;height: auto;" id="20" >
                            <dd>
                            	<em></em>
                            	<a href="/help/20" >Three Ways of Rising/Declining</a>
                            </dd>  
	                    </div>  <div class="dl_menu" style="display:block;height: auto;" id="21" >
                            <dd>
                            	<em></em>
                            	<a href="/help/21" >Reverse Hammer and Shooting Star</a>
                            </dd>  
	                    </div>                  </dl>
	        </div>
	        <!--右侧内容-->
	        <div class="assets_content w753 right bg_w">
	          	<h1><?php echo $content['title']; ?></h1>            
	            <div class="about_text">
				<?php echo $content['content']; ?>
		            </div>
	      	</div>
	      	<div class="clear"></div>
	    </div>
	</div>
</div>

<script>
	var urls=(window.location.href).split("help/")[1];
	if(urls){
		$("#"+urls).addClass("liActived");
	}else{
		$("#lefts div.dl_menu:first-of-type").find("dd:first-of-type").addClass("liActived")
	}
</script>