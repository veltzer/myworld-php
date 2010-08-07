<?php
/*
Plugin Name: MyWorld
Plugin URI: http://veltzer.net/
Description: MyWorld plugin for WordPress
Version: 0.0.1
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
	return WP_PLUGIN_URL."/myworld/sa/".$direct_page;
}

function link_to_resource($resource) {
	return WP_PLUGIN_URL."/myworld/resources/".$resource;
}

my_include("include/db.php");
my_include("include/na.php");
my_include("include/utils.php");
my_include("src/business.php");
my_include("src/works.php");
my_include("src/lilypond.php");

if(!class_exists('MyWorld')) {
	class MyWorld { 
		/*
		 * Our own version number
		 */
		var $version = "0.0.1";
		/*
		 * The function that creates dynamic content
		 */
		function create_content($what) {
			db_connect();
			switch($what) {
				case "courses":
					$ret=create_courses();
					break;
				case "consulting":
					$ret=create_consulting();
					break;
				case "teaching":
					$ret=create_teaching();
					break;
				case "certification":
					$ret=create_certification();
					break;
				case "works":
					$ret=create_works();
					break;
				case "lilypond":
					$ret=create_lilypond();
					break;
				case "test":
					$ret="שלום";
					break;
				default:
					$ret="[$what] is unknown";
					break;
			}
			#$ret="<br/>$ret<br/>";
			db_disconnect();
			return $ret;
		}

		/*
		 * The function that hooks into WP to substitute content
		 */
		function the_content($content) {
			$pattern = "/\[myworld:\s*([^\]]+)\s*\]/";
			preg_match_all( $pattern, $content, $tags );
			foreach( $tags[0] as $k=>$cnt ) {
				$content=str_replace($cnt,$this->create_content($tags[1][$k]),$content);
			}
			return $content;
		}

		/*
		 * This function will add my javascript code
		 */
		function wp_head() {
			// This next line is supposed to work but it doesn't...
			//wp_enqueue_script('mysupport', plugins_url('javascript/mysupport.js', __FILE__), array('jquery'), '1.0');
			// I used direct inclusion like below...
			echo "<script type='text/javascript' src='".plugins_url('javascript/mysupport.js?ver=',__FILE__).$this->version."'></script>'";
			// and now for the style sheet...
			echo "<link rel='stylesheet' id='myworld-css' href='".plugins_url('css/mystyle.css?ver=',__FILE__).$this->version."' type='text/css' media='screen' />";
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
