<?php 

define("PICASA_ADMIN", true); 
include_once(ABSPATH . "/wp-content/plugins/picasaweb/picasa.inc.php");


if (isset($_REQUEST["picasa_username"])){
	update_option("picasa_username", $_REQUEST["picasa_username"]);
	update_option("picasa_queryvar", $_REQUEST["picasa_queryvar"]);
	update_option("picasa_maxheight", $_REQUEST["picasa_maxheight"]);
	update_option("picasa_cache_path", $_REQUEST["picasa_cache_path"]);
	picasa_flush_rewrite_rules();
}
?>



<form method="post">	
	<fieldset class="options"><legend>What to show ? </legend>
		<table width="100%" cellspacing="2" cellpadding="5" class="editform">
			<tr>
				<td align="right"><label for="username">用户名</label></td>
				<td><input type="text" value="<?php echo get_option("picasa_username");?>" name="picasa_username"  onkeyup="var picasausername=document.getElementById('picasausername');picasausername.href='http://picasaweb.google.com/'+this.value;picasausername.innerHTML='http://picasaweb.google.com/'+this.value;"/></td>		
				<td>你的picasaweb地址是<a id="picasausername" href="http://picasaweb.google.com/<?php echo get_option("picasa_username");?>">http://picasaweb.google.com/<?php echo get_option("picasa_username");?></a>?</td>		
			</tr>
			<tr>
				<td align="right"><label for="picasaheight">图片高度</label></td>
				<td><input type="text" value="<?php echo get_option("picasa_maxheight");?>" name="picasa_maxheight" /></td>				
				<td>图片的高度，调整它以适应你的模板</td>
			</tr>
			<tr>
				<td align="right"><label for="username">地址</label></td>
				<td><input type="text" value="<?php echo get_option("picasa_queryvar");?>" name="picasa_queryvar" onkeyup="var picasaurl=document.getElementById('picasaurl');picasaurl.href='<?php echo bloginfo("url")."/";?>'+this.value+'.html';picasaurl.innerHTML='<?php echo bloginfo("url")."/";?>'+this.value+'.html';"/></td>				
				<td>浏览相册的地址<a id="picasaurl" href="<?php echo bloginfo("url")."/".PICASA_QUERYVAR.".html";?>"><?php echo bloginfo("url")."/".PICASA_QUERYVAR.".html";?></a></td>
			</tr>
			<tr>
				<td align="right"><label for="username">缓存目录</label></td>
				<td><input type="text" value="<?php echo get_option("picasa_cache_path");?>" name="picasa_cache_path"  onkeyup="var picasacachpath=document.getElementById('picasacachpath');picasacachpath.innerHTML='<?php echo substr(ABSPATH,0,-1);?>'+this.value;"/></td>				
				<td>缓存文件的地址: <span id='picasacachpath'><?php echo substr(ABSPATH, 0, -1) . get_option("picasa_cache_path");?></span>保持此目录可写 </td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td><input type="submit" name="submit" value="更新选项"/>
			</tr>
		</table>
	</fieldset>
</form>