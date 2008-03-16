// Modified by Zhiqiang http://zhiqiang.org/blog/posts/local-mirror-of-picasaweb-using-api.html
// $Update: Dec 23, 2007$

// alias for document.write
function _$(a) {
    document.write(a);
}
// alias for document.getElementById
function _e(a) {
	return document.getElementById(a);	
}

// parse URL
function readGet() {
    var _GET = new Array();
    var uriStr = window.location.href.replace(/&amp;/g, '&');
    var paraArr, paraSplit;
    if (uriStr.indexOf('?') > -1) {
        var uriArr = uriStr.split('?');
        var paraStr = uriArr[1];
    } else {
        return _GET;
    }
    if (paraStr.indexOf('&') > -1) {
        paraArr = paraStr.split('&');
    } else {
        paraArr = new Array(paraStr);
    }
    for (var i = 0; i < paraArr.length; i ++ ) {
        paraArr[i] = paraArr[i].indexOf('=') > -1 ? paraArr[i]: paraArr[i] + '=';
        paraSplit = paraArr[i].split('=');
        _GET[paraSplit[0]] = decodeURI(paraSplit[1].replace(/\+/g, ' '));
    }
    return _GET;
}
var _GET = readGet();


//Global variables
var photolist = new Array();
//this is used globally to store the entire list of photos in a given album, rather than pass the list around in a URL (which was getting rediculously long as a result)
var album_name = "";
//this is used globally to store the album name, so we don't have to pass it around in the URL anymore either.
var my_numpics = "";
//this is used globally to store the number of items in a particular album, so we don't have to pass it around in the URL anymore either.
var prev = "";
//used in the navigation arrows when viewing a single item
var next = "";
//used in the navigation arrows when viewing a single item
var albumfeed;
//used in globally to store the feed of album list
var photosfeed;
//used in globally to store the feed of photo list in one specific album
var np = 0;
//the photo index now
var total;
var photosize;
if ( ! photosize)
    photosize = 800;
var smallsize = "?imgmax=160";
var bigsize = "?imgmax=" + photosize;
var max_width = 800;
var max_height = 600;
if (document.body.clientWidth < 1100) max_height=535;

//returns the list of all albums for the user
function picasaweb(j) {
    albumfeed = j;
    
    _$("<h3 style='margin-left:3px'>相册首页</h3><!--<div style='text-align:right; margin-right:5px; margin-top:-14px'><a target=PICASA class='standard' href='http://picasaweb.google.com/" + username + "/'>View this gallery in Picasa</a></div>-->");
    _$("<div id='albumlist'>");
    
    for (i = 0; i < j.feed.entry.length; i ++ ) {
        var img_base = j.feed.entry[i].media$group.media$content[0].url;
        
        var id_begin = j.feed.entry[i].id.$t.indexOf('albumid/') + 8;
        var id_end = j.feed.entry[i].id.$t.indexOf('?');
        var id_base = j.feed.entry[i].id.$t.slice(id_begin, id_end);
        var img_update = j.feed.entry[i].published.$t.slice(0, 10);
        
        _$("<div class='album'><div class='photobigbox'><div class='photosmallbox'><a class='standard' href='?albumid=" + id_base + "'><img src='" + img_base + "?imgmax=160&crop=1' class='pwimages' /></a></div></div>");
        _$("<div class='albumtitle'><a class='title' class='standard' href='?albumid=" + id_base + "'>" + j.feed.entry[i].title.$t + "</a><div class='grey'>" + img_update + "</div></div></div>");
    }
    _$("</div>");
    
}

