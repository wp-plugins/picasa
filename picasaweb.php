<?php
/*
Plugin Name: PicasaWeb
Plugin URI: http://zhiqiang.org/blog/plugin/picasa
Version: 1.34
Description: Display your Picasa Web Albums in your own site. DEMO: http://zhiqiang.org/blog/photo.html
Author: zhiqiang
Author URI: http://zhiqiang.org/blog/
*/


if (get_option("picasa_username")==''):
	update_option("picasa_queryvar", "photo");
	update_option("picasa_cache_path", "/wp-content/cache/");
	update_option("picasa_maxheight", "480");
	add_action("init", picasa_flush_rewrite_rules);
endif;

define('PICASA_USERNAME', get_option("picasa_username"));  	// picasaweb user name
define('PICASA_QUERYVAR', get_option("picasa_queryvar")?get_option("picasa_queryvar"):"photo");	// url
define('PICASA_CACHE_PATH', get_option("picasa_cache_path")?get_option("picasa_cache_path"):"/wp-content/cache/"); 
// cache directory, keep it writable

define('PICASA_TAGURL', 'albumid');								// URL to use when querying tags


function is_keyword() {
    global $wp_version;
    $keyword = ( isset($wp_version) && ($wp_version >= 2.0) ) ? 
                get_query_var("zqp") : 
                $GLOBALS["zqp"];
	//		var_dump($keyword);
	if ($keyword != "")
		return true;
	else
		return false;
}

/***** Add actions *****/

/* for album id queries */
add_filter('query_vars', 'PICASA_addQueryVar');
add_action('parse_query', 'PICASA_parseQuery');

function PICASA_addQueryVar($wpvar_array) {
	$wpvar_array[] = PICASA_QUERYVAR;
	$wpvar_array[] = "zqp";
	return($wpvar_array);
}

function PICASA_parseQuery() {
	// if this is a keyword query, then reset other is_x flags and add query filters
	if (is_keyword()) {
		global $wp_query;
		$wp_query->is_single = false;
		$wp_query->is_page = false;
		$wp_query->is_archive = false;
		$wp_query->is_search = false;
		$wp_query->is_home = false;
		
		// add_filter('posts_where', 'PICASA_postsWhere');
		// add_filter('posts_join', 'PICASA_postsJoin');
		add_action('template_redirect', 'PICASA_includeTemplate');
	}
}


/***** Add actions *****/

/* for URL rewrite */
add_action('generate_rewrite_rules', 'PICASA_createRewriteRules');

function PICASA_includeTemplate() {

	if (is_keyword()) {
		$template = ABSPATH . "/wp-content/plugins/picasa/picasa.module.php";
		if ($template) {
			load_template($template);
			exit;
		}
	}
	return;
}

function PICASA_createRewriteRules($wp_rewrite) {
  $new_rules = array( get_option("picasa_queryvar") . '/(.*).html' => 'index.php?zqp=something&'. get_option("picasa_queryvar") .'=' .
       $wp_rewrite->preg_index(1), 
	   get_option("picasa_queryvar").".html"  => 'index.php?zqp=something' 
	);
  $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
  
//var_dump($wp_rewrite);
}

function picasa_flush_rewrite_rules() 
{
   global $wp_rewrite;
   $wp_rewrite->flush_rules();
}


// picasaweb administrator
add_action('admin_menu', 'stats_menu');
function stats_menu() {
	if (function_exists('add_submenu_page')) {
		add_options_page('Picasaweb administrator',  'Picasaweb', 1, 'picasa/picasa.admin.php');
	}
}
?>