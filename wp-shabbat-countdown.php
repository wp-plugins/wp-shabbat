<?php
 // add countdown html

$GLOBALS['diff'] = $diff;
$GLOBALS['dayDetails'] = $dayDetails;
function wp_shabbat_countdown() {
	/*wp_enqueue_script( 'wp-shabbat-countdown', plugin_dir_path( __FILE__ ) . 'js/countdown.js', array(), '1.0.0', true );
	wp_enqueue_script( 'wp_shabbat_countdown_js',plugins_url( 'js/countdown.js', __file__ ),  true );
	
	$dataToBePassed = array(
	'seconds'            => 10,//($enterTime -$user_time),
	'minutes'            => '20',//($enterTime -$user_time),
	);
	wp_localize_script( 'wp_shabbat_countdown_js', 'parse_vars', $datatoBePassed );
	
	//wp_enqueue_style( 'wp_shabbat_countdown_header', plugin_dir_path( __FILE__ ) . 'css/wp-shabbat-countdown.css' );	*/
	wp_enqueue_style( 'wp_shabbat_countdown', plugins_url( 'css/wp-shabbat-countdown.css', __file__ ) );	
	
	
	$seconds = $GLOBALS['diff'];
	 
	echo	'<div id="countdown_time">'.__('site will close due to ','WP-Shabbat').$GLOBALS['dayDetails'].__(' in','WP-Shabbat').'
				<div id="javascript_countdown_time"></div>
			</div>	
	<script type="text/javascript">
		
	var javascript_countdown = function () {
	var time_left = 10; //number of seconds for countdown
	var output_element_id = "javascript_countdown_time";
	var keep_counting = 1;
	var no_time_left_message = "'.__('The site is now closed for browsing','WP-Shabbat').'";
 
	function countdown() {
		if(time_left < 2) {
			keep_counting = 0;
		}
 
		time_left = time_left - 1;
	}
 
	function add_leading_zero(n) {
		if(n.toString().length < 2) {
			return "0" + n;
		} else {
			return n;
		}
	}
 
	function format_output() {
		var hours, minutes, seconds;
		seconds = time_left % 60;
		minutes = Math.floor(time_left / 60) % 60;
		hours = Math.floor(time_left / 3600);
 
		seconds = add_leading_zero( seconds );
		minutes = add_leading_zero( minutes );
		hours = add_leading_zero( hours );
 
		return hours + ":" + minutes + ":" + seconds;
	}
 
	function show_time_left() {
		document.getElementById(output_element_id).innerHTML = format_output();//time_left;
	}
 
	function no_time_left() {
		document.getElementById(output_element_id).innerHTML = no_time_left_message;
	}
 
	return {
		count: function () {
			countdown();
			show_time_left();
		},
		timer: function () {
			javascript_countdown.count();
 
			if(keep_counting) {
				setTimeout("javascript_countdown.timer();", 1000);
			} else {
				no_time_left();
			}
		},
		//Kristian Messer requested recalculation of time that is left
		setTimeLeft: function (t) {
			time_left = t;
			if(keep_counting == 0) {
				javascript_countdown.timer();
			}
		},
		init: function (t, element_id) {
			time_left = t;
			output_element_id = element_id;
			javascript_countdown.timer();
		}
	};
}();
 
//time to countdown in seconds, and element ID
javascript_countdown.init('.$seconds.', "javascript_countdown_time");

	
	</script>';
	

}   

add_action('wp_footer', 'wp_shabbat_countdown');


?>