//returns all photos in a specific album
//get the number of photos in the album
function albums(j) {
    photosfeed = j;
    
    var my_numpics = j.feed.openSearch$totalResults.$t;
    
    var album_begin = j.feed.entry[0].summary.$t.indexOf('href="') + 6;
    var album_end = j.feed.entry[0].summary.$t.indexOf('/photo#');
    var album_link = j.feed.entry[0].summary.$t.slice(album_begin, album_end);
    
    _$("<h3 style='margin-left:3px'><a class='standard' href='" + window.location.protocol + "//" + window.location.hostname + window.location.pathname + "'>相册首页</a> &gt; " + j.feed.title.$t + "&nbsp;&nbsp;[共" + my_numpics + " 张照片" + "]</h3><!--<div style='text-align:right; margin-right:5px; margin-top:-14px'><a target=PICASA class='standard' href='" + album_link + "'>View this album in Picasa</a></div>-->");
    _$("<div id='photolist'>");
    
    for (i = 0; i < j.feed.entry.length; i ++ ) {
        var img_base = j.feed.entry[i].media$group.media$content[0].url;
        var link_url = "?albumid=" + _GET['albumid'];
        var width = parseInt(j.feed.entry[i].media$group.media$content[0].width);
        var height = parseInt(j.feed.entry[i].media$group.media$content[0].height);
        var margin = 0, img_width = 160, img_height = 160;
        if (width > height)  
			img_height 	= Math.floor(160*height/width);
		else
			img_width 	= Math.floor(160*width/height);	
        margin = Math.floor(80 - img_height/2);
        
        _$("<div class='smallpicture'><div class='picturebox' ><a href='" + link_url + "&np=" + i + "'><img  style='margin:" + margin + "px 0;width:"+img_width+"px;height:"+img_height+"px;' src='" + img_base + "?imgmax=160' class='pwimages'/></a></div></div>");
        //_$("<p><center><SPAN STYLE='font-size: 9px'>"+j.feed.entry[i].media$group.media$description.$t+"</span></center>");
        
    }
    _$("</div>");
    
}

function getphotolist(j) {
    np = parseInt(_GET['np']);
    photosfeed = j;
    
    my_numpics = j.feed.openSearch$totalResults.$t;
    album_name = j.feed.title.$t;
    photo(photosfeed.feed.entry[np]);
    var np1 = np + 1;
    if (np1 > my_numpics - 1)
        np1 = 0;
    var t1 = photosfeed.feed.entry[np1];
    var img_link1 = t1.content.src;
    preloadImg(img_link1 + bigsize);
}

function photo(j) {
    var img_title = j.title.$t;
    var img_width = j.media$group.media$content[0].width;
    var img_height = j.media$group.media$content[0].height;
    var img_base = j.media$group.media$content[0].url;
    
    var album_id = _GET['albumid'];
    var current_indexster = "<span id='picindex'>" + (np + 1) + "</span>";
    var current_index_text = current_indexster + " of " + my_numpics;
    
    _$("<h3 style='margin-left:3px'><a class='standard' href='?'>相册首页</a> &gt; <a class='standard' href='?albumid=" + album_id + "'>" + album_name + "</a> &gt; " + current_index_text + "</h3>");
    
    _$("<table  style='margin:0 auto;border:1px solid #ccc;width:99%'><tr valign=top><td width='20%' align='left'><a href='?albumid=" + album_id + "'>«View Album</a></td>");
    
    _$("<td width='30%' align='right'><a onclick='previousImg();return false;' href='javascript:;'  ><img border=0 alt='Previous item' src='/images/left.gif'></a></td>");
    _$("<td> </td>");
    _$("<td width='30%' align='left'> <a  onclick='nextImg();return false;' href='javascript:;'><img border=0 alt='Next item' src='/images/right.gif'></a></td>");
    _$("<td width='20%' align='right'><a id='zoom' href='" + img_base + "' target='_blank' title='察看原图'><img src='/images/zoom_normal.gif' alt='view original'/></a></td></tr></table>");
    
    var display_height = max_height;
    if (img_height < display_height) {
        display_height = img_height;
    }
    _$("<div id='picbox' style='text-align:center'>");
    _$("<span id='imgdes' style='margin-left:2px'>" + j.media$group.media$description.$t + "</span>");
    _$("<p><a border=0 href='javascript:;' onclick='nextImg();return false;' title='点击显示下一张'><img id='picture' height=" + display_height + " src='" + img_base + bigsize + "' class='pwimages' /></a></p></div>");
    
    // add keyboard shortcut
    _$('<script language="Javascript"> function testKeyCode( evt, intKeyCode ) { if ( window.createPopup ) return evt.keyCode == intKeyCode; else return evt.which == intKeyCode; } document.onkeydown = function ( evt ) { if ( evt == null ) evt = event; if ( testKeyCode( evt, 37 ) ) { previousImg(); } if ( testKeyCode( evt, 39 ) ) { nextImg();return false; } } </script>');
}


