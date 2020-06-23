<!DOCTYPE html>
<html>
<head>
	<title><?php echo $content['title']; ?></title>
	<meta name="keywords" content="{$seo_keywords}" />
	<meta name="description" content="{$seo_description}">
	<meta id="viewport" name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" href="/resource/backend/css/article.css">
</head>
<body ontouchstart="" style="padding:15px 10px;background-color: #fff;">
	<script>!function(t,e){var i=t.documentElement,n="orientationchange"in window?"orientationchange":"resize",d=navigator.userAgent.match(/iphone|ipad|ipod/i),a=function(){var e=i.clientWidth,n=i.clientHeight;e&&(e>=750?(e=750,t.body.style.width="750px"):t.body.style.width=e+"px",i.style.fontSize=100*(e/750)+"px",i.dataset.percent=100*(e/750),i.dataset.width=e,i.dataset.height=n)};a(),d&&t.documentElement.classList.add("iosx"+e.devicePixelRatio),t.addEventListener&&e.addEventListener(n,a,!1)}(document,window)</script>
	<div class="js-analysis js-delegate">
		<section class="g-article js-article" style="max-height: none;">
			<div class="js-article-inner">
				<!-- <h1 class="g-title">{$post_title}</h1> -->

				<article class="g-main-content js-main-content active">
					<main style="padding:0;">
						<?php echo $content['content']; ?>
					</main>
				</article>
			</div>
		</section>
</body>
</html>