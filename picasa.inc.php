<?php 
### Require WordPress Header

global $albumid, $albumsfeed, $photosfeed, $max_height, $np, $smallsize, $bigsize, $username, $my_numpics, $cache_directory, $snoopy_file,$picasa_uri;


// user defined
$username 	= PICASA_USERNAME;		
$bigsize	= "?imgmax=800";	// large image size
$smallsize	= "?imgmax=160";	// small image size
$max_height = 600;				// max image height
$cache_directory	= ABSPATH . PICASA_CACHE_PATH ;			// cache directory
$snoopy_file	= ABSPATH . "/wp-includes/class-snoopy.php";

$picasa_uri		= get_bloginfo("home"). '/' . PICASA_QUERYVAR;

// input 
$albumid	= get_query_var(PICASA_QUERYVAR) ? get_query_var(PICASA_QUERYVAR) : 0;
if ( isset($_REQUEST['albumid'])) $albumid = $_REQUEST['albumid'];
$np			= 0;


if (isset($_REQUEST['json'])) {
	get_json_content(true);
	exit();
} elseif (isset($_REQUEST['update'])) {
	generate_cache();
	
	exit();
}
?>
<script type="text/javascript">
var picasa_uri = "<?php echo $picasa_uri; ?>";
</script>

<?php
function get_cache_date() {
	global $albumid, $cache_directory;
	
	$rss_uri = get_rss_uri();
	$rss_hash = md5($rss_uri).".xml";
	$cache_rss_path = $cache_directory.$rss_hash;
	if (is_file($cache_rss_path))
		return date ("Y-m-d", filemtime($cache_rss_path));
	else 
		return "0000-00-00";
}

function generate_cache() {
	global $albumid, $albumsfeed, $photosfeed;
	// ignore_user_abort();
	
	if ($albumid) {
		$photosfeed = get_rss_content(true);
		get_json_content(false, true);
		if (function_exists("cos_create_html")) {
			cos_create_html(get_bloginfo("home") . "/" . PICASA_QUERYVAR . "/" . $albumid . ".html",  get_bloginfo("home") . "/index.php?zqp=true&albumid=$albumid");
		}
		echo "{$photosfeed->feed->title->_t} is updated<br/>";
		exit();
	}
	
	$albumsfeed = get_rss_content(true);
	echo "{$albumsfeed->feed->title->_t} is updated<br/>";
	if (function_exists("cos_create_html")) {
		cos_create_html(get_bloginfo("home") . "/" . PICASA_QUERYVAR . ".html", get_bloginfo("home") . "/index.php?zqp=true");
	}
	for ($i = 0; $i < count($albumsfeed->feed->entry); $i++ ) {
		$album 		= $albumsfeed->feed->entry[$i];
        $id_begin 	= strpos($album->id->_t, 'albumid/') + 8;
        $id_end 	= strpos($album->id->_t, '?');
        $albumid 	= substr($album->id->_t, $id_begin, $id_end - $id_begin);
		$img_update = substr($album->updated->_t, 0, 10);
		$mod_date	= get_cache_date();
	
		// echo $mod_date."".$img_update."<br/>";
		if ($mod_date < $img_update) {
			get_rss_content(true);
			get_json_content(false, true);
			if (function_exists("cos_create_html")) {
				cos_create_html(get_bloginfo("home") . "/" . PICASA_QUERYVAR . "/" . $albumid . ".html", get_bloginfo("home") . "/index.php?zqp=true&albumid=$albumid");
			}	
			echo "{$album->title->_t} is updated<br/>";
		}
	} 	
}

function get_rss_uri() {
	global $albumid, $username;
	if ($albumid) return "http://picasaweb.google.com/data/feed/base/user/$username/albumid/$albumid?category=photo&alt=json&access=public";
	else return "http://picasaweb.google.com/data/feed/base/user/$username?category=album&alt=json&access=public";
}