function browsePhoto(j) {
    if (_GET['np'] && _GET['albumid'])
        getphotolist(j);
    else albums(j);
}

if (_GET['albumid']) {
    _$('<script type="text/javascript" src="http://picasaweb.google.com/data/feed/base/user/' + username + '/albumid/' + _GET['albumid'] + '?category=photo&alt=json&callback=browsePhoto"></script>');
    //photo
    
} else {
    _$('<script type="text/javascript" src="http://picasaweb.google.com/data/feed/base/user/' + username + '?category=album&alt=json&callback=picasaweb&access=public"></script>');
    //picasaweb
    
}


// this function display album and photo information.
function albumhead() {
    if (_GET['albumid']) {
        _$("<div class='photobigbox'><div class='photosmallbox'><img src='" + photosfeed.feed.icon.$t + "' class='pwimages' /></div></div>");
        _$("<h4>" + photosfeed.feed.title.$t + "(" + photosfeed.feed.openSearch$totalResults.$t + ")</h4>");
        _$("<span>" + photosfeed.feed.subtitle.$t + "</span>");
        _$("<p class='grey'>Last update at " + photosfeed.feed.updated.$t.slice(0, 10) + "</p>");
        if (_GET['np']) {
            np1 = np + 1;
            if (np1 > my_numpics - 1)
                np1 = 0;
            t1 = photosfeed.feed.entry[np1];
            var img_link1 = t1.content.src;
            _$('<h4>预览下(上)一张</h4>');
            _$('<center><a href="javascript:nextImg();" title="下(上)一张"><img id="next" src="' + img_link1 + '?imgmax=320" style="width:160px;"/></a></center>');
        }
    } else {
        _$("<div><img src='" + albumfeed.feed.icon.$t + "' class='pwimages' /></div>");
        _$("<h4>" + albumfeed.feed.title.$t + "(" + albumfeed.feed.openSearch$totalResults.$t + ")</h4>");
        _$("<span>" + albumfeed.feed.subtitle.$t + "</span>");
        _$("<p class='grey'>Last update at " + albumfeed.feed.updated.$t.slice(0, 10) + "</p>");
    }
}

// preload img to display
var img = new Image();
function preloadImg(img1) {
    img.src = img1;
}

function nextImg() {
    np = np + 1;
    if (np > my_numpics - 1)
        np = 0;
    var t = photosfeed.feed.entry[np];
    var img_link = t.content.src;
    var np1 = np + 1;
    if (np1 > my_numpics - 1)
        np1 = 0;
    var t1 = photosfeed.feed.entry[np1];
    var img_link1 = t1.content.src;
    _e("picture").src = img_link + smallsize;
    //if(img.src==img_link+bigsize && img.readyState=="complete") 
    if(_e("zoom")) _e("zoom").href = img_link;
    _e("picture").src = img_link + bigsize;
    _e("imgdes").innerHTML = t.media$group.media$description.$t;
    _e("picindex").innerHTML = np + 1;
    if(_e("next")) _e("next").src = img_link1 + smallsize;
    preloadImg(img_link1 + bigsize);
    
}

function previousImg() {
    np = np - 1;
    if (np < 0)
        np = my_numpics - 1;
    var t = photosfeed.feed.entry[np];
    var img_link = t.content.src;
    
    var np1 = np - 1;
     
    if (np1 < 0)
        np1 = my_numpics - 1;
    var t1 = photosfeed.feed.entry[np1];
    var img_link1 = t1.content.src;
    _e("picture").src = img_link + smallsize;
    //if(img.src==img_link+bigsize && img.readyState=="complete") 
    if(_e("zoom"))_e("zoom").href = img_link;
    _e("picture").src = img_link + bigsize;
    _e("imgdes").innerHTML = t.media$group.media$description.$t;
    _e("picindex").innerHTML = np + 1;
    if(_e("next")) _e("next").src = img_link1 + smallsize;
    preloadImg(img_link1 + bigsize);
}