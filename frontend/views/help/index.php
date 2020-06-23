<div id="main">
	<div class="autobox">   
	    <div class="assets_center clear po_re">
	        <!--左侧菜单-->
	        <div class="coin_menu" id="lefts">
	        	<h5 style="position: relative;"><i class="helps"></i>帮助分类</h5>
                     <?php foreach ($all_title as $key => $value) { ?>
                            <div class="dl_menu" style="display:block;height: auto;" id="<?= $value['id']?>" >
                                   <dd>
                                          <em></em>
                                          <a href="/help/<?= $value['id']?>" ><?= $value['title']?></a>
                                   </dd>  
                            </div>
                     <?php } ?>
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