function get_rss_content($force = false) {
	global $snoopy_file, $cache_directory;
	$rss_uri = get_rss_uri();
	$rss_hash = md5($rss_uri).".xml";
	$cache_rss_path = $cache_directory.$rss_hash;
	
	if ($force || !is_file($cache_rss_path) || filesize($cache_rss_path)==0 ) {
		require_once($snoopy_file);
     	$snoopy = new Snoopy;
		$snoopy->fetch($rss_uri);
        // this will copy the created tex-image to your cache-folder
        
		$rss_result = str_replace("$", "_", $snoopy->results);
		if (strlen($rss_result) < 100) exit();
		
		$cache_file = fopen($cache_rss_path, 'w');
		fputs($cache_file, $rss_result);
		fclose($cache_file);	
	} else {
		$rss_result = file_get_contents ($cache_rss_path);
	}
	
	$rss_result = json_decode($rss_result);
	return $rss_result;   
	
}

function album_list() {
	global $albumsfeed, $picasa_uri;
	$albumsfeed = get_rss_content();
	$totalalbum = count($albumsfeed->feed->entry);
	
	$nowalbum = isset($_REQUEST['h']) ? ($_REQUEST['h']) : 0;
    
    echo "
		<script type='text/javascript'>
			var totalalbum = $totalalbum;
			var nowalbum = 0, albumid = $nowalbum;
		</script>
		<h3 style='margin-left:3px' class='pmenu'>Home</h3>
			<div id='albumlist'>";
    
    for ($i = 0; $i < count($albumsfeed->feed->entry); $i++ ) {
		$album 		= $albumsfeed->feed->entry[$i];
		$img_base 	= $album->media_group->media_content[0]->url;
		
        $id_begin 	= strpos($album->id->_t, 'albumid/') + 8;
        $id_end 	= strpos($album->id->_t, '?');
        $id_base 	= substr($album->id->_t, $id_begin, $id_end - $id_begin);
        $img_update = substr($album->published->_t, 0, 10);

        echo "
				<div class='album'>
					<div class='photobigbox'>
						<div class='photosmallbox'>
							<a id='a$i' class='standard' href='{$picasa_uri}/$id_base.html'>
								<img id='i$id_base' src='$img_base?imgmax=160&crop=1' class='pwimages' />
							</a>
						</div>
					</div>
					<div class='albumtitle'>
						<a class='title' class='standard' href='$picasa_uri//$id_base.html'>{$albumsfeed->feed->entry[$i]->title->_t}</a>
						<div class='grey'>$img_update</div>
					</div>
				</div>
		";
    }
    
	echo "
			</div>";
}

function photo_list() {
	echo "<div id='photo-list-div'>";
	global $my_numpics, $photosfeed, $albumid, $picasa_uri;	    
    $photosfeed = get_rss_content();
    
    $my_numpics = $photosfeed->feed->openSearch_totalResults->_t;
    
    echo "
	<h3 style='margin-left:3px' class='pmenu'>
		<a class='standard' href='{$picasa_uri}.html#$albumid'>Home</a> &gt; {$photosfeed->feed->title->_t}&nbsp;[Total {$my_numpics}]
	</h3>
	<div id='photolist'>";
    
    for ($i = 0; $i < count($photosfeed->feed->entry); $i ++ ) {
        $img_base = $photosfeed->feed->entry[$i]->media_group->media_content[0]->url;
        $link_url = "?albumid=".$albumid;
        $width = (int)($photosfeed->feed->entry[$i]->media_group->media_content[0]->width);
        $height = (int)($photosfeed->feed->entry[$i]->media_group->media_content[0]->height);
        $margin = 0;
		$img_width = 160;
		$img_height = 160;
        if ($width > $height)  
			$img_height = floor(160*$height/$width);
		else
			$img_width 	= floor(160*$width/$height);	 
        $margin = floor(80 - $img_height/2);
        
		$j = $i + 1;
        echo "
		<div class='smallpicture'>
			<div class='picturebox'>
				<a href='#$j' onclick='show_photo($i);return false;'>
					<img id='p$i' style='margin:{$margin}px 0;width:{$img_width}px;height:{$img_height}px;' src='$img_base?imgmax=160' class='pwimages'/>
				</a>
			</div>
		</div>";    
    }
    
	echo "
	</div>";
	
	echo "</div>";
}

