=== PicasaWeb ===
Contributors: zhiqiang
Donate link: 
Tags: picasa,picasaweb
Requires at least: 2.3
Tested up to: 2.3.3
Stable tag: trunk

This plugin let you browse photos of your Picasa Web Albums in your own blog.

== Description ==

Before trying out this plugin, please take a looking at DEMO: http://zhiqiang.org/blog/photo.html

This plugin uses PicasaWeb API to retrieval your albums information, cache them (but the photos themselves aren't cached), display them within blog. It looks like your own gallery service.

= Features:=

* information are cached. very fast to navigate
* image preload, fast to navigate next photo.
* can use keyboard shortcut

= Notice =
* This plugin is not for displaying album in post and page. It's different from lots of similar plugins.
* PHP 5 and higher is required (most web hostor has it)
* mod_rewrite is required and you must use permalink.
* You might need to do lots of work to customize this plugin to fit your own template. It's not recommanded if you don't know any knowledge about HTML and PHP.

== Installation ==
= Installation instruction =

1. Upload `picasaweb` fold to the `/wp-content/plugins/` directory
1. Activate the plugin `picasaweb` through the 'Plugins' menu in WordPress
1. create a directory named `cache` in the diretory `wp-content`, and make it writable by your webserver (chmod 777 will do the trick).
1. setup the plugin under administation panel `options/picasaweb` (check option configurations section below).
1. check `http://yourblog/photo.html`


= option configurations =

There are four parameters you can configure under administator panel `options/picasaweb`:

1. username - The username of your picasa web albums. Password is not needed. So this plugin only display your public albums.
2. photo height- The height of your photos displayed. Customize it to fit your template.
3. url - Customize the url to browse your photos. 
4. Cache Directory - the cache diretory of your albums information. make sure the diretory is writable.

= uninstall instruction ==

1. deactivate the plugin or remove the plugin files.

=  =

As said in description section, this plugin need to do a lot of work to fit into your template.

* You must customize the display structure at `picasaweb/picasa.module.php`

== Frequently Asked Questions ==

none

== Screenshots ==

1. This is the first screen shot
2. This is the second screen shot
3. This is the third screen shot