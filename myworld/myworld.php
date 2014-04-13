<?php
/*
Plugin Name: MyWorld
Plugin URI: http://veltzer.net/
Description: MyWorld plugin for WordPress
Version: 0.0.2
Author: Mark Veltzer <mark.veltzer@gmail.com> 
Author URI: http://www.veltzer.net 
*/

/*
 * Handy function to include files once from my own plugin directory
 */
function my_include($file) {
	include_once(plugin_dir_path(__FILE__).$file);
}

function link_to_direct($direct_page) {
	return plugins_url('/sa/'.$direct_page, __FILE__);
}

function link_to_resource($resource) {
	return plugins_url('/resources/'.$resource, __FILE__);
}

/* lets include all php files in 'src'... */
foreach(glob(plugin_dir_path(__FILE__).'src/*.php') as $filename) {
	include_once($filename);
}

if(!class_exists('MyWorld')) {
	class MyWorld { 
		/*
		 * Our own version number
		 */
		var $version = '0.0.2';
		/*
		 * The function that creates dynamic content
		 */
		function create_content($name,$params) {
			my_mysql_connect();
			$ret=call_user_func('create_'.$name,$params);
			my_mysql_disconnect();
			return $ret;
		}

		/*
		 * The function that hooks into WP to substitute content
		 */
		function the_content($content) {
			$pattern='/\[myworld:\s*(.+)\s*\]/';
			preg_match_all($pattern,$content,$tags);
			foreach( $tags[0] as $k=>$old_content ) {
				$full=$tags[1][$k];
				$extra_array=preg_split('/,/',$full);
				$extra_hash=array();
				// the first is always the name of the content
				$name=trim(array_shift($extra_array));
				foreach ($extra_array as $val) {
					$pair=preg_split('/=/',$val);	
					$extra_hash[trim($pair[0])]=trim($pair[1]);
				}
				$new_content=$this->create_content($name,$extra_hash);
				$content=str_replace($old_content,$new_content,$content);
			}
			return $content;
		}

		/*
		 * This function will adds any javascript code that I need and also my own.
		 * It addition it handles style sheets and favicon stuff.
		 *
		 * Notes:
		 * - My javascript code and css MUST come at the end since
		 * they override any other stuff (especially the css...)
		 * - This next line is supposed to work but it doesn't...
		 * wp_enqueue_script('myworld', plugins_url('javascript/myworld_jquery.js', __FILE__), array('jquery'), '1.0');
		 * That is why the next section uses direct inclusion instead.
		 */
		function wp_head() {
			/*
			 * Third party javascript libs
			 */
			// jquery - need that for the accordion stuff
			echo "<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js'></script>\n";
			// jquery ui
			//echo "<link rel='stylesheet' href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/ui-lightness/jquery-ui.css'/>\n";
			//echo "<script src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js'></script>\n";
			// dojo
			echo "<script src='https://ajax.googleapis.com/ajax/libs/dojo/1.9.1/dojo/dojo.js'></script>";
			// full calendar stuff
			//echo "<link rel='stylesheet' href='/public/fullcalendar/fullcalendar.css'/>\n";
			//echo "<script src='/public/fullcalendar/fullcalendar.min.js'></script>\n";
			//echo "<script src='/public/fullcalendar/gcal.js'></script>\n";
			// highcharts stuff
			//echo "<script src='/public/highcharts/highcharts.js'></script>\n";
			// Ext4(sencha) stuff - need that for the movies tab
			echo "<link rel='stylesheet' href='http://cdn.sencha.io/ext/gpl/4.2.1/resources/css/ext-all.css'/>\n";
			echo "<script src='http://cdn.sencha.io/ext/gpl/4.2.1/ext-all.js'></script>\n";
			// this line is to remove the nocss that extjs does on the whole page
			echo "<script>Ext.onReady(function() { Ext.getBody().removeCls('x-body'); });</script>\n";

			// our own javascripts
			echo "<script src='/public/myworld_utils.js'></script>\n";
			echo "<script src='/public/myworld_extjs.js'></script>\n";
			echo "<script src='/public/myworld_dojo.js'></script>\n";
			echo "<script src='/public/myworld_jquery.js'></script>\n";
			// and now for the style sheet...
			echo "<link rel='stylesheet' href='".plugins_url('css/myworld.css?ver=',__FILE__).$this->version."'/>\n";
			// favicon
			//static version
			//echo "<link rel='SHORTCUT ICON' type='image/x-icon' href='".plugins_url('resources/favicon.ico',__FILE__)."'/>\n";
			//dynamic version
			echo "<link rel='SHORTCUT ICON' type='image/x-icon' href='".plugins_url('resources/favicon.ico',__FILE__)."?t='".time()."'/>\n";
		}

		function MyWorld() {
			add_action('wp_head',array(&$this, 'wp_head'));
			add_filter('the_content',array(&$this, 'the_content'),0);
		}
	}
	// now create an instance of the class...
	global $MyWorld_instance;
	$MyWorld_instance=&new MyWorld();
}
?>