function show_photo() {
	echo "<div id='show-photo-div'>";
	global $my_numpics, $photosfeed, $np, $bigsize, $smallsize, $albumid, $max_height, $picasa_uri;
	
	$photosfeed = get_rss_content();
	$photofeed	= $photosfeed->feed->entry[$np];
	$albumname	= $photosfeed->feed->title->_t;
	$my_numpics = $photosfeed->feed->openSearch_totalResults->_t;

    $img_title 	= $photofeed->title->_t;
    $img_width 	= (int)$photofeed->media_group->media_content[0]->width;
    $img_height = (int)$photofeed->media_group->media_content[0]->height;
    $img_base 	= $photofeed->media_group->media_content[0]->url;
    
	$np1 = $np + 1;
    $current_index_text = "<span id='picindex'>$np1</span> of $my_numpics";
    
    echo "
	<h3 style='margin-left:3px' class='pmenu'>
		<a class='standard' href='{$picasa_uri}.html#$albumid'>Home</a> &gt; <a class='standard' href='?albumid=$albumid' onclick='return_photo_list();return false;'>$albumname</a> &gt; $current_index_text
	</h3>
    
    <table  style='margin:0 auto;width:99%'>
		<tr valign=top>
			<td width='20%' align='left'>
				<a href='?albumid=$albumid' onclick='return_photo_list();return false;'>«View Album</a>
			</td>
			<td width='30%' align='right'>
				<a onclick='navi(-1);return false;' href='javascript:;'  >			
					<img border=0 alt='Previous item' src='".get_bloginfo("home")."/wp-content/plugins/picasa/images/left.gif'>
				</a>
			</td>
    		<td> 
			</td>
			<td width='30%' align='left'> 
				<a onclick='navi(1);return false;' href='javascript:;'>
					<img border=0 alt='Next item' src='".get_bloginfo("home")."/wp-content/plugins/picasa/images/right.gif'>
				</a>
			</td>
			<td width='20%' align='right'>
				<a id='zoom' href='$img_base' target='_blank' title='original photo'>
					<img src='".get_bloginfo("home")."/wp-content/plugins/picasa/images/zoom_normal.gif' alt='view original'/>
				</a>
			</td>
		</tr>
	</table>";
    
    $display_height = get_option("picasa_maxheight");

    echo "
	<div id='picbox' style='text-align:center'>
		<h4 id='imgdes' style='margin-left:2px'>
			{$photofeed->media_group->media_description->_t}
		</h4>
		<div>
			<a border=0 href='javascript:;' onclick='navi();return false;' title='click to display next'>
				<img id='picture' height='{$display_height}px'   src='/images/zoom_normal.gif' class='pwimages' />
			</a>
		</div>
	</div>";
	
	echo "</div>";
}

if (!defined("PICASA_ADMIN")) {
	if ($albumid) {
		photo_list();
		show_photo();
	} else {
		album_list();
	}
}
	
	
function get_json_content($echo = true, $force = false) {
	global $photosfeed, $albumid, $cache_directory;
	
	$rss_uri = get_rss_uri();
	$album_md5	= md5($rss_uri).".js";
	if ($force || !is_file($cache_directory.$album_md5)) {
		$photosfeed = get_rss_content();
		
		$src = Array();
		$des = Array();
		$total = count($photosfeed->feed->entry);
		for ($i = 0; $i < $total; $i ++ ) {
			$src[$i] = $photosfeed->feed->entry[$i]->content->src;
			$des[$i] = $photosfeed->feed->entry[$i]->media_group->media_description->_t;
		}
		
		$json->total = $total;
		$json->src	 = $src;
		$json->des 	 = $des;

		$json_text	 = "var photolist=".json_encode($json);
		$cache_file = fopen($cache_directory.$album_md5, 'w');
		fputs($cache_file, $json_text);
		fclose($cache_file);
	} elseif ($echo) {
		$json_text = file_get_contents($cache_directory.$album_md5);
	}
	
	if ($echo)	echo $json_text;
}

