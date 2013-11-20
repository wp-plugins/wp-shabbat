<?php

function wp_shabbat_closed_title() {
  $return = ' ';
  return $return;
}
function wp_shabbat_closed_content() {
	if ($_GET['WP-Shabbat'] == "Shabbat-Closed-Page"){
		$content =  '<center><h1>'.__('We Dont Work Today','WP-Shabbat').'</h1></center>
					 <center><h3>'.__('Today is : ','WP-Shabbat'). $_GET['redirectReason'] .'</h3></center><br/>
					 <center><h3>'.__('Please come back ','WP-Shabbat'). $_GET['opentime'] .'</h3><center>';
   }
   return $content;
}
function wp_shabbat_closed_template() {
  include(TEMPLATEPATH."/index.php");
  exit;
}
if ($_GET['WP-Shabbat'] == "Shabbat-Closed-Page") {
  add_filter('the_title','wp_shabbat_closed_title');
  add_filter('the_content','wp_shabbat_closed_content');
  add_action('template_redirect', 'wp_shabbat_closed_template');
}
?>