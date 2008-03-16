<?php get_header(); ?>
<style>
	a img{border:none;}
	.grey{font-size:small;
	color:grey;
	}
	
	.photobigbox{
	background:transparent url(<?php bloginfo("home");?>/wp-content/plugins/picasaweb/images/album.gif) no-repeat scroll left center;
	height:194px;
	text-align:center;
	width:194px;
	margin:0pt auto;
	}
	.photosmallbox{
	padding-top:16px;
	text-align:center;
	}
	.album,.photo{
	float:left;
	width:194px;
	}
	.album img, .photo img{
	border:2px solid #5C7FB9;
	width:160px;
	height:160px;
	}
	.center{text-align:center;}
	.albumtitle{height:45px;text-align:center;}
	.smallpicture{float:left;}
	.picturebox{width:164px; height:164px; padding:10px;text-align:center;}
	.picturebox img{border:2px solid #5C7FB9;}
	.pmenu{background:transparent url(<?php bloginfo("home");?>/wp-content/plugins/picasaweb/images/picasaweb.gif) no-repeat scroll right 0;}
	
	#photo-list-div, #show-photo-div, #next-photo-preview{
		clear:both;
	}
	#show-photo-div, #next-photo-preview {
		display:none;
		padding-bottom:15px;
	}
	#previous, #next {max-width:128px;height:96px;}
</style>
	<div id="content" class="narrowcolumn">
			<div id="picasaweb"><?php
				if (PICASA_USERNAME != "")			 
				include_once(ABSPATH . "/wp-content/plugins/picasaweb/picasa.inc.php");
				else 
					echo "picasaweb插件未激活，请到<a href='".get_bloginfo("url")."/wp-admin/options-general.php?page=picasaweb/picasa.admin.php'>后台</a>填写用户名";
			?></div>
	</div>

<div id="sidebar">
	<div id="picasainfo"><?php if (PICASA_USERNAME != "") albumhead();?></div>
</div>
<?php  if (PICASA_USERNAME != "") echo_picasa_script();?>
<?php get_footer(); ?>