function albumhead() {
	global $albumid, $albumsfeed, $photosfeed, $my_numpics, $np, $smallsize, $bigsize, $picasa_uri;
    if ($albumid != "") {
		$date = substr($photosfeed->feed->updated->_t, 0, 10);
        echo "
		<div class='photobigbox'>
			<div class='photosmallbox'>
				<img src='{$photosfeed->feed->icon->_t}' class='pwimages' />
			</div>
		</div>
		<h4>
			{$photosfeed->feed->title->_t}({$photosfeed->feed->openSearch_totalResults->_t})
		</h4>
		<span>
			{$photosfeed->feed->subtitle->_t}
		</span>
		<p class='grey' id='update'>
			Last <a href='".get_bloginfo("home")."/wp-content/plugins/picasa/picasaweb.cache.php?albumid=$albumid&update='  onclick='ajaxShowPost(\"".get_bloginfo("home") . "/wp-content/plugins/picasa/picasaweb.cache.php?albumid=$albumid&update=\", \"update\");return false;'>update</a> at {$date}
		</p>";
		
		echo "<div id='next-photo-preview'>";
		$np1 = $np + 1;
		if ($np1 > $my_numpics - 1) $np1 = 0;
		$t1 = $photosfeed->feed->entry[$np1];
		$img_link1 = $t1->content->src;
		echo "
		<div class='center'>
			<a href='javascript:navi(-1);' title='next/previous' class='center'>
				<img id='previous' src='".get_bloginfo("home")."/wp-content/plugins/picasa/images/left.gif'/>
			</a>
			<a href='javascript:navi(1);' title='previous/next' class='center'>
				<img id='next' src='".get_bloginfo("home")."/wp-content/plugins/picasa/images/right.gif'/>
			</a>
		</div>
		<script type='text/javascript'>
			var nextImgLink = '$img_link1';
		</script>
		";
		echo "</div>";
    } else {
		$date = substr($albumsfeed->feed->updated->_t, 0, 10);
        echo "
		<div>
			<img src='{$albumsfeed->feed->icon->_t}' class='pwimages' />
		</div>
		<h4>
			{$albumsfeed->feed->title->_t}({$albumsfeed->feed->openSearch_totalResults->_t})
		</h4>
		<span>
			{$albumsfeed->feed->subtitle->_t}
		</span>
		<p class='grey' id='update'>
Last <a href='".get_bloginfo("home")."/wp-content/plugins/picasa/picasaweb.cache.php?update=' onclick='ajaxShowPost(\"". get_bloginfo("home")."/wp-content/plugins/picasa/picasaweb.cache.php?albumid=$albumid&update=\", \"update\");return false;'>update</a> at {$date}
		</p>";
    }
	
	echo "<h4>Keyboard shortcut</h4>
		o, enter: enter choosed item (with red border);<br/>
	 	j, left arrow: previous item;<br/>
		k, right arrow: next item;<br/>
		u: return items list;<br/>Power by <a href='http://zhiqiang.org/blog/plugin/picasa'>PicasaWeb</a>";
}

