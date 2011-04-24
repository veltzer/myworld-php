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
	return WP_PLUGIN_URL.'/myworld/sa/'.$direct_page;
}

function link_to_resource($resource) {
	return WP_PLUGIN_URL.'/myworld/resources/'.$resource;
}

my_include('include/utils.php');
my_include('src/accordion.php');
my_include('src/audio.php');
my_include('src/business.php');
my_include('src/calendar.php');
my_include('src/helloworld.php');
my_include('src/lilypond.php');
my_include('src/music.php');
my_include('src/table.php');
my_include('src/works.php');

if(!class_exists('MyWorld')) {
	class MyWorld { 
		/*
		 * Our own version number
		 */
		var $version = '0.0.1';
		/*
		 * The function that creates dynamic content
		 */
		function create_content($name,$extra) {
			my_mysql_connect();
			switch($name) {
				case 'person':
					// TODO: throw error if parameters are not there...
					$firstname=$extra['firstname'];
					$surname=$extra['surname'];
					$ret=create_person($firstname,$surname);
					break;
				case 'courses':
					$ret=create_courses();
					break;
				case 'consulting':
					$ret=create_consulting();
					break;
				case 'teaching':
					$ret=create_teaching();
					break;
				case 'certification':
					$ret=create_certification();
					break;
				case 'works':
					$type=$extra['type'];
					$ret=create_works($type);
					break;
				case 'stats':
					$ret=create_stats();
					break;
				case 'lilypond':
					$ret=create_lilypond();
					break;
				case 'music':
					$ret=create_music();
					break;
				case 'calendar':
					$ret=create_calendar();
					break;
				case 'embed_ted':
					$ret=embed_ted($extra['id']);
					break;
				case 'helloworld':
					$ret=create_helloworld();
					break;
				case 'echo':
					$ret=$extra;
					break;
				case 'test':
					$ret='שלום';
					break;
				default:
					$ret='[$name] is unknown';
					break;
			}
			#$ret='<br/>$ret<br/>';
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
		 * wp_enqueue_script('myworld', plugins_url('javascript/myworld.js', __FILE__), array('jquery'), '1.0');
		 * That is why the next section uses direct inclusion instead.
		 */
		function wp_head() {
			// jquery
			echo "<script type='text/javascript' src='".plugins_url('javascript/jquery/jquery.js?ver=',__FILE__).$this->version."'></script>\n";
			// jquery ui
			echo "<script type='text/javascript' src='".plugins_url('javascript/jquery/jquery-ui-custom.js?ver=',__FILE__).$this->version."'></script>\n";

			// full calendar stuff
			echo "<link rel='stylesheet' id='fullcalendar-css' href='".plugins_url('javascript/fullcalendar/fullcalendar.css?ver=',__FILE__).$this->version."' type='text/css' media='screen' />\n";
			echo "<script type='text/javascript' src='".plugins_url('javascript/fullcalendar/fullcalendar.min.js?ver=',__FILE__).$this->version."'></script>\n";
			echo "<script type='text/javascript' src='".plugins_url('javascript/fullcalendar/gcal.js?ver=',__FILE__).$this->version."'></script>\n";
			// high charts stuff
			echo "<script type='text/javascript' src='".plugins_url('javascript/highcharts/highcharts.js?ver=',__FILE__).$this->version."'></script>\n";

			// myworld javascript
			echo "<script type='text/javascript' src='".plugins_url('javascript/myworld.js?ver=',__FILE__).$this->version."'></script>\n";
			// and now for the style sheet...
			echo "<link rel='stylesheet' id='myworld-css' href='".plugins_url('css/myworld.css?ver=',__FILE__).$this->version."' type='text/css' media='screen' />\n";
			// favicon
			echo "<link rel='SHORTCUT ICON' type='image/x-icon' href='".plugins_url('resources/favicon.ico',__FILE__)."' />\n";
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
