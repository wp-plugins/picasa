<?php 

define("PICASA_ADMIN", true); 
include_once(ABSPATH . "/wp-content/plugins/picasa/picasa.inc.php");


if (isset($_REQUEST["picasa_username"])){
	update_option("picasa_username", $_REQUEST["picasa_username"]);
	update_option("picasa_queryvar", $_REQUEST["picasa_queryvar"]);
	update_option("picasa_maxheight", $_REQUEST["picasa_maxheight"]);
	update_option("picasa_cache_path", $_REQUEST["picasa_cache_path"]);
	picasa_flush_rewrite_rules();
}
?>



<form method="post">	
	<fieldset class="options"><legend>Setup PicasaWeb</legend>
		<table width="100%" cellspacing="2" cellpadding="5" class="editform">
			<tr>
				<td width="150" align="right"><label for="username">Username</label></td>
				<td><input type="text" value="<?php echo get_option("picasa_username");?>" name="picasa_username"  onkeyup="var picasausername=document.getElementById('picasausername');picasausername.href='http://picasaweb.google.com/'+this.value;picasausername.innerHTML='http://picasaweb.google.com/'+this.value;" tabindex="11"/></td>		
				<td>Your PicasaWeb URL is <a id="picasausername" href="http://picasaweb.google.com/<?php echo get_option("picasa_username");?>">http://picasaweb.google.com/<?php echo get_option("picasa_username");?></a>?</td>		
			</tr>
			<tr>
				<td align="right"><label for="picasaheight">Height of Photos</label></td>
				<td><input type="text" value="<?php echo get_option("picasa_maxheight");?>" name="picasa_maxheight" tabindex="12" /></td>				
				<td>Height of the photos displayed in. Adjust it to fit into your template.</td>
			</tr>
			<tr>
				<td align="right"><label for="username">URL</label></td>
				<td><input type="text" value="<?php echo get_option("picasa_queryvar");?>" name="picasa_queryvar" onkeyup="var picasaurl=document.getElementById('picasaurl');picasaurl.href='<?php echo bloginfo("url")."/";?>'+this.value+'.html';picasaurl.innerHTML='<?php echo bloginfo("url")."/";?>'+this.value+'.html';" tabindex="13"/></td>				
				<td>You can browse your photos at <a id="picasaurl" href="<?php echo bloginfo("url")."/".get_option("picasa_queryvar").".html";?>"><?php echo bloginfo("url")."/".get_option("picasa_queryvar").".html";?></a></td>
			</tr>
			<tr>
				<td align="right"><label for="username">Cache Diretory</label></td>
				<td><input type="text" value="<?php echo get_option("picasa_cache_path");?>" name="picasa_cache_path"   tabindex="14" onkeyup="var picasacachpath=document.getElementById('picasacachpath');picasacachpath.innerHTML='<?php echo substr(ABSPATH,0,-1);?>'+this.value;"/></td>				
				<td>Your full path of Cache Directory: <span id='picasacachpath'><?php echo substr(ABSPATH, 0, -1) . get_option("picasa_cache_path");?></span>, make sure it is writable. </td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td><input type="submit" name="submit" value="Update" tabindex="15"/></td>
			</tr>
		</table>
	</fieldset>
</form>