function echo_picasa_script() {
	global $albumid, $albumsfeed, $photosfeed, $my_numpics, $np, $smallsize, $bigsize, $picasa_uri;
	if ($albumid):?>
		<script type="text/javascript" src="<?php bloginfo("home"); echo PICASA_CACHE_PATH;	$rss_uri = get_rss_uri(); echo md5($rss_uri).".js";?>"></script>
		
		<script type="text/javascript"> 
			if (typeof(photolist) == "undefined" || typeof(photolist.total) == "undefined") {
				document.write("<scri"+"pt type='text/jav"+"asc"+"ript' src='<?php bloginfo("home");?>/wp-content/plugins/picasa/picasaweb.cache.php?albumid=<?php echo $albumid;?>&json='></scri"+"pt>");
			}
		</script>
		
		<script type="text/javascript">
			
			var np = 0;
			var albumid = "<?php echo $albumid;?>";//<?php echo $_REQUEST['albumid'];?>;
			if (document.location.hash)	np = parseInt(document.location.hash.substr(1))-1;
			var smallsize	= "<?php echo $smallsize;?>";
			var bigsize		= "<?php echo $bigsize;?>";
			
			function $(a) { return document.getElementById(a);}
			var img = new Image();

			function preloadImg(img1) {
				img.src = img1;
			}

			//$("picture").src = $("picture").src.slice(0, -11) + bigsize;
			
			// if ($("picasaweb").clientWidth < 700) { $("picture").height=parseInt(($("picasaweb").clientWidth-40)*3/4); $("picture").style.maxWidth=$("picasaweb").clientWidth-40+"px";}
			add_border(np);
			// alert($("picasaweb"));
			add_border(np);
						
			function getImgLink(n) {
				if (n < 0)	n = photolist.total - 1;
				if (n > photolist.total - 1) n = 0;
				return photolist.src[n];
			}
			var nowj = 1;
			
			function offsetTop(e){
				var agt=navigator.userAgent.toLowerCase();
				var ie = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1) && (agt.indexOf("omniweb") == -1));
				var h=0;
				if (ie){
 					var offsetParent = e;
					while (offsetParent!=null && offsetParent!=document.body) {
 						h+=offsetParent.offsetTop;
 						offsetParent=offsetParent.offsetParent;
					}
					return h;
				} else {
					return e.offsetTop;
				}
			}
			function add_border(n) {
				$("p"+n).style.borderColor="red";
				if ($("photo-list-div").style.display == 'none') return;
				
				//$("p"+n).offsetParent = document.body;
				oT = offsetTop($("p"+n));
				
				//alert(oT);
				if (oT < document.documentElement.scrollTop)
					document.documentElement.scrollTop = oT - document.documentElement.clientHeight + 200 ;
				if (oT > document.documentElement.scrollTop + document.documentElement.clientHeight - 180)
					document.documentElement.scrollTop = oT - 100 ;
			}
			function delete_border(n) {
				$("p"+n).style.borderColor="#ccc";
			}
			function navi(num) {
				delete_border(np);
				if (typeof(num)=='undefined')	num = nowj;
				else {nowj = num;
				}
				np = np + num;
				
				if (np < 0)	np = photolist.total - 1;
				if (np > photolist.total - 1) np = 0;
				var img_link = photolist.src[np];
				window.location.hash = np + 1;
				
				var img_link1 = getImgLink(np+1);
				var img_link2 = getImgLink(np-1);
				$("picture").src = img_link + smallsize;
				$("zoom").href = img_link;
				$("picture").src = img_link + bigsize;
				$("imgdes").innerHTML = photolist.des[np];
				$("picindex").innerHTML = np + 1;
				if($("next"))$("next").src = img_link1 + smallsize;
				if($("previous"))$("previous").src = img_link2 + smallsize;
				if (nowj == -1) preloadImg(img_link1 + bigsize);
				else  preloadImg(img_link2 + bigsize);
				
				add_border(np);
			}

			preloadImg(nextImgLink + bigsize);
			
			function return_photo_list() {
				$("photo-list-div").style.display = "block";
				$("show-photo-div").style.display = "none";
				$("next-photo-preview").style.display = "none";
				add_border(np);
				// document.location.hash = '#';
			}
			
			function show_photo(num) {
				delete_border(np);
				window.location.hash = num + 1;
				$("photo-list-div").style.display = "none";
				$("show-photo-div").style.display = "block";
				$("next-photo-preview").style.display = "block";
				np = num;
				var img_link = photolist.src[np];
				
				var img_link1 = getImgLink(np+1);
				var img_link2 = getImgLink(np-1);
				$("picture").src = img_link + smallsize;
				$("zoom").href = img_link;
				$("picture").src = img_link + bigsize;
				$("imgdes").innerHTML = photolist.des[np];
				$("picindex").innerHTML = np + 1;
				if($("next"))$("next").src = img_link1 + smallsize;
				if($("previous"))$("previous").src = img_link2 + smallsize;
				if (nowj == -1) preloadImg(img_link1 + bigsize);
				else  preloadImg(img_link2 + bigsize);
				
				add_border(np);
			}
			
			function testKeyCode( evt, intKeyCode ) { 
				if ( window.createPopup ) 
					return evt.keyCode == intKeyCode; 
				else 
				return evt.which == intKeyCode; 
			} 
			document.onkeydown = function ( evt ) { 
				if ( evt == null ) evt = event; 
				if ( testKeyCode( evt, 37 ) ) { navi(-1); } 
				if ( testKeyCode( evt, 39 ) ) { navi(1);}
				if ( testKeyCode( evt, 74 ) ) { navi(-1); } 
				if ( testKeyCode( evt, 75 ) ) { navi(1);}
				if ( testKeyCode( evt, 85 ) ) { if ($("photo-list-div").style.display == "none") return_photo_list(); else document.location="<?php echo $picasa_uri;?>.html#" + albumid; } 
				if ( testKeyCode( evt, 13 ) ) { show_photo(np); } 
				if ( testKeyCode( evt, 79 ) ) { show_photo(np); } 
			} 
		</script>
		<script type="text/javascript">
			if (document.location.hash)	show_photo(parseInt(document.location.hash.substr(1))-1);
		</script>
<?php 
	else:
?>
<script type="text/javascript">
	function $(a) { return document.getElementById(a);}
	function offsetTop(e){
		var agt=navigator.userAgent.toLowerCase();
		var ie = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1) && (agt.indexOf("omniweb") == -1));
		var h=0;
		if (ie){
			var offsetParent = e;
			while (offsetParent!=null && offsetParent!=document.body) {
				h+=offsetParent.offsetTop;
				offsetParent=offsetParent.offsetParent;
			}
			return h;
		} else {
			return e.offsetTop;
		}
	}
	function add_border(n) {
		$("a"+n).getElementsByTagName('img')[0].style.borderColor="red";
		
		//$("p"+n).offsetParent = document.body;
		oT = offsetTop($("a"+n));
		
		//alert(oT);
		if (oT < document.documentElement.scrollTop + 200)
			document.documentElement.scrollTop = oT - document.documentElement.clientHeight + 200 ;
		if (oT > document.documentElement.scrollTop + document.documentElement.clientHeight - 180)
			document.documentElement.scrollTop = oT - 200 ;
	}
	function delete_border(n) {
		$("a"+n).getElementsByTagName('img')[0].style.borderColor="#ccc";
	}
	
	function navi(num) {
		delete_border(nowalbum);
		
		nowalbum += num;
		if (nowalbum < 0)	nowalbum = totalalbum - 1;
		else if (nowalbum >= totalalbum) nowalbum = 0;
		
		add_border(nowalbum);
	}
			
	function testKeyCode( evt, intKeyCode ) { 
		if ( window.createPopup ) 
			return evt.keyCode == intKeyCode; 
		else 
		return evt.which == intKeyCode; 
	} 
	document.onkeydown = function ( evt ) { 
		if ( evt == null ) evt = event; 
		if ( testKeyCode( evt, 37 ) ) { navi(-1); } 
		if ( testKeyCode( evt, 39 ) ) { navi(1);}
		if ( testKeyCode( evt, 74 ) ) { navi(-1); } 
		if ( testKeyCode( evt, 75 ) ) { navi(1);}
		if ( testKeyCode( evt, 13 ) || testKeyCode( evt, 79 )) { 
			document.location = $('a'+nowalbum).href;
		}
	} 
	
	if (document.location.hash) {
		var hash = document.location.hash.substr(1);
		var e = $("i"+hash);

		if (e) nowalbum = parseInt(e.parentNode.id.substr(1));
		else nowalbum = 0;
	} else {nowalbum = 0;}
	
	add_border(nowalbum);
</script>
<?php
	endif;
}
?>